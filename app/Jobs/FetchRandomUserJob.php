<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http; // For HTTP requests
use Illuminate\Support\Facades\Log;

class FetchRandomUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $response = Http::get('https://randomuser.me/api/');


        if ($response->successful()) {

            Log::info('Fetched random user:', $response->json());
        } else {
            Log::error('Failed to fetch random user.', ['status' => $response->status()]);
        }
    }
}
