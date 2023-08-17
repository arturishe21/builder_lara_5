<?php

namespace Vis\Builder\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Trend
{
    public string  $size = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
    protected int $defaultCountDays = 356;

    public function countByDays(string $model, string $field = 'id'): array
    {
        $result = $this->aggregate($model, $field, 'count');

        return [
            'labels' => Arr::pluck($result, 'x'),
            'values' => Arr::pluck($result, 'y')
        ];
    }

    public function avgByDays($model, $field = 'id'): array
    {
        return $this->aggregate($model, $field, 'avg');
    }

    public function maxByDays($model, $field = 'id'): array
    {
        return $this->aggregate($model, $field, 'max');
    }

    public function minByDays($model, $field = 'id'): array
    {
        return $this->aggregate($model, $field, 'min');
    }

    public function sumByDays($model, $field = 'id'): array
    {
       return $this->aggregate($model, $field, 'sum');
    }

    protected function aggregate(string $model, string $field, string $type): array
    {
        $dateRange = $this->currentRange();
        $dateRange[1] .= ' 23:59:59';

        return (new $model())
            ->select(DB::raw("date(created_at) as x, {$type}({$field}) as y"))
            ->whereBetween('created_at', $dateRange)
            ->orderBy('x')
            ->groupBy('x')
            ->get()
            ->toArray();
    }

    public function currentRange() : array
    {
        $from = request('from', date('Y-m-d', strtotime('-'.$this->defaultCountDays.' days')));
        $to = request('to', date('Y-m-d'));

        return [
            $from,
            $to,
        ];
    }
}
