<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;

class ChangeRangeController extends Controller
{
    public function changeValue()
    {
        $model = request('model');

        return (new $model())->calculate();
    }
}
