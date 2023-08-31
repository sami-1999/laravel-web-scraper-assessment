<?php

namespace App\Console\Commands;
use App\Http\Controllers\MovieController;
use Illuminate\Console\Command;

class ScrapeMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape and store movies from IMDb';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       
       
        // return 0;
        $movieController = new MovieController();
        $movieController->scrapeAndStoreMovies();
        \Log::info('Scrape Movies command executed.');

    }
}
