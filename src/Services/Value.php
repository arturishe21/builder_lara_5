<?php

namespace Vis\Builder\Services;

class Value
{
    public string $size = 'col-xs-12 col-sm-6 col-md-3 col-lg-3';

    private int $range = 30;

    public function __construct()
    {
        $this->range = request('range', 30);
    }

    public function ranges() : array
    {
        return [
            30  => '30 Days',
            60  => '60 Days',
            365 => '365 Days',
        ];
    }

    public function count($model): array
    {
        $current = (new $model())->whereBetween('created_at', $this->currentRange())->count();
        $previous = (new $model())->whereBetween('created_at', $this->previousRange())->count();

        return $this->returnValue($current, $previous);
    }

    public function avg($model, string $field): array
    {
        $current = (new $model())->whereBetween('created_at', $this->currentRange())->avg($field);
        $previous = (new $model())->whereBetween('created_at', $this->previousRange())->avg($field);

        return $this->returnValue($current, $previous);
    }

    public function sum($model, string $field): array
    {
        $current = (new $model())->whereBetween('created_at', $this->currentRange())->sum($field);
        $previous = (new $model())->whereBetween('created_at', $this->previousRange())->sum($field);

        return $this->returnValue($current, $previous);
    }

    public function max($model, string $field): array
    {
        $current = (new $model())->whereBetween('created_at', $this->currentRange())->max($field);
        $previous = (new $model())->whereBetween('created_at', $this->previousRange())->max($field);

        return $this->returnValue($current, $previous);
    }

    public function min($model, string $field): array
    {
        $modelNew = new $model();
        $current = $modelNew->whereBetween('created_at', $this->currentRange())->min($field);
        $previous = $modelNew->whereBetween('created_at', $this->previousRange())->min($field);

        return $this->returnValue($current, $previous);
    }

    protected function returnValue(float $current, float $previous): array
    {
        return [
            'current'    => round($current, 2),
            'difference' => $this->getDifference($current, $previous),
        ];
    }

    protected function getDifference(float $current, float $previous): string
    {
        $difference = $previous ? ($current / $previous - 1) * 100 : $current * 100;

        $plusOrMinus = $current > $previous ? '+' : '';

        if ($previous === $current) {
            $plusOrMinus = '';
        }

        return $plusOrMinus . round($difference, 2) . '%';
    }

    protected function currentRange(): array
    {
        return [
            date('Y-m-d', strtotime('-' . $this->range . ' days')),
            date('Y-m-d'),
        ];
    }

    protected function previousRange(): array
    {
        return [
            date('Y-m-d', strtotime('-' . ($this->range * 2) . ' days')),
            date('Y-m-d', strtotime('-' . $this->range . ' days')),
        ];
    }
}
