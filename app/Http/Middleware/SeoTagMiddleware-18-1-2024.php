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
        'question' => 'How can GoRide’s taxi booking software improve my taxi business in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s taxi booking software streamlines the booking process by allowing customers to book rides quickly and efficiently. It helps your business in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' by reducing wait times, automating bookings, and improving customer satisfaction.'
    ],
    [
        'question' => 'Is GoRide’s taxi management software suitable for managing a fleet in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'Yes, GoRide’s taxi management software is designed to efficiently manage fleets of all sizes in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '. It helps track vehicles, optimize routes, and improve operational efficiency.'
    ],
    [
        'question' => 'How does GoRide’s driver management software benefit my drivers in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s driver management software helps businesses in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' track driver performance, schedule shifts, and ensure compliance with regulations. It also allows drivers to receive real-time updates on ride requests, improving response times.'
    ],
    [
        'question' => 'Can GoRide’s taxi booking system handle high volumes of bookings in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'Yes, GoRide’s taxi booking system is built to handle a high volume of bookings, even during peak hours in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '. The system automatically assigns the nearest driver, ensuring timely service and improving customer satisfaction.'
    ],
    [
        'question' => 'How does GoRide’s cab booking software help businesses in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' reduce operational costs?',
        'answer' => 'GoRide’s cab booking software helps businesses in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' reduce operational costs by automating processes such as bookings, dispatching, and payment processing. This reduces the need for manual intervention and minimizes errors.'
    ],
    [
        'question' => 'How does GoRide’s taxi management software improve dispatch efficiency in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s taxi management software enhances dispatch efficiency in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' by automatically assigning rides to the nearest available drivers, reducing response times, and optimizing routes for the fastest service.'
    ],
    [
        'question' => 'What features does GoRide’s cab management software offer for businesses in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s cab management software offers features like vehicle tracking, maintenance scheduling, and driver performance tracking. These features help businesses in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' maintain a fleet that is both efficient and cost-effective.'
    ],
    [
        'question' => 'How can GoRide’s taxi booking system improve customer experience for passengers in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s taxi booking system improves customer experience in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' by offering a seamless booking process, real-time tracking of rides, and various payment options, ensuring convenience and reliability for passengers.'
    ],
    [
        'question' => 'Can GoRide’s cab booking software integrate with other systems in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ' while maintaining low costs and high performance?',
        'answer' => 'Yes, GoRide’s cab booking software is designed to integrate seamlessly with various systems in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', including CRM tools, customer databases, and other operational solutions. The software is optimized for low operational costs and high performance, ensuring a smooth and efficient experience for both passengers and businesses.'
    ],
    [
        'question' => 'How does GoRide’s taxi management software enhance payment convenience and settlement processes for drivers in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?',
        'answer' => 'GoRide’s taxi management software simplifies the payment and settlement process for drivers in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '. It includes automatic fare calculation, integrated payment gateways, and a streamlined settlement process, ensuring timely and accurate payouts. This reduces administrative workload and enhances financial transparency for both drivers and businesses.'
    ]
];
      
      
//       [
//     [
//         'question' => 'How does taxi cab dispatch software work in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /* 'What is Taxi Cab Dispatch Software?' */,
//         'answer' => 'Taxi cab dispatch software is a system designed to help manage and streamline the operation of taxi services. For example, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', the software enables taxi companies to handle ride requests, track vehicles, and process payments, all in real-time. It connects drivers, passengers, and dispatchers, ensuring efficient and smooth operations.'
//     ],
//     [
//         'question' => 'Can passengers schedule rides in advance from ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /* 'How Does Taxi Dispatch Software Work?' */,
//         'answer' => 'Taxi dispatch software works by allowing passengers to request rides via an app or phone call. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for instance, the software automatically identifies the passenger\'s location and assigns the nearest available driver based on GPS. The dispatcher monitors the ride progress and communicates with the driver through the system.'
//     ],
//     [
//         'question' => 'Can I offer discounts or promotions through the software from ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /* 'Is Taxi Dispatch Software compatible with different devices?' */,
//         'answer' => 'Yes, taxi dispatch software is compatible with various devices. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for example, drivers use mobile apps to receive ride requests, while dispatchers use desktop interfaces to manage the fleet.'
//     ],
//     [
//         'question' => 'How does Taxi Dispatch Software handle payment at ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /* 'Can passengers track their rides in real-time?' */,
//         'answer' => 'Yes, passengers can track their ride in real-time. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', passengers can see their driver\'s location, estimated time of arrival, and route via a mobile app, ensuring transparency and reducing uncertainty.'
//     ],
//     [
//         'question' => 'Can passengers track their rides in real-time from ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /* 'How does Taxi Dispatch Software handle payment?' */,
//         'answer' => 'Taxi dispatch software supports various payment methods, including credit cards, digital wallets, and cash. For instance, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', passengers can pay seamlessly through the app, while drivers receive automatic payment processing at the end of each trip.'
//     ],
//     [
//         'question' => 'Is Taxi Dispatch Software compatible with different devices and also works at ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /*  'Can I offer discounts or promotions through the software?' */,
//         'answer' => 'Yes, many taxi dispatch software platforms allow you to set up and offer promotional codes or discounts. For example, in ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', businesses can create special promotions for customers during holidays or events, enhancing customer retention and driving more bookings.'
//     ],
//     [
//         'question' => 'Does the taxi despatch software is offerdable at ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . '?' /*  'Can passengers schedule rides in advance?' */,
//         'answer' => 'Yes, passengers can schedule rides in advance using taxi dispatch software. In ' . (isset($seoTags['cityName']) ? ucfirst(strtolower($seoTags['cityName'])) : '') . ', for example, passengers can book rides for specific times and dates, ensuring that they have a taxi ready when they need it, such as for airport pickups or appointments.'
//     ]
// ];

                
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
