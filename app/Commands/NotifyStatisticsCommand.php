<?php

namespace App\Commands;

use App\Notifications\StatisticsResultNotification;
use App\Support\Umami\UmamiClient;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Notification;
use LaravelZero\Framework\Commands\Command;

class NotifyStatisticsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:notify';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Notify statistics for all sites.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(UmamiClient $client)
    {
        $this->info('Notifying statistics for all sites...');

        DB::table('sites')
            ->get()
            ->each(function ($site) use ($client) {
                $this->line('Notifying ' . $site->name);

                $startDate = now()->addDay()->sub(config('umami.timerange'), 1)->startOfDay();
                $endDate = $startDate->clone()->add(config('umami.timerange'), 1)->subDay()->endOfDay();

                $comparisonStartDate = now()->addDay()->sub(config('umami.timerange'), 2)->startOfDay();
                $comparisonEndDate = $comparisonStartDate->clone()->add(config('umami.timerange'), 1)->subDay()->endOfDay();

                $data = $client->getStats(
                    id: $site->umami_id,
                    start: $startDate,
                    end: $endDate,
                );
                $dataReferrers = $client->getMetrics(
                    id: $site->umami_id,
                    type: 'referrer',
                    start: $startDate,
                    end: $endDate,
                );
                $comparisonData = $client->getStats(
                    id: $site->umami_id,
                    start: $comparisonStartDate,
                    end: $comparisonEndDate,
                );

                Notification::route('slack', $site->slack_url)
                    ->notify(new StatisticsResultNotification(
                        site: $site,
                        startDate: $startDate,
                        endDate: $endDate,
                        comparisonStartDate: $comparisonStartDate,
                        comparisonEndDate: $comparisonEndDate,
                        dataReferrers: $dataReferrers,
                        statistics: $data,
                        comparisonStatistics: $comparisonData
                    ));
            });

        $this->info('Notified all site statistics!');
    }
}
