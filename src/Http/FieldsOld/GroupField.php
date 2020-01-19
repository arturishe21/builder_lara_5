<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GroupField extends AbstractField
{
    public function isEditable()
    {
        return true;
    }

    // end isEditable

    public function onSearchFilter(&$db, $value)
    {
        $db->where($this->getFieldName(), 'LIKE', '%'.$value.'%');
    }

    // end onSearchFilter

    public function getEditInput($row = [])
    {
        $type = $this->getAttribute('type');
        $valueArray = $this->getValue($row);
        $filds = $this->getAttribute('filds');
        $section = [];

        if (count($valueArray)) {
            foreach ($valueArray as $nameVal => $val) {
                foreach ($val as $k => $res) {
                    $section[$k][$nameVal] = $res;
                }
            }

            foreach ($section as $k => $param) {
                foreach ($param as $nameParam => $valParam) {
                    if (isset($filds[$nameParam]['type'])) {
                        $nameClass = 'Vis\\Builder\\Fields\\'.ucfirst($filds[$nameParam]['type']).'Field';

                        $resultObjectFild = new $nameClass($nameParam, $filds[$nameParam], $this->options, $this->definition, $this->handler);
                        $sectionResult[$k][$nameParam] = $filds[$nameParam];

                        if (isset($filds[$nameParam]['tabs'])) {
                            $sectionResult[$k][$nameParam]['html'] = $resultObjectFild->getTabbedEditInput($param);
                        } else {
                            $sectionResult[$k][$nameParam]['html'] = $resultObjectFild->getEditInput([$nameParam => $valParam]);
                        }
                    }
                }
            }
        } else {
            foreach ($filds as $name => $fild) {
                $nameClass = 'Vis\\Builder\\Fields\\'.ucfirst($fild['type']).'Field';

                $resultObjectFild = new $nameClass($name, $fild, $this->options, $this->definition, $this->handler);
                $sectionResult[0][$name] = $fild;
                if (isset($fild['tabs'])) {
                    $sectionResult[0][$name]['html'] = $resultObjectFild->getTabbedEditInput();
                } else {
                    $sectionResult[0][$name]['html'] = $resultObjectFild->getEditInput();
                }
            }
        }

        $input = view('admin::tb.input_'.$type);
        $input->value = $valueArray;
        $input->name = $this->getFieldName();
        $input->rows = $sectionResult;
        $input->hideAdd = $this->getAttribute('hide_add');
        $input->hideDelete = $this->getAttribute('hide_delete');

        return $input->render();
    }

    // end getEditInput

    public function getAttribute($ident, $default = false)
    {
        if ($ident == 'hide_list') {
            return true;
        }

        return parent::getAttribute($ident, $default);
    }

    // end getAttribute

    private function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    public function getValue($row, $postfix = '')
    {
        $valueArray = [];

        if ($this->ifUseTable()) {
            $tableUse = $this->getAttribute('use_table')['table'];
            $fieldForeign = $this->getAttribute('use_table')['id'];

            if (isset($row['id'])) {
                $values = DB::table($tableUse)->where($fieldForeign, $row['id'])->get();

                foreach ($values as $arrValues) {
                    foreach ($arrValues as $name => $v) {
                        $valueArray[$name][] = $v;
                    }
                }

                unset($valueArray[$fieldForeign]);
            }
        } else {
            $valueJson = parent::getValue($row);

            if ($valueJson && $this->isJson($valueJson)) {
                $valueArray = json_decode($valueJson);
            }
        }

        return  $valueArray;
    }

    public function onSelectValue(&$db)
    {
        if (! $this->ifUseTable()) {
            return parent::onSelectValue($db);
        }
    }

    private function ifUseTable()
    {
        return $this->getAttribute('type') == 'group' && $this->getAttribute('use_table');
    }
}
