<?php

namespace Vis\Builder\Helpers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\View;
use Throwable;

class AnnotationHelper
{
    protected $annotation;

    public function __construct($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
    */
    public function handle()
    {
        if ($this->annotation instanceof View || $this->annotation instanceof ViewFactory) {
            try {
                $view = $this->annotation->render();
            } catch (Throwable $e) {
                $view = '';
            }
            return $view;
        } elseif (is_string($this->annotation)) {
            return $this->annotation;
        } elseif (is_callable($this->annotation)) {
            try {
                $test = '';
                //todo call to property as func
            } catch (Throwable $e) {
                return $e->getMessage();
            }
        }
    }
}