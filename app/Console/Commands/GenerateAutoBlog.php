<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class GenerateAutoBlog extends Command
{
    // The terminal command to execute this script
    protected $signature = 'blog:fully-automate';
    
    protected $description = '100% Autonomous AI Content Engine: Generates text via OpenAI and images via Free AI service.';

    public function handle()
    {
        $this->info("Initializing Fully Autonomous Content Engine...");

        try {
            // 1. AI Brainstorms the Topic and Category
            $topicPrompt = "You are an SEO expert for an Indian ride-sharing app called 'GoRide'. Suggest a trending, highly clickable blog topic related to bike pooling, carpooling, saving fuel, or women's safety in transport. 
            Return ONLY a valid JSON object with two keys: 
            'category_name' (Short, max 3 words, e.g., 'Eco Travel', 'Women Safety') and 
            'specific_topic' (The exact title of the blog post).";

            $this->info("Brainstorming topic...");
            // Using OPEN_AI_KEY from your .env for Text
            $topicResponse = Http::withToken(env('OPEN_AI_KEY'))
                ->post(env('OPEN_AI_API', 'https://api.openai.com/v1/chat/completions'), [
                    'model' => 'gpt-4',
                    'messages' => [['role' => 'user', 'content' => $topicPrompt]],
                    'temperature' => 0.8,
                ])->json();

            $topicData = json_decode($topicResponse['choices'][0]['message']['content'], true);
            $categoryName = $topicData['category_name'];
            $blogTitle = $topicData['specific_topic'];

            $this->info("AI Selected Topic: " . $blogTitle);

            // 2. Dynamic Category Management
            $categorySlug = Str::slug($categoryName);
            $category = DB::table('categories')->where('cat_name', $categoryName)->first();

            if (!$category) {
                // Category doesn't exist, create it automatically
                $categoryId = DB::table('categories')->insertGetId([
                    'cat_name' => $categoryName,
                    'cat_url' => '/blog/' . $categorySlug,
                    'status' => 1,
                    'created_at' => Carbon::now(),
                ]);
                $this->info("Created new category: " . $categoryName);
            } else {
                $categoryId = $category->id;
                $this->info("Using existing category: " . $categoryName);
            }

            // 3. Generate the Actual Blog Content
            $this->info("Generating blog content...");
            $contentPrompt = "Write a comprehensive, SEO-optimized blog post titled '{$blogTitle}' for the GoRide app. 
            Return ONLY a valid JSON object with these keys: 
            'sub_title', 'description', 'content' (in raw HTML sections like <h2> and <p>), 'seo_title', 'meta_description', 'meta_keywords', and 'faq' (an array of question/answer objects).";

            $contentResponse = Http::withToken(env('OPEN_AI_KEY'))
                ->post(env('OPEN_AI_API', 'https://api.openai.com/v1/chat/completions'), [
                    'model' => 'gpt-4',
                    'messages' => [['role' => 'user', 'content' => $contentPrompt]],
                    'temperature' => 0.7,
                ])->json();

            $blogData = json_decode($contentResponse['choices'][0]['message']['content'], true);
            $blogSlug = Str::slug($blogTitle);

            // 4. Generate Image & Upload to AWS S3 (USING FREE AI IMAGE GENERATOR)
            $this->info("Requesting image from Free AI Generator (Pollinations.ai)...");
            
            // Create a clean prompt for the image
            $imagePrompt = "A professional high quality blog banner image for: " . $blogTitle . ". Indian modern city background, transportation, bright colors, no text, no words.";
            $encodedPrompt = urlencode($imagePrompt);
            
            // Pollinations.ai generates images via a simple GET request for free
            // width 1024, height 512 is great for blog banners
            $imageUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width=1024&height=512&nologo=true";

            // Download the image
            $imageContents = file_get_contents($imageUrl);
            
            if ($imageContents === false) {
                throw new \Exception("Failed to download image from the free generator.");
            }
            
            $this->info("Image successfully generated. Uploading to S3...");

            // Stream directly to your configured S3 bucket
            $imageName = 'custom_images/auto_blog_' . time() . '.jpg';
            Storage::disk('s3')->put($imageName, $imageContents, 'public');
            $s3Url = Storage::disk('s3')->url($imageName);
            
            $this->info("Image uploaded to S3 successfully.");

            // Calculate Read Minutes
            $wordCount = str_word_count(strip_tags($blogData['content']));
            $readMinutes = ceil($wordCount / 200);

            // 5. Insert and Publish LIVE Instantly into DB
            DB::table('blogs_content')->insert([
                'category_id' => $categoryId,
                'published_date' => Carbon::today(),
                'sub_title' => $blogData['sub_title'],
                'blog_title' => $blogTitle,
                'description' => $blogData['description'],
                'slug' => $blogSlug,
                'read_minutes' => $readMinutes,
                'thumbnail' => $s3Url, 
                'hero_image' => $s3Url,
                'faq' => json_encode($blogData['faq']),
                'content' => $blogData['content'],
                'seo_title' => $blogData['seo_title'],
                'meta_description' => $blogData['meta_description'],
                'meta_keywords' => $blogData['meta_keywords'],
                'status' => 1, // Instantly LIVE
                'created_at' => Carbon::now(),
            ]);

            $this->info("SUCCESS! Blog is now LIVE at: " . env('APP_URL') . "blog/{$categorySlug}/{$blogSlug}");

        } catch (\Exception $e) {
            $this->error("Automation Failed: " . $e->getMessage());
        }
    }
}