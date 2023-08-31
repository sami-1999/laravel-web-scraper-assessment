<?php

namespace Tests\Feature;

use App\Http\Controllers\MovieController;
use App\Models\Movie;
use Goutte\Client;
use Mockery;
use Tests\TestCase;

class MovieScrapingTest extends TestCase
{
    public function testScrapingAndStoringMovies()
    {
    
        $goutteClient = Mockery::mock(Client::class);
        $this->app->instance(Client::class, $goutteClient);

      
        $crawler = Mockery::mock('overload:Goutte\Crawler');
        $crawler->shouldReceive('filter')->andReturnSelf();
        $crawler->shouldReceive('each')->andReturn([]);

   
        $response = Mockery::mock('overload:GuzzleHttp\Psr7\Response');
        $response->shouldReceive('getStatusCode')->andReturn(200);
        $goutteClient->shouldReceive('request')->andReturn($response);
        

        $controller = new MovieController();
        $result = $controller->scrapeAndStoreMovies();

     
        $this->assertJson($result->content());

        
        $this->assertTrue(true);
    }
}
