<?php

namespace App\Commands;

use DB;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class AddSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:add';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a site to the notifying list.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->ask('What is the id of the site?');
        $name = $this->ask('Please enter a display name');
        $slackUrl = $this->ask('Please enter a slack incoming webhook url');

        DB::table('sites')->updateOrInsert([
            'umami_id' => $id,
        ], [
            'name' => $name,
            'slack_url' => $slackUrl,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Successfully added site {$name}.");
    }
}
