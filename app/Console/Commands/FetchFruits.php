<?php

namespace App\Console\Commands;

use App\Models\Fruit;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Utils;
use Illuminate\Console\Command;

class FetchFruits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-fruits {--notify : Whether notify admin about new fruits}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch all fruits from Fruityvice and notify if needed';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Fetch all fruits from Fruityvice.
        $client = new Client();
        $fruitIDs = Fruit::all(['fruit_id'])->pluck('fruit_id')->toArray();
        $hasNewFruit = false;
        $this->info("Fetching fruits from Fruityvice");
        try {
            $body = $client->get("https://fruityvice.com/api/fruit/all")->getBody();
            $result = Utils::jsonDecode($body);
            foreach ($result as $row) {
                if (!in_array($row->id, $fruitIDs)) {
                    $this->info("Found new fruit - {$row->name}");
                    $hasNewFruit = true;
                    $fruit = Fruit::query()->create([
                        'fruit_id' => $row->id,
                        'name' => $row->name,
                        'family' => $row->family,
                        'genus' => $row->genus,
                        'order' => $row->order,
                        'nutritions' => Utils::jsonEncode($row->nutritions),
                    ]);
                    $this->info("Added a new fruit: {$fruit->id}");
                }
            }
        } catch (GuzzleException $e) {
            $this->warn("Failed with network error: {$e->getMessage()}");
        }

        $notify = $this->option('notify');
        if ($notify && $hasNewFruit) {
            $this->info("Sending email to administrator");
        }
        $this->info("All done!!!");
    }
}
