<?php

namespace App\Support\Umami\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class StatsResult extends DataTransferObject
{
    public int $pageviews;
    public int $uniques;
    public int $bounces;
    public int $totaltime;
}