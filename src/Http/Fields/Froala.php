<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Str;

class Froala extends Field
{
    private $toolbar = "fullscreen, bold, italic, underline, strikeThrough, subscript, superscript, fontFamily, fontSize,  color, emoticons, inlineStyle, paragraphStyle,  paragraphFormat, align, formatOL, formatUL, outdent, indent, quote, insertHR, insertLink, insertImage, insertVideo, insertFile, insertTable, undo, redo, clearFormatting, selectAll, html";
    private $options = '';

    public function toolbar($value)
    {
        $this->toolbar = $value;

        return $this;
    }

    public function options($collection)
    {
        $this->options = $collection;

        return $this;
    }

    public function getToolbar()
    {
        return $this->toolbar;
    }

    public function getOptions()
    {
        return json_encode($this->options);
    }

    public function getValueForList($definition)
    {
        $arrayValue = json_decode($this->getValue());

        $value = $arrayValue->{$this->locale} ?? $this->getValue();

        return  Str::limit(strip_tags($value), 70);
    }
}
