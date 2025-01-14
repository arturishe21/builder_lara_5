<?php

namespace Vis\Builder\Http\Interfaces;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

interface ResourceInterface
{
    public function model(): Model;
}
