<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie; 
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
// Assuming you have a Movie model


class MovieController extends Controller
{
    //
    

    public function index()
    {
        $movies = Movie::paginate(10);
        return view('movies')->with(compact('movies'));
    }

    
    

    public function scrapeAndStoreMovies()
{
    try {
        $client = HttpClient::create();
        
        $response = $client->request('GET', 'https://www.imdb.com/chart/top');

        if ($response->getStatusCode() == 200) {
            $crawler = $client->request('GET', 'https://www.imdb.com/chart/top');

            $content = $response->getContent();
            $crawler = new Crawler($content);
         
            $movies = $crawler->filter('.ipc-metadata-list-summary-item__c')->each(function ($node) {
                $title = trim($node->filter('.ipc-title__text')->text());
                $year = intval(preg_replace('/[^0-9]/', '', $node->filter('.cli-title-metadata-item')->text()));
                $rating = floatval($node->filter('.ipc-rating-star')->text());
                $url = 'https://www.imdb.com' . $node->filter('.ipc-title a')->attr('href');
                
                return [
                    'title' => $title,
                    'year' => $year,
                    'rating' => $rating,
                    'url' => $url,
                ];
            });
    
            foreach ($movies as $movie) 
            {
                $existingMovie = Movie::where('title', $movie['title'])
                    ->where('year', $movie['year'])
                    ->first();
    
                if (!$existingMovie) {
                    Movie::create($movie);
                }
            }

            return response()->json(['message' => 'Movies scraped and stored successfully']);
        } else {
            return response()->json(['message' => 'Failed to fetch IMDb data.'], 500);
        }


        

        return response()->json(['message' => 'Movies scraped and stored successfully']);
    } catch (\Exception $e) {
        // Handle the exception gracefully
        \Log::error('Error while scraping movies: ' . $e->getMessage());
        
        return response()->json(['message' => 'An error occurred while scraping data. Please try again later.'], 500);
    }
}
}

//     public function scrapeAndStoreMovies()
// {
//     try {
//         $url = 'https://www.imdb.com/chart/top';
//         $response = Http::get($url);
//         dd($response);

//         if ($response->successful()) {
//             $html = $response->body();
//             $dom = new \simple_html_dom();
//             $dom->load($html);

//             $movies = [];

//             foreach ($dom->find('tbody.lister-list tr') as $row) {
//                 $title = trim($row->find('.titleColumn a')->plaintext);
//                 $year = intval(trim(str_replace(['(', ')'], '', $row->find('.titleColumn span.secondaryInfo')->plaintext)));
//                 $rating = floatval(trim($row->find('.imdbRating strong')->plaintext));
//                 $url = 'https://www.imdb.com' . $row->find('.titleColumn a')->getAttribute('href');

//                 $movies[] = [
//                     'title' => $title,
//                     'year' => $year,
//                     'rating' => $rating,
//                     'url' => $url,
//                 ];
//             }

//             foreach ($movies as $movie) {
//                 Movie::create($movie);
//             }

//             $dom->clear();
            
//             return response()->json(['message' => 'Movies scraped and stored successfully']);
//         } else {
//             return response()->json(['error' => 'Error scraping IMDb top chart'], 500);
//         }
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }


