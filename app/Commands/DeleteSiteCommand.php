<?php

namespace App\Commands;

use DB;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DeleteSiteCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:delete {site}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete a site';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('sites')
            ->where('umami_id', $this->argument('site'))
            ->delete();

        $this->info("Site deleted.");
    }
}
