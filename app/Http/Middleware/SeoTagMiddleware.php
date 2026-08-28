<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class SeoTagMiddleware
{
    
    // public function normalizeUtf8($data)
    // {
    //     if (is_array($data)) {
    //         foreach ($data as $k => $v) {
    //             $data[$k] = normalizeUtf8($v);
    //         }
    //         return $data;
    //     }
    
    //     if (is_string($data)) {
    //         return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    //     }
    
    //     return $data;
    // }

    
    public function handle(Request $request, Closure $next)
    {
        $slug = $request->route('slug') ?: '';
        $firstSegment = $request->segment(1);
        $secondSegment = $request->segment(2);
        $thirdSegment = $request->segment(3);
        // dd($firstSegment, $sSegment);

        $routeName = Route::currentRouteName() ?: 'home';

        // $cacheKey = $slug != '' ? trim($request->path(), '/') : $routeName;
        $cacheKey = $request->path() != '' ? trim($request->path(), '/') : $routeName;
        $cacheKey = str_replace('/', '_', $cacheKey);
        // dd($cacheKey);

        $cacheDir = storage_path('seoMiddleware');
        if (!File::exists($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }

        $cachePath = $cacheDir . '/' . $cacheKey . '.json';

        if (File::exists($cachePath)) {
            $seoTags = json_decode(File::get($cachePath), true) ?? [];
            view()->share('seoTags', $seoTags);
            return $next($request);
        }

        $seoTags = [
            'id' => '',
            'metaTitle' => '',
            'metaDes' => '',
            'metaKeyword' => '',
            'canonical' => $request->fullUrl(),
            'shortNote' => '',
            'img' => '',
            'wikiDes' => '',
            'wikiDesHtml' => '',
            'listPages' => [],
            'blogPages' => [],
            'slug' => $slug,
            'kms' => '',
            'from' => '',
            'from_id' => '',
            'from_lat' => '',
            'from_lng' => '',
            'to' => '',
            'to_id' => '',
            'to_lat' => '',
            'to_lng' => '',
            'innerLinks' => [],
            'faqData' => [],
            'pop_routes' => [],
            'page_exist' => true
        ];

        $article = $routeName;

        $art_pages = DB::table('article')
            ->where('url', $article)
            ->where('status', 'Active')
            ->orderByDesc('id')
            ->limit(1)
            ->get();

        if ($art_pages->count() > 0) {
            $seoTags['id'] = $art_pages[0]->id;
            $seoTags['metaTitle'] = $art_pages[0]->meta_title;
            $seoTags['metaDes'] = $art_pages[0]->meta_desp;
            $seoTags['metaKeyword'] = $art_pages[0]->meta_keyword;
        }
        
        // dd($seoTags);

        if ($slug && !str_contains($request->path(), 'car-rental') && !str_contains($request->path(), 'taxi') && !str_contains($request->path(), 'blog') && isset( $slug) && $slug != '') {

            getSeoGHI:

            $art_pages = DB::table('dynamic_pages')
                ->where('slug', $slug)
                ->where('status', 'active')
                ->where('deletes', '0')
                ->orderByDesc('id')
                ->limit(1)
                ->get();

            if ($art_pages->count() === 0) {
                $seoTags['page_exist'] = false;
                view()->share('seoTags', $seoTags);
                return $next($request);
            }

            $cityName = $art_pages[0]->name;

            $seoTags = array_merge($seoTags, [
                'id' => $art_pages[0]->id,
                'cityName' => $cityName,
                'metaTitle' => $art_pages[0]->title,
                'metaDes' => $art_pages[0]->description,
                'metaKeyword' => $art_pages[0]->keyword,
                'shortNote' => $art_pages[0]->shortNote,
                'img' => $art_pages[0]->img,
                'wikiDes' => $art_pages[0]->wikiDes,
                'wikiDesHtml' => $art_pages[0]->wikiDesHtml,
            ]);

            /* ---------- OPENAI (FIRST TIME ONLY) ---------- */
            if (!$art_pages[0]->shortNote && $art_pages[0]->chatGptStatus === 'NO') {

                DB::table('dynamic_pages')
                    ->where('id', $art_pages[0]->id)
                    ->update(['chatGptStatus' => 'YES']);

                $ai = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPEN_AI_KEY')
                ])->post(env('OPEN_AI_API'), [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => 'Generate SEO content for '.$cityName]
                    ]
                ]);

                if ($ai->successful()) {
                    $json = json_decode($ai['choices'][0]['message']['content'], true);
                    DB::table('dynamic_pages')->where('id', $art_pages[0]->id)->update([
                        'title' => $json['seo_title'] ?? null,
                        'description' => $json['meta_description'] ?? null,
                        'keyword' => is_array($json['keywords'] ?? null) ? implode(', ', $json['keywords']) : null,
                        'shortNote' => $json['content'] ?? null,
                    ]);
                    goto getSeoGHI;
                }
            }

            /* ---------- WIKIPEDIA (FIRST TIME ONLY) ---------- */
            if (!$seoTags['wikiDes']) {
                $wiki = Http::get(env('WIKI_SUMMERY') . $cityName);
                if ($wiki->successful()) {
                    DB::table('dynamic_pages')->where('id', $art_pages[0]->id)->update([
                        'wikiDes' => $wiki['extract'] ?? null,
                        'wikiDesHtml' => $wiki['extract_html'] ?? null,
                    ]);
                    goto getSeoGHI;
                }
            }
        }
        
        // else if ($slug && !str_contains($request->path(), 'car-rental') && str_contains($request->path(), 'taxi') && !str_contains($request->path(), 'blog') && isset( $slug) && $slug != '') {

        //     getSeoGHI:

        //     $art_pages = DB::table('dynamic_pages')
        //         ->where('slug', $slug)
        //         ->where('status', 'active')
        //         ->where('deletes', '0')
        //         ->orderByDesc('id')
        //         ->limit(1)
        //         ->get();

        //     if ($art_pages->count() === 0) {
        //         $seoTags['page_exist'] = false;
        //         view()->share('seoTags', $seoTags);
        //         return $next($request);
        //     }

        //     $cityName = $art_pages[0]->name;

        //     $seoTags = array_merge($seoTags, [
        //         'id' => $art_pages[0]->id,
        //         'cityName' => $cityName,
        //         'metaTitle' => $art_pages[0]->title,
        //         'metaDes' => $art_pages[0]->description,
        //         'metaKeyword' => $art_pages[0]->keyword,
        //         'shortNote' => $art_pages[0]->shortNote,
        //         'img' => $art_pages[0]->img,
        //         'wikiDes' => $art_pages[0]->wikiDes,
        //         'wikiDesHtml' => $art_pages[0]->wikiDesHtml,
        //     ]);

        //     /* ---------- OPENAI (FIRST TIME ONLY) ---------- */
        //     if (!$art_pages[0]->shortNote && $art_pages[0]->chatGptStatus === 'NO') {

        //         DB::table('dynamic_pages')
        //             ->where('id', $art_pages[0]->id)
        //             ->update(['chatGptStatus' => 'YES']);

        //         $ai = Http::withHeaders([
        //             'Authorization' => 'Bearer ' . env('OPEN_AI_KEY')
        //         ])->post(env('OPEN_AI_API'), [
        //             'model' => 'gpt-3.5-turbo',
        //             'messages' => [
        //                 ['role' => 'system', 'content' => 'You are a helpful assistant.'],
        //                 ['role' => 'user', 'content' => 'Generate SEO content for '.$cityName]
        //             ]
        //         ]);

        //         if ($ai->successful()) {
        //             $json = json_decode($ai['choices'][0]['message']['content'], true);
        //             DB::table('dynamic_pages')->where('id', $art_pages[0]->id)->update([
        //                 'title' => $json['seo_title'] ?? null,
        //                 'description' => $json['meta_description'] ?? null,
        //                 'keyword' => is_array($json['keywords'] ?? null) ? implode(', ', $json['keywords']) : null,
        //                 'shortNote' => $json['content'] ?? null,
        //             ]);
        //             goto getSeoGHI;
        //         }
        //     }

        //     /* ---------- WIKIPEDIA (FIRST TIME ONLY) ---------- */
        //     if (!$seoTags['wikiDes']) {
        //         $wiki = Http::get(env('WIKI_SUMMERY') . $cityName);
        //         if ($wiki->successful()) {
        //             DB::table('dynamic_pages')->where('id', $art_pages[0]->id)->update([
        //                 'wikiDes' => $wiki['extract'] ?? null,
        //                 'wikiDesHtml' => $wiki['extract_html'] ?? null,
        //             ]);
        //             goto getSeoGHI;
        //         }
        //     }
        // }

        // elseif ($slug && str_contains($request->path(), 'car-rental') && isset( $slug) && $slug != '') {

        //     $explode = explode('-', $slug);
        //     $fromPlace = strtoupper($explode[0] ?? '');
        //     $toPlace = strtoupper($explode[2] ?? '');

        //     getSeoEGHI:

        //     $art_pages = DB::table('dynamic_pages_local')
        //         ->where('name', $fromPlace)
        //         ->where('to_place', $toPlace)
        //         ->where('status', 'active')
        //         ->where('deletes', '0')
        //         ->orderByDesc('id')
        //         ->limit(1)
        //         ->get();
                
        //     $kms = $art_pages[0]->kms;
            
        //     $bindings = array_fill(0, 36, $kms);

        //     $get_tariff = DB::selectOne("
        //         SELECT
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS mini_four_kms,
                    
        //             COALESCE(
        //                 (SELECT mini_four_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS mini_four_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS four_kms,
                    
        //             COALESCE(
        //                 (SELECT four_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS four_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS six_kms,
                    
        //             COALESCE(
        //                 (SELECT six_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS six_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS seven_kms,
                    
        //             COALESCE(
        //                 (SELECT seven_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS seven_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS onethree_kms,
                    
        //             COALESCE(
        //                 (SELECT onethree_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS onethree_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS oneeight_kms,
                    
        //             COALESCE(
        //                 (SELECT oneeight_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS oneeight_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS twoone_kms,
                    
        //             COALESCE(
        //                 (SELECT twoone_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS twoone_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS twofive_kms,
                    
        //             COALESCE(
        //                 (SELECT twofive_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS twofive_seater,
                    
        //             COALESCE(
        //                 (SELECT to_km FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS fivezero_kms,
                    
        //             COALESCE(
        //                 (SELECT fivezero_seater FROM tariff_fare 
        //                  WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
        //                 'N/A'
        //             ) AS fivezero_seater
        //     ", $bindings);

        //     if ($art_pages->count() === 0) {
        //         $seoTags['page_exist'] = false;
        //         view()->share('seoTags', $seoTags);
        //         return $next($request);
        //     }

        //     $seoTags['cityName'] = $art_pages[0]->name;
        //     $seoTags['kms'] = $art_pages[0]->kms;
        // }
        
        elseif( isset( $slug) && $slug != '' && str_contains(request()->path(), 'car-rental') && !str_contains($request->path(), 'blog')) {
             
            $explode = explode('-', $slug);
            
            $fromPlace = strtoupper($explode[0] ?? '');
            $toPlace = strtoupper($explode[2] ?? '');

            getSeoEGHI:

            $art_pages = DB::table('dynamic_pages_local')
                ->where('name', $fromPlace)
                ->where('to_place', $toPlace)
                ->where('status', 'active')
                ->where('deletes', '0')
                ->orderByDesc('id')
                ->limit(1)
                ->get();
                                
                                
            $cityName = null;
          
            if ($art_pages->count() > 0) {
               
                $cityName = $art_pages[0]->name;
                
                
                $kms = $art_pages[0]->kms;

                $bindings = array_fill(0, 36, $kms);

                $get_tariff = DB::selectOne("
                    SELECT
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS mini_four_kms,
                        
                        COALESCE(
                            (SELECT mini_four_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS mini_four_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS four_kms,
                        
                        COALESCE(
                            (SELECT four_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS four_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS six_kms,
                        
                        COALESCE(
                            (SELECT six_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS six_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS seven_kms,
                        
                        COALESCE(
                            (SELECT seven_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS seven_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS onethree_kms,
                        
                        COALESCE(
                            (SELECT onethree_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS onethree_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS oneeight_kms,
                        
                        COALESCE(
                            (SELECT oneeight_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS oneeight_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twoone_kms,
                        
                        COALESCE(
                            (SELECT twoone_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twoone_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twofive_kms,
                        
                        COALESCE(
                            (SELECT twofive_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twofive_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS fivezero_kms,
                        
                        COALESCE(
                            (SELECT fivezero_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS fivezero_seater
                ", $bindings);
                
                $getLinksFor = DB::table('dynamic_pages_local as d')
                    ->select(
                        'd.name',
                        'd.to_place',
                        'd.kms',
                        DB::raw("CONCAT('car-rental/', LOWER(d.name), '-to-', LOWER(d.to_place), '-outstation-cab-service') AS slug"),
            
                        DB::raw("(SELECT mini_four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_seater"),
                        DB::raw("(SELECT four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_seater"),
                        DB::raw("(SELECT six_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_seater"),
                        DB::raw("(SELECT seven_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_seater"),
                        DB::raw("(SELECT onethree_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_seater"),
                        DB::raw("(SELECT oneeight_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_seater"),
                        DB::raw("(SELECT twoone_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_seater"),
                        DB::raw("(SELECT twofive_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_seater"),
                        DB::raw("(SELECT fivezero_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_seater"),
            
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_kms")
                    )
                     ->where('d.deletes', '=', '0')
                    ->where('d.status', '=', 'active')
                    ->where('d.name', '=', strtoupper($fromPlace))
                    ->where('d.country_code', '=', $art_pages[0]->country_code)
                    ->orderBy('d.name', 'ASC')
                    ->get();
                    
                $seoTags = [
                    'id' => $art_pages[0]->id,
                    'cityName' => $cityName,
                    'metaTitle' => $art_pages[0]->title,
                    'metaDes' => $art_pages[0]->description,
                    'metaKeyword' => $art_pages[0]->keyword,
                    'canonical' => $request->fullUrl() ?? '',
                    'shortNote' => $art_pages[0]->shortNote,
                    'img' => $art_pages[0]->img ?? '',
                    'wikiDes' => $art_pages[0]->wikiDes ?? '',
                    'wikiDesHtml' => $art_pages[0]->wikiDesHtml ?? '',
                    'to_wikiDes' => $art_pages[0]->to_wikiDes ?? '',
                    'to_wikiDesHtml' => $art_pages[0]->to_wikiDesHtml ?? '',
                    'slug' => $slug,
                    'slug' => $slug,
                    'kms' => $art_pages[0]->kms,
                    'innerLinks' => [],
                    'listPages' => [],
                    'faqData' => [],
                    'mini_four_seater' => $get_tariff->mini_four_seater,
                    'four_seater' => $get_tariff->four_seater,
                    'six_seater' => $get_tariff->six_seater,
                    'seven_seater' => $get_tariff->seven_seater,
                    'onethree_seater' => $get_tariff->onethree_seater,
                    'twoone_seater' => $get_tariff->twoone_seater,
                    'fivezero_seater' => $get_tariff->fivezero_seater,
                    'mini_four_km' => $get_tariff->mini_four_kms,
                    'four_km' => $get_tariff->four_kms,
                    'six_km' => $get_tariff->six_kms,
                    'seven_km' => $get_tariff->seven_kms,
                    'onethree_km' => $get_tariff->onethree_kms,
                    'twoone_km' => $get_tariff->twoone_kms,
                    'fivezero_km' => $get_tariff->fivezero_kms,
                    'page_exist' => true
                ];
            
                if ($getLinksFor->count() > 0) {
                    $seoTags['innerLinks'] = json_decode(json_encode($getLinksFor), true);
                    $seoTags['pop_routes'] = $seoTags['innerLinks'];
                }

                 
           }
           
           if ($secondSegment) {

                // ranipet-to-namakkal-outstation-cab-service
                $parts = explode('-', $secondSegment);
            
                // Expected format: from-to-toLocation-outstation-cab-service
                // ranipet-to-namakkal-outstation-cab-service
            
                if (count($parts) >= 3) {
            
                    $from = $parts[0]; // ranipet
                    $to   = $parts[2]; // namakkal
            
                    // FROM LOCATION
                    $g_from = DB::table('outstation_locations')
                        ->where('search_key', 'LIKE', '%' . $from . 'tamilnaduindia%')
                        ->first();
            
                    if ($g_from) {
                        $seoTags['from']    = $g_from->display_name;
                        $seoTags['from_id'] = $g_from->place_id;
                        $seoTags['from_lat'] = $g_from->latitude;
                        $seoTags['from_lng'] = $g_from->longitude;
                    }
            
                    // TO LOCATION
                    $t_from = DB::table('outstation_locations')
                        ->where('search_key', 'LIKE', '%' . $to . 'tamilnaduindia%')
                        ->first();
            
                    if ($t_from) {
                        $seoTags['to']    = $t_from->display_name;
                        $seoTags['to_id'] = $t_from->place_id;
                        $seoTags['to_lat'] = $t_from->latitude;
                        $seoTags['to_lng'] = $t_from->longitude;
                    }
                }
            }
           
           
        }
        
        elseif( isset( $slug) && $slug != '' && !str_contains(request()->path(), 'car-rental')  && str_contains($request->path(), 'taxi') && !str_contains($request->path(), 'blog')) {
             
            $explode = explode('-', $slug);
            
            $toPlace = strtoupper($explode[0] ?? '');
            // $toPlace = strtoupper($explode[2] ?? '');
            // dd($toPlace);

            getSeoEGHII:

            $art_pages = DB::table('dynamic_pages_local')
                // ->where('name', $fromPlace)
                ->where('to_place', $toPlace)
                ->where('status', 'active')
                ->where('deletes', '0')
                ->orderByDesc('id')
                ->limit(1)
                ->first();
                                
                                
            $cityName = null;
          
            if ($art_pages) {
               
                $cityName = $art_pages->to_place;
                
                
                $kms = $art_pages->kms;

                $bindings = array_fill(0, 36, $kms);

                $get_tariff = DB::selectOne("
                    SELECT
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS mini_four_kms,
                        
                        COALESCE(
                            (SELECT mini_four_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND mini_four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS mini_four_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS four_kms,
                        
                        COALESCE(
                            (SELECT four_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND four_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS four_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS six_kms,
                        
                        COALESCE(
                            (SELECT six_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND six_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS six_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS seven_kms,
                        
                        COALESCE(
                            (SELECT seven_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND seven_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS seven_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS onethree_kms,
                        
                        COALESCE(
                            (SELECT onethree_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND onethree_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS onethree_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS oneeight_kms,
                        
                        COALESCE(
                            (SELECT oneeight_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND oneeight_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS oneeight_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twoone_kms,
                        
                        COALESCE(
                            (SELECT twoone_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twoone_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twoone_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twofive_kms,
                        
                        COALESCE(
                            (SELECT twofive_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND twofive_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS twofive_seater,
                        
                        COALESCE(
                            (SELECT to_km FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS fivezero_kms,
                        
                        COALESCE(
                            (SELECT fivezero_seater FROM tariff_fare 
                             WHERE from_km <= ? AND to_km >= ? AND fivezero_seater > 0 LIMIT 1),
                            'N/A'
                        ) AS fivezero_seater
                ", $bindings);
                
                $getLinksFor = DB::table('dynamic_pages_local as d')
                    ->select(
                        'd.name',
                        'd.to_place',
                        'd.kms',
                        DB::raw("CONCAT('car-rental/', LOWER(d.name), '-to-', LOWER(d.to_place), '-outstation-service') AS slug"),
            
                        DB::raw("(SELECT mini_four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_seater"),
                        DB::raw("(SELECT four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_seater"),
                        DB::raw("(SELECT six_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_seater"),
                        DB::raw("(SELECT seven_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_seater"),
                        DB::raw("(SELECT onethree_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_seater"),
                        DB::raw("(SELECT oneeight_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_seater"),
                        DB::raw("(SELECT twoone_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_seater"),
                        DB::raw("(SELECT twofive_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_seater"),
                        DB::raw("(SELECT fivezero_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_seater"),
            
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_kms"),
                        DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_kms")
                    )
                     ->where('d.deletes', '=', '0')
                    ->where('d.status', '=', 'active')
                    ->where('d.name', '=', strtoupper($toPlace))
                    ->where('d.country_code', '=', $art_pages->country_code)
                    ->orderBy('d.name', 'ASC')
                    ->get();
                    
                $seoTags = [
                    'id' => $art_pages->id,
                    'cityName' => $cityName,
                    'metaTitle' => $art_pages->title,
                    'metaDes' => $art_pages->description,
                    'metaKeyword' => $art_pages->keyword,
                    'canonical' => $request->fullUrl() ?? '',
                    'shortNote' => $art_pages->shortNote,
                    'img' => $art_pages->img ?? '',
                    'wikiDes' => $art_pages->wikiDes ?? '',
                    'wikiDesHtml' => $art_pages->wikiDesHtml ?? '',
                    'to_wikiDes' => $art_pages->to_wikiDes ?? '',
                    'to_wikiDesHtml' => $art_pages->to_wikiDesHtml ?? '',
                    'slug' => $slug,
                    'slug' => $slug,
                    'kms' => $art_pages->kms,
                    'innerLinks' => [],
                    'listPages' => [],
                    'faqData' => [],
                    'mini_four_seater' => $get_tariff->mini_four_seater,
                    'four_seater' => $get_tariff->four_seater,
                    'six_seater' => $get_tariff->six_seater,
                    'seven_seater' => $get_tariff->seven_seater,
                    'onethree_seater' => $get_tariff->onethree_seater,
                    'twoone_seater' => $get_tariff->twoone_seater,
                    'fivezero_seater' => $get_tariff->fivezero_seater,
                    'mini_four_km' => $get_tariff->mini_four_kms,
                    'four_km' => $get_tariff->four_kms,
                    'six_km' => $get_tariff->six_kms,
                    'seven_km' => $get_tariff->seven_kms,
                    'onethree_km' => $get_tariff->onethree_kms,
                    'twoone_km' => $get_tariff->twoone_kms,
                    'fivezero_km' => $get_tariff->fivezero_kms,
                    'page_exist' => true
                ];
            
                if ($getLinksFor->count() > 0) {
                    $seoTags['innerLinks'] = json_decode(json_encode($getLinksFor), true);
                    $seoTags['pop_routes'] = $seoTags['innerLinks'];
                }

                 
           }
           
           if ($secondSegment) {

                // ranipet-to-namakkal-outstation-cab-service
                $parts = explode('-', $secondSegment);
            
                // Expected format: from-to-toLocation-outstation-cab-service
                // ranipet-to-namakkal-outstation-cab-service
            
                if (count($parts) >= 3) {
            
                    $from = $parts[0]; // ranipet
                    $to   = $parts[2]; // namakkal
            
                    // FROM LOCATION
                    $g_from = DB::table('outstation_locations')
                        ->where('search_key', 'LIKE', '%' . $from . 'tamilnaduindia%')
                        ->first();
            
                    if ($g_from) {
                        $seoTags['from']    = $g_from->display_name;
                        $seoTags['from_id'] = $g_from->place_id;
                        $seoTags['from_lat'] = $g_from->latitude;
                        $seoTags['from_lng'] = $g_from->longitude;
                    }
            
                    // TO LOCATION
                    $t_from = DB::table('outstation_locations')
                        ->where('search_key', 'LIKE', '%' . $to . 'tamilnaduindia%')
                        ->first();
            
                    if ($t_from) {
                        $seoTags['to']    = $t_from->display_name;
                        $seoTags['to_id'] = $t_from->place_id;
                        $seoTags['to_lat'] = $t_from->latitude;
                        $seoTags['to_lng'] = $t_from->longitude;
                    }
                }
            }
           
        //   dd($seoTags);
        }

        else {

            /* ================= POPULAR ROUTES ================= */
        
            $getLinksFor = DB::table('dynamic_pages_local as d')
                ->select(
                    'd.name',
                    'd.to_place',
                    'd.kms',
                    DB::raw("CONCAT('car-rental/', LOWER(d.name), '-to-', LOWER(d.to_place), '-outstation-cab-service') AS slug"),
        
                    DB::raw("(SELECT mini_four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_seater"),
                    DB::raw("(SELECT four_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_seater"),
                    DB::raw("(SELECT six_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_seater"),
                    DB::raw("(SELECT seven_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_seater"),
                    DB::raw("(SELECT onethree_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_seater"),
                    DB::raw("(SELECT oneeight_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_seater"),
                    DB::raw("(SELECT twoone_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_seater"),
                    DB::raw("(SELECT twofive_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_seater"),
                    DB::raw("(SELECT fivezero_seater FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_seater"),
        
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND mini_four_seater > 0 LIMIT 1) AS mini_four_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND four_seater > 0 LIMIT 1) AS four_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND six_seater > 0 LIMIT 1) AS six_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND seven_seater > 0 LIMIT 1) AS seven_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND onethree_seater > 0 LIMIT 1) AS onethree_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND oneeight_seater > 0 LIMIT 1) AS oneeight_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twoone_seater > 0 LIMIT 1) AS twoone_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND twofive_seater > 0 LIMIT 1) AS twofive_kms"),
                    DB::raw("(SELECT to_km FROM tariff_fare WHERE from_km <= d.kms AND to_km >= d.kms AND fivezero_seater > 0 LIMIT 1) AS fivezero_kms")
                )
                ->where('d.deletes', '0')
                ->where('d.status', 'active')
                ->where('d.country_code', 'IN')
                ->inRandomOrder()
                ->limit(20)
                ->orderBy('d.name')
                ->get();
        
            if ($getLinksFor->count() > 0) {
                $seoTags['innerLinks'] = json_decode(json_encode($getLinksFor), true);
                $seoTags['pop_routes'] = $seoTags['innerLinks'];
            }
        
            /* ================= GROUPED ROUTES ================= */
        
            if (!str_contains($request->path(), 'car-rental') && !str_contains($request->path(), 'blog')) {
        
                $getFromPlaces = DB::table('dynamic_pages_local')
                    ->where([
                        'deletes' => '0',
                        'status' => 'active',
                        'country_code' => 'IN',
                    ])
                    ->groupBy('name')
                    ->pluck('name');
        
                $grouped = DB::table('dynamic_pages_local as d')
                    ->select(
                        DB::raw("LOWER(d.name) as name"),
                        'd.to_place',
                        'd.kms',
                        DB::raw("CONCAT(
                            'car-rental/',
                            LOWER(d.name),
                            '-to-',
                            LOWER(d.to_place),
                            '-outstation-cab-service'
                        ) AS slug")
                    )
                    ->whereIn(DB::raw('UPPER(d.name)'), $getFromPlaces)
                    ->where('d.deletes', '0')
                    ->where('d.status', 'active')
                    ->where('d.country_code', 'IN')
                    ->orderBy('d.name')
                    ->get()
                    ->groupBy('name')
                    ->map(function ($group) {
                        // ðŸ”‘ normalize nested collections
                        return json_decode(json_encode($group), true);
                    })
                    ->toArray();
        
                $seoTags['innerLinks'] = $grouped;
            }
            
            if($firstSegment == 'blog' && $thirdSegment == null){
                
                // function normalizeUtf8($data)
                // {
                //     if (is_array($data)) {
                //         foreach ($data as $k => $v) {
                //             $data[$k] = normalizeUtf8($v);
                //         }
                //         return $data;
                //     }
                
                //     if (is_string($data)) {
                //         return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
                //     }
                
                //     return $data;
                // }

                
                $seoTags['metaTitle'] = "GoRide - Blog All things you have to know about the taxi/cab services.";
                
                $seoTags['metaDes'] = 'GoRide Blog shares everything you need to know about taxi and cab services, including one way trip cabs, round trip bookings, online cab booking tips, affordable ride options, and safe travel guidance.';

                $seoTags['metaKeyword'] = 'taxi services, cab services, online cab booking, ride hailing service, book taxi online, affordable cab service, best taxi service, local taxi service, city cab service, airport cab service, one way trip cab, one way cab, one way taxi, one way cab booking, one way cab service, cab one way trip, book one way cab, cheap one way cab, one way  outstation cab, one way ride booking, two way cab, round trip cab, two way taxi, two way cab booking, round trip taxi service, cab round trip booking, two way outstation cab, return trip cab, GoRide blog, GoRide taxi service, GoRide cab booking, GoRide one way cab, GoRide taxi booking, cab booking guide, taxi service tips, all about cab services';
                $all_data = DB::table('blogs_content as bc')
                    ->join('categories as cat', 'cat.id', '=', 'bc.category_id')
                    ->select(
                        'bc.blog_title',
                        'bc.description',
                        'bc.published_date',
                        'bc.thumbnail',
                        DB::raw("CONCAT('https://goride.run/', cat.cat_url, '/', bc.slug) as slug")
                    )
                    ->where('bc.status', 1)
                    ->where('bc.deleted_at', 0)
                    ->orderBy('bc.published_date', 'desc')
                    ->get()->toArray();
                
                $seoTags['blogPages'] = $all_data;
                
                // $seoTags = normalizeUtf8($seoTags);
            }else if($firstSegment == 'blog' && $thirdSegment != null){
                
                $get_data = DB::table('blogs_content as bc')->join('categories as ct', 'ct.id', 'bc.category_id')->where(['bc.slug' => $thirdSegment, 'bc.status' => 1, 'bc.deleted_at' => 0])->first();

                
                if($get_data){
                    
                    $seoTags['metaTitle'] = $get_data->seo_title;
                    
                    $seoTags['metaDes'] = $get_data->meta_description;
                    
                    $seoTags['metaKeyword'] = $get_data->meta_keywords;
                    
                    $seoTags['img'] = $get_data->hero_image;
                    $seoTags['shortNote'] = $get_data->blog_title;
                    $seoTags['wikiDes'] = $get_data->description;
                    $seoTags['wikiDesHtml'] = $get_data->published_date;
                    $faqArray = json_decode($get_data->faq, true);
                    
                    $seoTags['faqData'] = (is_array($faqArray) && count($faqArray) > 0) ? $faqArray : [];
                    $seoTags['blogPages'] = [
                        'first' => 'blog',
                        'second' => $get_data->cat_name,
                        'third' => $get_data->sub_title
                    ];
                    // $seoTags['blogPages'] = $all_data;
                    
                }
                
                // $seoTags = normalizeUtf8($seoTags);
                
            }
            
        }
        
        // dd($seoTags);
        File::put(
            $cachePath,
            json_encode($seoTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        
        if (File::exists($cachePath)) {
            $seoTags = json_decode(File::get($cachePath), true) ?? [];
            // dd($seoTags);
            view()->share('seoTags', $seoTags);
            return $next($request);
        }

        // view()->share('seoTags', $seoTags);
        // return $next($request);
    }
}