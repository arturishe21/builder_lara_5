<?php

namespace Vis\Builder\Helpers\Traits;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\App;

trait QuickEditTrait
{
    public function edit($field)
    {
        $user = Sentinel::getUser();
        $value = $this->t($field);

        if (Sentinel::check() && $user->hasAccess(['admin.access'])) {

            $typeEdit = $this->isHtmlBlcock($value) ? 'class="edit_for_admin_html" ' : 'contenteditable="true" class="edit_for_admin"';

            return '<div
                '.$typeEdit.'
                data-model="'.get_class($this).'"
                data-field-name="'.$field.'"
                data-language="'.App::getLocale().'"
                data-id="'.$this->id.'"
                >'.$value.'</div>';

        } else {
            return $value;
        }
    }

    private function isHtmlBlcock($value)
    {
        return strpos($value, '<p') !==false;
    }
}
