<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function generateSitemap()
    {
        // Fetch active articles ordered by 'order' column
        $articles = DB::table('article')
            ->where('status', 'Active')
            ->orderBy('order', 'ASC')
            ->get();


  $articles2 = DB::table('dynamic_pages')
            // ->where('status', 'Active')
            ->where('deletes', '=', '0')
    ->where('status', '=', 'active')
       ->where('title', '!=', '')
 ->where('description', '!=', '')
     ->orderBy('id', 'ASC')
     ->get();
            
            
            // dd(  $articles2);
            
        // Define the current date/time for lastmod in ISO 8601 format
        $datetimes = now()->format('Y-m-d\TH:i:00+00:00');

        // Start XML response
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        // Add the static URL
        $xml .= "    <url>\n";
        $xml .= "        <loc>https://www.goride.run</loc>\n";
        $xml .= "        <lastmod>{$datetimes}</lastmod>\n";
        $xml .= "        <changefreq>hourly</changefreq>\n";
        $xml .= "        <priority>1</priority>\n";
        $xml .= "    </url>\n";

        // Add dynamic URLs from the database
        foreach ($articles as $article) {
            $loc = url('/') . '/' . htmlspecialchars($article->url);
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$loc}</loc>\n";
            $xml .= "        <lastmod>{$datetimes}</lastmod>\n";
            $xml .= "        <changefreq>hourly</changefreq>\n";
            $xml .= "        <priority>0.8</priority>\n";
            $xml .= "    </url>\n";
        }
        
        
        
         foreach ($articles2 as $article) {
            $loc = url('/') . '/'. htmlspecialchars($article->slug);
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$loc}</loc>\n";
            $xml .= "        <lastmod>{$datetimes}</lastmod>\n";
            $xml .= "        <changefreq>hourly</changefreq>\n";
            $xml .= "        <priority>0.8</priority>\n";
            $xml .= "    </url>\n";
        }
        
        $get_locations = DB::table('dynamic_pages_local')
            ->where([
                'country_code' => 'IN', 
                'status' => 'active', 
                'deletes' => '0'
            ])->get();
        
        
        foreach ($get_locations as $value) {
            $low_f = strtolower($value->name);
            $low_t = strtolower($value->to_place);
        
            $loc = url('/').'/car-rental/'.$low_f.'-to-'.$low_t.'-outstation-cab-service';
            
            $xml .= "    <url>\n";
            $xml .= "        <loc>{$loc}</loc>\n";
            $xml .= "        <lastmod>{$datetimes}</lastmod>\n";
            $xml .= "        <changefreq>hourly</changefreq>\n";
            $xml .= "        <priority>0.8</priority>\n";
            $xml .= "    </url>\n";
        }

        // Close the XML structure
        $xml .= "</urlset>";

        // Return XML response
        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }
}
