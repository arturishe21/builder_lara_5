<?php

namespace Vis\Builder\Http\Services;;

use Vis\Builder\Interfaces\Button;

class ButtonStrategy
{
   private $button;

   public function __construct(Button $button)
   {
       $this->button = $button;
   }

   public function render()
   {
       return $this->button->show();
   }
}