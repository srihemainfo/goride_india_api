<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Log;

class CompressImage extends Command
{
    protected $signature = 'compress:image {--limit=2}';
    protected $description = 'Generate thumbnails for KYC selfie images';

    public function handle()
    {
        // Increase limits to avoid premature termination
        set_time_limit(300); // 5 minutes per run
        ini_set('memory_limit', '512M');

        $limit = (int) $this->option('limit');

        $kycRecords = DB::table('kyc_details')
            ->whereNotNull('selfie_url')
            ->whereNull('tumbnail_selfie_url')
            ->limit($limit)
            ->get();

        $total = $kycRecords->count();
        $this->info("Found {$total} record(s) to process.");
        Log::channel('daily')->info("CompressImage started. Found {$total} records.");

        if ($total === 0) {
            $this->info('No records to process.');
            return 0;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($kycRecords as $index => $kyc) {
            DB::beginTransaction();
            $tempPath = null;
            $uploadedS3Path = null; // Track S3 upload for potential rollback

            try {
                $this->line("\n Processing KYC ID: {$kyc->id}");
                Log::info("KYC {$kyc->id} - started");

                $selfieUrl = $kyc->selfie_url;

                // Check if URL is accessible
                if (!filter_var($selfieUrl, FILTER_VALIDATE_URL)) {
                    throw new \Exception("Invalid URL: {$selfieUrl}");
                }

                $originalImage = DB::table('s3_images')
                    ->where('s3_url', $selfieUrl)
                    ->first();

                if ($originalImage) {
                    Log::info("KYC {$kyc->id} - getting from S3 path: {$originalImage->s3_path}");
                    $imageContent = Storage::disk('s3')->get($originalImage->s3_path);
                    if (!$imageContent) {
                        throw new \Exception("Failed to get image content from S3: {$originalImage->s3_path}");
                    }
                } else {
                    Log::info("KYC {$kyc->id} - downloading from URL: {$selfieUrl}");
                    $response = Http::timeout(60)->get($selfieUrl);
                    if (!$response->successful()) {
                        throw new \Exception("Failed to download image. HTTP status: " . $response->status());
                    }
                    $imageContent = $response->body();
                }

                // Write to temp file
                $tempPath = tempnam(sys_get_temp_dir(), 'thumb_') . '.jpg';
                file_put_contents($tempPath, $imageContent);
                Log::info("KYC {$kyc->id} - temp file created: {$tempPath}");

                // Load image
                try {
                    $img = Image::make($tempPath);
                    $img->orientate();
                } catch (\Exception $e) {
                    throw new \Exception("Image processing failed: " . $e->getMessage());
                }

                // Resize first if image is very large to save memory
                $width = $img->width();
                $height = $img->height();
                if ($width > 2000 || $height > 2000) {
                    $img->resize(2000, 2000, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    Log::info("KYC {$kyc->id} - resized to max 2000px to reduce memory");
                }

                $quality = 70;
                $maxSize = 50000;
                $size = 0;

                do {
                    $img->encode('jpg', $quality);
                    $size = strlen($img->getEncoded());
                    if ($size <= $maxSize) {
                        break;
                    }
                    $quality -= 5;
                    if ($quality < 10) {
                        $img->resize(800, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                        $quality = 70;
                    }
                } while ($quality >= 10);

                $thumbnailContent = $img->getEncoded();
                Log::info("KYC {$kyc->id} - final thumbnail size: {$size} bytes");

                $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($originalImage->name ?? pathinfo($selfieUrl, PATHINFO_FILENAME) ?? 'thumb'));
                $fileName = uniqid($safeName . '_thumb_') . '.jpg';
                $path = 'cus_app/images/thumbnails/' . $fileName;

                // Upload
                $uploaded = Storage::disk('s3')->put($path, $thumbnailContent);
                if (!$uploaded) {
                    throw new \Exception("Failed to upload thumbnail to S3: {$path}");
                }
                
                // Track the uploaded path so we can delete it if subsequent steps fail
                $uploadedS3Path = $path;
                Log::info("KYC {$kyc->id} - uploaded to S3: {$path}");

                // Try to set visibility, but don't fail if it's not allowed
                try {
                    Storage::disk('s3')->setVisibility($path, 'public');
                } catch (\Exception $e) {
                    Log::warning("KYC {$kyc->id} - setVisibility failed: " . $e->getMessage());
                    // File may still be accessible via bucket policy
                }

                // This step could potentially fail or throw an exception depending on the S3 driver configuration
                $url = Storage::disk('s3')->url($path);

                $thumbId = DB::table('s3_image_thumbnails')->insertGetId([
                    'original_image_id' => $originalImage ? $originalImage->id : null,
                    'img_type'          => $originalImage ? $originalImage->img_type : 'kyc_selfie',
                    'name'              => $originalImage ? $originalImage->name : $safeName,
                    's3_path'           => $path,
                    's3_url'            => $url,
                    'mime_type'         => 'image/jpeg',
                    'size'              => $size,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                DB::table('kyc_details')
                    ->where('id', $kyc->id)
                    ->update(['tumbnail_selfie_url' => $url]);

                DB::commit();
                $this->info(" Processed KYC ID: {$kyc->id}, Thumbnail ID: {$thumbId}");
                Log::info("KYC {$kyc->id} - completed successfully");

            } catch (\Exception $e) {
                // Rollback database changes
                DB::rollBack();
                
                // Cleanup orphaned S3 file if it was uploaded before the crash
                if ($uploadedS3Path) {
                    try {
                        Storage::disk('s3')->delete($uploadedS3Path);
                        Log::info("KYC {$kyc->id} - S3 rollback successful. Deleted orphaned file: {$uploadedS3Path}");
                    } catch (\Exception $deleteEx) {
                        Log::error("KYC {$kyc->id} - CRITICAL: Failed to delete orphaned S3 file during rollback [{$uploadedS3Path}]. Error: " . $deleteEx->getMessage());
                    }
                }

                $this->error(" Error processing KYC ID {$kyc->id}: " . $e->getMessage());
                Log::error("CompressImage failed for KYC {$kyc->id}: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                // Continue with next record
            } finally {
                // Always clean up the local temporary file
                if ($tempPath && file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        return 0;
    }
}