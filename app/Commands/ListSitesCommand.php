<?php

namespace App\Commands;

use DB;
use LaravelZero\Framework\Commands\Command;

class ListSitesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:list {--show-webhook}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all configured sites';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->table(
            $this->option('show-webhook') ? ['Umami ID', 'Name', 'Webhook url'] : ['Umami ID', 'Name'],
            DB::table('sites')->get(
                $this->option('show-webhook')
                    ? ['umami_id', 'name', 'slack_url']
                    : ['umami_id', 'name']
            )->map(fn($site) => (array)$site)
        );
    }
}
