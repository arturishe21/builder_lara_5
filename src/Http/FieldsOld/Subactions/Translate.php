<?php

namespace Vis\Builder\Fields\Subactions;

class Translate extends AbstractSubaction
{
    public function fetch()
    {
        [$from, $to] = $this->getAttribute('locales');
        $caption = $this->getAttribute('caption');

        $data = compact('caption', 'to', 'from');

        return \view('admin::tb.subactions.translate', $data)->render();
    }

    // end fetch
}
