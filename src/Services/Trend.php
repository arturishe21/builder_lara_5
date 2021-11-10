<?php

namespace Vis\Builder\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Trend
{
    public $size = 'col-xs-12 col-sm-12 col-md-12 col-lg-12';
    protected $defaultCountDays = 356;

    public function countByDays(string $model, string $field = 'id')
    {
        $result = $this->aggregate($model, $field, 'count');

        return [
            'labels' => Arr::pluck($result, 'x'),
            'values' => Arr::pluck($result, 'y')
        ];
    }

    public function avgByDays($model, $field = 'id')
    {
        $result = $this->aggregate($model, $field, 'avg');

        return $this->returnResult($result);
    }

    public function maxByDays($model, $field = 'id')
    {
        $result = $this->aggregate($model, $field, 'max');

        return $this->returnResult($result);
    }

    public function minByDays($model, $field = 'id')
    {
        $result = $this->aggregate($model, $field, 'min');

        return $this->returnResult($result);
    }

    public function sumByDays($model, $field = 'id')
    {
        $result = $this->aggregate($model, $field, 'sum');

        return $this->returnResult($result);
    }

    protected function aggregate(string $model, string $field, string $type) : array
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

    protected function returnResult($result)
    {
        return $result;
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
