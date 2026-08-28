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
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // Get current route name
        $article = Route::currentRouteName() ?: 'home';

       
        $seoTags = [
            'id' => '',
            'metaTitle' => '',
            'metaDes' => '',
            'metaKeyword' => '',
            'canonical' => '',
            'shortNote' => '',
            'img' => '',
            'wikiDes' => '',
            'wikiDesHtml' => '',
            'listPages' => [],
            'slug' => '',
            'innerLinks' => [],
             'faqData' => []
        ];

      
        $art_pages = DB::table('article')
            ->where('url', '=', $article)
            ->where('status', '=', 'Active')
            ->orderby('id', 'desc')
            ->limit(1)
            ->get();

        if ($art_pages->count() > 0) {
            $seoTags = [
                'id' => $art_pages[0]->id,
                'metaTitle' => $art_pages[0]->meta_title,
                'metaDes' => $art_pages[0]->meta_desp,
                'metaKeyword' => $art_pages[0]->meta_keyword,
                'canonical' => $request->fullUrl() ?? ''
            ];
        }
        
        
        
       $slug = $request->route('slug') ?: '';

         
         if( isset( $slug) && $slug != '') {
             
             
             getSeoGHI:
              $art_pages = DB::table('dynamic_pages')
            //   -> select(*, DB(concat(env('DO_REDIRECT_URL'). img)))
                                ->where('slug', '=', $slug)
                                ->where('status', '=', 'active')
                                ->where('deletes', '=', '0')
                                ->orderby('id', 'desc')
                                ->limit(1)
                                ->get();
                                
                                
              $cityName = null;
          
           if ($art_pages->count() > 0) {
               
                 $cityName = $art_pages[0]->name;
                 
                 
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
                            'slug' => $slug,
                             'innerLinks' => [],
                               'listPages' => [],
                               'faqData' => []
                             
                        ];
                        
                        
                   }
                   
                   
                //   dd($art_pages[0]->chatGptStatus);
                   
                    if(isset($cityName) &&  $cityName != null && !isset($art_pages[0]->shortNote) && $art_pages[0]->chatGptStatus === 'NO') {
                
                
               $updated = DB::table('dynamic_pages')
                        ->where('id', $art_pages[0]->id)  
                        ->update([
                            'chatGptStatus' => 'YES' ,
                            'updated_at' => now() 
                        ]);
                        
                        
                        
           
           $getAPIrs = Http::withHeaders([
                                'Authorization' => 'Bearer ' . env('OPEN_AI_KEY'),  
                            ])->post(env('OPEN_AI_API'), [
                                'model' => 'gpt-3.5-turbo',  
                                'messages' => [
                                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                                    // ['role' => 'user', 'content' => 'Generate SEO content strictly in the following JSON format for a page targeting '.$cityName.' tourist places. The website is "GoRide," which sells taxi/cab dispatch software. The JSON format is: json { "seo_title": "<SEO Title>", "meta_description": "<Meta Description>", "keywords": ["<Keyword1>", "<Keyword2>", "<Keyword3>"], "content": "<Attractive Content>" } Ensure the content section is more than 400 characters long, emphasizing '.$cityName.'\'s tourist attractions and how GoRide\'s software can enhance travel experiences for cab businesses.']
                                
                                        ['role' => 'user', 'content' => 'Generate SEO content strictly in the following JSON format for a page targeting '.$cityName.' tourist places. The website is GoRide, which sells taxi/cab dispatch software. The JSON format is: {"seo_title": "<SEO Title>","meta_description": "<Meta Description>","keywords": ["<Keyword1>", "<Keyword2>", "<Keyword3>", "<Keyword4>", "<Keyword5>", "<Keyword6>", "<Keyword7>", "<Keyword8>", "<Keyword9>", "<Keyword10>", "<Keyword11>", "<Keyword12>"],"content": "<Attractive Content>"} Ensure the content section is more than 400 characters long. The content should emphasize '.$cityName.'\'s tourist attractions and how GoRide\'s taxi/cab dispatch software can enhance travel experiences for cab businesses. Include more than 10 relevant SEO keywords related to '.$cityName.' tourism, taxi services, and GoRide’s software.']
                                ],
                            ]);

                if ($getAPIrs->successful()) {
   
    $responseData = $getAPIrs->json(); 
  
     if (isset($responseData['choices'][0]['message']['content'])) {
       
        $generatedContent = $responseData['choices'][0]['message']['content'];

            $decodedContent = json_decode($generatedContent, true);
 
           $seo_title = $decodedContent['seo_title'] ?? null;
           $meta_description = $decodedContent['meta_description'] ?? null;
           $keywords = $decodedContent['keywords'] ?? null;
           $content = $decodedContent['content'] ?? null;
    
       $updated = DB::table('dynamic_pages')
                        ->where('id', $art_pages[0]->id)  
                        ->update([
                            'title' => $seo_title ,
                            'description' => $meta_description ,
                            'keyword' => is_array($keywords) && count($keywords) > 0  ? implode(', ', $decodedContent['keywords']) : null,
                            'shortNote' => $content ,
                            'updated_at' => now() 
                        ]);
                        
                        
                if($updated) {
                    goto getSeoGHI;
                }
        
             } 
           } 
           
           
             
                        
           
           
           
           
         }
         
         
         if(isset($cityName) && !isset($art_pages[0]->wikiDes) && (!isset($seoTags['img']) || $seoTags['img'] == '' || $seoTags['img'] == null)) {
             
             $getWiki = Http::get(env('WIKI_SUMMERY') . $cityName);
             
             if ($getWiki->successful()) {
                 
                 
                 $wikSum = $getWiki->json();
                 
                // $wikiCon = json_decode($wikSum, true);
                  
                 $wikiDesHtml = $wikSum['extract_html'] ?? null;
                 
                $wikiDes = $wikSum['extract'] ?? null;
                
$thumbnail = isset($wikSum['thumbnail']['source']) ? $wikSum['thumbnail']['source'] : null;
                if($thumbnail != ''){
    $directoryPath = public_path('goride/img/dynamicPages');
    
    if (!File::exists($directoryPath)) {
    File::makeDirectory($directoryPath, 0775, true);  
}


    $fileName = basename($thumbnail);
    
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
    
    // $localPath = $directoryPath . '/' . $fileName;
    
      $localPath = $directoryPath  . '/' . $slug . '.' . $fileExtension;
    // dd($localPath);
    
      $imageGet = Http::get($thumbnail);

 
    if ($imageGet->successful()) {
        
        $imageContent = $imageGet->body();

       
        $saveIMG = file_put_contents($localPath, $imageContent);

        
        if ($saveIMG) {
            $thumbnail = 'goride/img/dynamicPages/'  . $slug . '.' . $fileExtension;
            
        } 
    } 



    
        //  dd( $fileName);
}


 $updated = DB::table('dynamic_pages')
                        ->where('id', $art_pages[0]->id)  
                        ->update([
                            'wikiDes' => $wikiDes ,
                            'wikiDesHtml' => $wikiDesHtml ,
                            // 'keyword' => is_array($keywords) && count($keywords) > 0  ? implode(', ', $decodedContent['keywords']) : null,
                            'img' => $thumbnail ,
                            'updated_at' => now() 
                        ]);
                        
                        
                if($updated) {
                    goto getSeoGHI;
                }
 
  

              
       
                   
                 
             }
          
         }
         
         
    //  $seoTags['img']     = env('DO_REDIRECT_URL') . $seoTags['img'];
    
            $seoTags['metaTitle'] = isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) . ' Taxi/Cab Dispatch Software  with Goride' : $seoTags['metaTitle'];
         
      
      
      if(isset($art_pages[0]->country_code) && $art_pages[0]->country_code != '') {
          
    
          
                
                $getLinksFor = DB::table('dynamic_pages as d')
    ->select(
        'd.name',
        'd.slug'
        // 'd.slug'
        // DB::raw('CONCAT("<a style=\"color: #467bbe; text-decoration: underline;\" href=\"", d.slug, "\">", d.name, "</a>") AS link')
    )
    ->where('d.deletes', '=', '0')
    ->where('d.status', '=', 'active')
    ->where('d.country_code','=', $art_pages[0]->country_code)
    ->orderBy('d.name', 'ASC')
    ->get();
                
                // dd($getLinksFor);
                  if($getLinksFor->count() > 0) {
                      
                      $seoTags['innerLinks'] = $getLinksFor->toArray();
                }
                
      }
      
      
      
      
      $seoTags['faqData'] = [
    [
        'question' => 'What is Taxi Cab Dispatch Software?',
        'answer' => 'Taxi cab dispatch software is a system designed to help manage and streamline the operation of taxi services. For example, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', the software enables taxi companies to handle ride requests, track vehicles, and process payments, all in real-time. It connects drivers, passengers, and dispatchers, ensuring efficient and smooth operations.'
    ],
    [
        'question' => 'How Does Taxi Dispatch Software Work?',
        'answer' => 'Taxi dispatch software works by allowing passengers to request rides via an app or phone call. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for instance, the software automatically identifies the passenger\'s location and assigns the nearest available driver based on GPS. The dispatcher monitors the ride progress and communicates with the driver through the system.'
    ],
    [
        'question' => 'Is Taxi Dispatch Software compatible with different devices?',
        'answer' => 'Yes, taxi dispatch software is compatible with various devices. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for example, drivers use mobile apps to receive ride requests, while dispatchers use desktop interfaces to manage the fleet.'
    ],
    [
        'question' => 'Can passengers track their rides in real-time?',
        'answer' => 'Yes, passengers can track their ride in real-time. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', passengers can see their driver\'s location, estimated time of arrival, and route via a mobile app, ensuring transparency and reducing uncertainty.'
    ],
    [
        'question' => 'How does Taxi Dispatch Software handle payment?',
        'answer' => 'Taxi dispatch software supports various payment methods, including credit cards, digital wallets, and cash. For instance, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', passengers can pay seamlessly through the app, while drivers receive automatic payment processing at the end of each trip.'
    ],
    [
        'question' => 'Can I offer discounts or promotions through the software?',
        'answer' => 'Yes, many taxi dispatch software platforms allow you to set up and offer promotional codes or discounts. For example, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', businesses can create special promotions for customers during holidays or events, enhancing customer retention and driving more bookings.'
    ],
    [
        'question' => 'Can passengers schedule rides in advance?',
        'answer' => 'Yes, passengers can schedule rides in advance using taxi dispatch software. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for example, passengers can book rides for specific times and dates, ensuring that they have a taxi ready when they need it, such as for airport pickups or appointments.'
    ]
];

                
        }
         
         
         
    //  $getRandomPage =  DB::table('dynamic_pages')
    // ->select('name', 'slug')
    // ->where('deletes', '=', '0')
    // ->where('status', '=', 'active')
    // ->inRandomOrder()  
    // ->limit(20)
    // ->get();
            
            
             $getRandomPage =   DB::table('dynamic_pages as d')
    ->leftJoin('countries as c', 'c.iso2', '=', 'd.country_code')
    ->select(DB::raw("CONCAT(d.name, IFNULL(CONCAT(' - ', c.name), '')) as name"), 'd.slug')
             
    //          DB::table('dynamic_pages as d')
    // ->leftJoin('countries as c', 'c.iso2', '=', 'd.country_code')
    // ->select(DB::raw("CONCAT(d.name, ' - ', c.name) as name"), 'd.slug')
     ->where('d.deletes', '=', '0')
    ->where('d.status', '=', 'active')
    ->inRandomOrder()  
    ->limit(10)
    ->get();
    
                if( $getRandomPage->count() > 0){
                  $seoTags['listPages'] = $getRandomPage->toArray();
                }
            
                
                
                //  dd( $seoTags);

        // Share SEO tags with all views
        view()->share('seoTags', $seoTags);

        // Continue with the request
        return $next($request);
    }
}
