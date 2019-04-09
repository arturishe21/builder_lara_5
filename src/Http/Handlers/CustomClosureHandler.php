<?php

namespace Vis\Builder\Handlers;

use Vis\Builder\JarboeController;

class CustomClosureHandler
{
    public $controller;
    private $functions = [];

    public function __construct($functions, JarboeController $controller)
    {
        $this->functions = $functions;
        $this->controller = $controller;
    }

    private function getClosure($name)
    {
        return isset($this->functions[$name]) ? $this->functions[$name] : false;
    }

    public function handle()
    {
        $closure = $this->getClosure('handle');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure();
        }
    }

    public function onGetValue($formField, array &$row, &$postfix)
    {
        $closure = $this->getClosure('onGetValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row, $postfix);
        }
    }

    public function onGetExportValue($formField, $type, array &$row, &$postfix)
    {
        $closure = $this->getClosure('onGetExportValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $type, $row, $postfix);
        }
    }

    public function onGetEditInput($formField, array &$row)
    {
        $closure = $this->getClosure('onGetEditInput');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row);
        }
    }

    public function onGetListValue($formField, array &$row)
    {
        $closure = $this->getClosure('onGetListValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row);
        }
    }

    public function onSelectField($formField, &$db)
    {
        $closure = $this->getClosure('onSelectField');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $db);
        }
    }

    public function onPrepareSearchFilters(array &$filters)
    {
        $closure = $this->getClosure('onPrepareSearchFilters');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($filters);
        }
    }

    public function onSearchFilter(&$db, $name, $value)
    {
        $closure = $this->getClosure('onSearchFilter');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($db, $name, $value);
        }
    }

    public function onViewFilter()
    {
        $closure = $this->getClosure('onViewFilter');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure();
        }
    }

    public function onUpdateRowResponse(array &$response)
    {
        $closure = $this->getClosure('onUpdateRowResponse');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($response);
        }
    }

    public function onInsertRowResponse(array &$response)
    {
        $closure = $this->getClosure('onInsertRowResponse');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($response);
        }
    }

    public function onDeleteRowResponse(array &$response)
    {
        $closure = $this->getClosure('onDeleteRowResponse');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($response);
        }
    }

    public function handleDeleteRow($id)
    {
        $closure = $this->getClosure('handleDeleteRow');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($id);
        }
    }

    public function handleInsertRow($values)
    {
        $closure = $this->getClosure('handleInsertRow');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($values);
        }
    }

    public function handleUpdateRow($values)
    {
        $closure = $this->getClosure('handleUpdateRow');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($values);
        }
    }

    public function onUpdateFastRowResponse(array &$response)
    {
        $closure = $this->getClosure('onUpdateFastRowResponse');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($response);
        }
    }

    public function onInsertRowData(array &$data)
    {
        $closure = $this->getClosure('onInsertRowData');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($data);
        }
    }

    public function onUpdateRowData(array &$data)
    {
        $closure = $this->getClosure('onUpdateRowData');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($data);
        }
    }

    public function onSearchCustomFilter($formField, &$db, $value)
    {
        $closure = $this->getClosure('onSearchCustomFilter');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $db, $value);
        }
    }

    public function onGetCustomValue($formField, array &$row, &$postfix)
    {
        $closure = $this->getClosure('onGetCustomValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row, $postfix);
        }
    }

    public function onGetCustomEditInput($formField, array &$row)
    {
        $closure = $this->getClosure('onGetCustomEditInput');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row);
        }
    }

    public function onGetCustomListValue($formField, array &$row)
    {
        $closure = $this->getClosure('onGetCustomListValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $row);
        }
    }

    public function onSelectCustomValue(&$db)
    {
        $closure = $this->getClosure('onSelectCustomValue');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($db);
        }
    }

    public function onFileUpload($file)
    {
        $closure = $this->getClosure('onFileUpload');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($file);
        }
    }

    public function onPhotoUpload($formField, $file)
    {
        $closure = $this->getClosure('onPhotoUpload');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($formField, $file);
        }
    }

    public function onPhotoUploadFromWysiwyg($file)
    {
        $closure = $this->getClosure('onPhotoUploadFromWysiwyg');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($file);
        }
    }

    public function onInsertButtonFetch($def)
    {
        $closure = $this->getClosure('onInsertButtonFetch');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($def);
        }
    }

    public function onUpdateButtonFetch($def)
    {
        $closure = $this->getClosure('onUpdateButtonFetch');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($def);
        }
    }

    public function onDeleteButtonFetch($def)
    {
        $closure = $this->getClosure('onDeleteButtonFetch');
        if ($closure) {
            $closure = $closure->bindTo($this);

            return $closure($def);
        }
    }
}
