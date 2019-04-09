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
                $view = $e->getMessage();
            }

            return $view;
        } elseif (is_string($this->annotation)) {
            return $this->annotation;
        } elseif (is_callable($this->annotation)) {
            try {
                $annotation = $this->annotation;
                $this->annotation = $annotation();

                return $this->handle();
            } catch (Throwable $e) {
                return $e->getMessage();
            }
        }
    }

    public static function staticHandler($annotation)
    {
        return (new static($annotation))->handle();
    }
}
