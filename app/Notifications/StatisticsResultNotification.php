<?php

namespace App\Notifications;

use App\Support\Umami\DataTransferObjects\StatsResult;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class StatisticsResultNotification extends Notification
{
    public function __construct(
        protected $site,
        protected Carbon $startDate,
        protected Carbon $endDate,
        protected Carbon $comparisonStartDate,
        protected Carbon $comparisonEndDate,
        protected array $dataReferrers,
        protected StatsResult $statistics,
        protected StatsResult $comparisonStatistics,
    )
    {
    }

    public function via(): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $range = config('umami.timerange');

        $growth = $this->getGrowth();
        $notification = "This {$range}, *{$this->site->name}* had *{$this->statistics->pageviews}* users.\n";
        $growthPerception = $growth > 0 ? 'better' : 'worse';
        $growthNumber = round(abs($growth));

        $notification .= "That's *{$growthNumber}%* {$growthPerception} than last {$range}.";

        return (new SlackMessage())
            ->content($notification)
            ->attachment(function (SlackAttachment $attachment) use ($growth) {
                $formattedStartDate = $this->startDate->isSameDay($this->endDate)
                    ? $this->startDate->format('d. M')
                    : $this->startDate->format('d. M') . ' - ' . $this->endDate->format('d. M');
                $formattedComparisonDate = $this->comparisonStartDate->isSameDay($this->comparisonEndDate)
                    ? $this->comparisonStartDate->format('d. M')
                    : $this->comparisonStartDate->format('d. M') . ' - ' . $this->comparisonEndDate->format('d. M');

                $attachment
                    ->color($growth > 0 ? 'good' : 'danger')
                    ->fields([
                        $formattedStartDate . ' (' . ($growth > 0 ? '↑' : '↓') . ')' => $this->statistics->pageviews,
                        $formattedComparisonDate => $this->comparisonStatistics->pageviews,
                    ]);
            })
            ->attachment(function (SlackAttachment $attachment) {
                $topReferrers = array_slice($this->dataReferrers, 0, 5);
                $attachment
                    ->title('Top Referrers')
                    ->fields($topReferrers);
            });
    }

    protected function getGrowth(): float|int
    {
        $previousUsers = $this->comparisonStatistics->pageviews;

        return (100 - ($this->statistics->pageviews / $previousUsers * 100)) * -1;
    }

}