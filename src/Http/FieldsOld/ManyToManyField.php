<?php

namespace Vis\Builder\Fields;

use Illuminate\Support\Facades\DB;

/**
 * Class ManyToManyField
 * for manu2many field.
 */
class ManyToManyField extends AbstractField
{
    /**
     * @return bool
     */
    public function isEditable()
    {
        return true;
    }

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
        if ($value) {
            $mtmTable = $this->getAttribute('mtm_table');
            $mtmExternalTable = $this->getAttribute('mtm_external_table');
            $mtmKeyField = $this->getAttribute('mtm_key_field');
            $mtmExternalKeyField = $this->getAttribute('mtm_external_key_field');
            $mtmExternalForeignKeyField = $this->getAttribute('mtm_external_foreign_key_field');
            $mtmExternalValueField = $this->getAttribute('mtm_external_value_field');

            $searchResult = DB::table($mtmTable)
                ->leftJoin($mtmExternalTable, $mtmExternalTable.'.'.$mtmExternalForeignKeyField, '=', $mtmTable.'.'.$mtmExternalKeyField)
                ->where($mtmExternalValueField, 'like', '%'.$value.'%')
                ->select($mtmKeyField)->pluck($mtmKeyField);

            $db->whereIn($this->definition['db']['table'].'.id', $searchResult);
        }
    }

    /**
     * @param $values
     * @param $id
     */
    public function onPrepareRowValues($values, $id)
    {
        // we get comma separated values in string if select2 ajax search
        $values = is_array($values) ? $values : explode(',', $values);

        $delete = DB::table($this->getAttribute('mtm_table'))
            ->where($this->getAttribute('mtm_key_field'), $id);

        if ($this->getAttribute('mtm_external_model')) {
            $delete = $this->getAttribute('mtm_external_model_custom') ?
                $delete->where($this->getAttribute('mtm_external_model'), $this->getAttribute('mtm_external_model_custom')) :
                $delete->where($this->getAttribute('mtm_external_model'), $this->definition['options']['model']);
        }

        $delete->delete();

        $data = [];
        if ($this->getAttribute('show_type') == 'extra') {
            foreach ($values as $info) {
                $temp = [
                    $this->getAttribute('mtm_key_field')          => $id,
                    $this->getAttribute('mtm_external_key_field') => $info['id'],
                ];

                $extraFields = $this->getAttribute('extra_fields', []);
                foreach ($extraFields as $fieldName => $fieldInfo) {
                    $temp[$fieldName] = $info[$fieldName];
                }

                $data[] = $temp;
            }
        } else {
            $values = array_filter($values);
            // HACK: in checkbox we have id as key of element, in select - as value
            $isInValueElement = ($this->getAttribute('show_type', 'checkbox') == 'select2' || $this->getAttribute('show_type', 'checkbox') == 'select3' || $this->getAttribute('show_type', 'checkbox') == 'select_tree');
            foreach ($values as $key => $val) {
                $externalID = $isInValueElement ? $val : $key;
                $data[$key] = [
                    $this->getAttribute('mtm_key_field')          => $id,
                    $this->getAttribute('mtm_external_key_field') => $externalID,
                ];

                if ($this->getAttribute('mtm_external_model')) {
                    $data[$key][$this->getAttribute('mtm_external_model')] =
                        $this->getAttribute('mtm_external_model_custom') ?
                            $this->getAttribute('mtm_external_model_custom') :
                            $this->definition['options']['model'];
                }
            }
        }

        if ($data) {
            DB::table($this->getAttribute('mtm_table'))->insert($data);
        }
    }

    /**
     * @param $db
     */
    public function onSelectValue(&$db)
    {
        // HACK: we dont need this method to be called for many2many field
    }

    /**
     * @param $row
     * @param string $postfix
     *
     * @return bool|mixed
     */
    public function getValue($row, $postfix = '')
    {
        if ($this->hasCustomHandlerMethod('onGetValue')) {
            $res = $this->handler->onGetValue($this, $row, $postfix);
            if ($res) {
                return $res;
            }
        }

        $assocTable = $this->getAttribute('mtm_table');
        $extTable = $this->getAttribute('mtm_external_table');
        $assocKeyField = $this->getAttribute('mtm_key_field');
        $assocExtKeyField = $this->getAttribute('mtm_external_key_field');
        $extKeyField = $this->getAttribute('mtm_external_foreign_key_field');
        $extValueField = $this->getAttribute('mtm_external_value_field');

        return DB::table($assocTable)
            ->join($extTable, $assocTable.'.'.$assocExtKeyField, '=', $extTable.'.'.$extKeyField)
            ->where($assocTable.'.'.$assocKeyField, $row['id'])->value($extValueField);
    }

    /**
     * @param $row
     *
     * @return bool|string
     */
    public function getListValue($row)
    {
        if ($this->hasCustomHandlerMethod('onGetListValue')) {
            $res = $this->handler->onGetListValue($this, $row);
            if ($res) {
                return $res;
            }
        }

        return implode(', ', $this->getRelatedExternalFieldOptions($row));
    }

    /**
     * @param array $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function getEditInput($row = [])
    {
        if ($this->hasCustomHandlerMethod('onGetEditInput')) {
            $res = $this->handler->onGetEditInput($this, $row);
            if ($res) {
                return $res;
            }
        }

        $showType = $this->getAttribute('show_type', 'checkbox');

        if ($showType == 'extra') {
            return $this->getEditInputWithExtra($row);
        }
        if ($showType == 'select2' && $this->getAttribute('select2_search')) {
            return $this->getEditInputSelectWithAjaxSearch($row);
        }

        $input = view('admin::tb.input_many2many_'.$showType);
        $input->selected = [];
        if ($row) {
            $input->selected = $this->getRelatedExternalFieldOptions($row);
        }

        $input->link = $this->getAttribute('with_link');
        $input->name = $this->getFieldName();
        $input->divide = $this->getAttribute('divide_columns', 2);
        $input->depends_on = $this->getAttribute('mtm_depends_on');
        $input->depends_on_url = $this->getAttribute('mtm_depends_on_url');

        if ($showType == 'select_tree') {
            $input->options = $this->getFieldOptionForTree();
        } else {
            $input->options = $this->doDivideOnParts(
                $this->getAllExternalFieldOptions(),
                $this->getAttribute('divide_columns', 2)
            );
        }

        return $input->render();
    }

    /**
     * @param $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    private function getEditInputSelectWithAjaxSearch($row)
    {
        $input = view('admin::tb.input_many2many_select2_search');

        $data = [];
        if ($row) {
            $selected = $this->getRelatedExternalFieldOptions($row);
            foreach ($selected as $id => $title) {
                $data[] = [
                    'id'   => $id,
                    'name' => $title,
                ];
            }
        }
        $input->selected = json_encode($data);

        $input->postfix = $row ? '_e' : '_c';
        $input->link = $this->getAttribute('with_link');
        $input->name = $this->getFieldName();
        $input->search = $this->getAttribute('select2_search');
        $input->row = $row;
        $input->insert = $this->getAttribute('insert');
        $input->attributes = json_encode($this->attributes);

        return $input->render();
    }

    /**
     * @param $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    private function getEditInputWithExtra($row)
    {
        $input = view('admin::tb.input_many2many_extra');

        $input->selected = [];
        if ($row) {
            $input->selected = $this->getRelatedExternalFieldOptions($row, true);
        }

        $input->postfix = $row ? '_e' : '_c';
        $input->name = $this->getFieldName();
        $input->options = $this->getAllExternalFieldOptions(true);
        $input->extra = $this->getAttribute('extra_fields');

        return $input->render();
    }

    /**
     * @param $array
     * @param $segmentCount
     *
     * @return array
     */
    private function doDivideOnParts($array, $segmentCount)
    {
        $dataCount = count($array);
        if ($dataCount === 0) {
            // HACK: when there is no many2many options
            return [[]];
        }

        $segmentLimit = ceil($dataCount / $segmentCount);

        return array_chunk($array, $segmentLimit, true);
    }

    /**
     * @param $row
     * @param bool $isGetAll
     *
     * @return array|\Illuminate\Database\Query\Builder
     */
    protected function getRelatedExternalFieldOptions($row, $isGetAll = false)
    {
        $keyField = $this->getAttribute('mtm_table').'.'.$this->getAttribute('mtm_external_key_field');
        $valueField = $this->getAttribute('mtm_external_table').'.'.$this->getAttribute('mtm_external_value_field');
        $externalTable = $this->getAttribute('mtm_external_table');
        $externalForeignKey = $externalTable.'.'.$this->getAttribute('mtm_external_foreign_key_field');
        $addFieldQuery = $this->getAttribute('mtm_external_select_field', true);

        $options = DB::table($this->getAttribute('mtm_table'))->select($keyField);

        if ($addFieldQuery) {
            $options->addSelect($valueField);
        }

        if ($isGetAll) {
            $options->addSelect($this->getAttribute('mtm_table').'.*');
        }

        $options->join($externalTable, $keyField, '=', $externalForeignKey);

        $this->additionalWhere($options);

        $options->where($this->getAttribute('mtm_key_field'), $row['id']);

        if ($this->getAttribute('mtm_external_model')) {
            if ($this->getAttribute('mtm_external_model_custom')) {
                $options->where($this->getAttribute('mtm_external_model'), $this->getAttribute('mtm_external_model_custom'));
            } else {
                $options->where($this->getAttribute('mtm_external_model'), $this->definition['options']['model']);
            }
        }

        $externalOrder = $this->getAttribute('mtm_external_order');
        if ($externalOrder) {
            if (is_callable($externalOrder)) {
                $externalOrder($options);
            } else {
                foreach ($externalOrder as $key => $opt) {
                    $options->orderBy($key, $opt);
                }
            }
        }
        if ($this->getAttribute('show_type') == 'select3') {
            $res = $options->orderBy($this->getAttribute('mtm_table').'.id', 'asc')->get();
        } else {
            $res = $options->get();
        }

        $options = [];
        foreach ($res as $opt) {
            $opt = (array) $opt;
            $id = $opt[$this->getAttribute('mtm_external_key_field')];
            $value = $opt[$this->getAttribute('mtm_external_value_field')];

            if ($isGetAll) {
                $options[$id] = [
                    'value' => $value,
                    'info'  => $opt,
                ];
            } else {
                $options[$id] = $value;
            }
        }

        return $options;
    }

    /**
     * @param bool $isGetAll
     *
     * @return array|\Illuminate\Database\Query\Builder
     */
    protected function getAllExternalFieldOptions($isGetAll = false)
    {
        $valueField = $this->getAttribute('mtm_external_table').'.'.$this->getAttribute('mtm_external_value_field');
        $externalTable = $this->getAttribute('mtm_external_table');
        $externalForeignKey = $externalTable.'.'.$this->getAttribute('mtm_external_foreign_key_field');
        $addFieldQuery = $this->getAttribute('mtm_external_select_field', true);

        $options = DB::table($externalTable);

        if (! $isGetAll) {
            $options->select($externalForeignKey);

            if ($addFieldQuery) {
                $options->addSelect($valueField);
            }
        }

        $this->additionalWhere($options);

        $this->externalTableOrder($options);

        $res = $options->get();
        $options = [];
        foreach ($res as $opt) {
            $opt = (array) $opt;
            $id = $opt[$this->getAttribute('mtm_external_foreign_key_field')];
            $value = $opt[$this->getAttribute('mtm_external_value_field')];

            if ($isGetAll) {
                $options[$id] = [
                    'value' => $value,
                    'info'  => $opt,
                ];
            } else {
                $options[$id] = $value;
            }
        }

        return $options;
    }

    /**
     * @return mixed
     */
    public function getFieldOptionForTree()
    {
        $params = $this->getAttribute('type_tree_params');
        $model = $params['model'];
        $options = $model::where('parent_id', $params['start_id_folder']);

        $this->additionalWhere($options);

        return $options->orderBy('parent_id')->get();
    }

    /**
     * @param $query
     * @param $limit
     * @param $page
     *
     * @return array
     */
    public function getAjaxSearchResult($query, $limit, $page)
    {
        if ($this->hasCustomHandlerMethod('onGetAjaxSearchResult')) {
            $res = $this->handler->onGetAjaxSearchResult($this, $query, $limit, $page);
            if ($res) {
                return $res;
            }
        }

        $results = DB::table($this->getAttribute('mtm_external_table'))
            ->select('id', $this->getAttribute('mtm_external_value_field'))
            ->where($this->getAttribute('mtm_external_value_field'), 'LIKE', '%'.$query.'%')
            ->take($limit)
            ->skip(($limit * $page) - $limit);

        $this->additionalWhere($results);

        $this->externalTableOrder($results);

        $results = $results->get();
        $results = $results ?: [];

        $res = [];

        foreach ($results as $result) {
            $result = (array) $result;
            $res[] = [
                'id'   => $result['id'],
                'name' => $result[$this->getAttribute('mtm_external_value_field')],
            ];
        }

        return [
            'results' => $res,
            'more'    => $res && ! empty($res),
            'message' => '',
        ];
    }

    /**
     * @param $options
     */
    private function externalTableOrder(&$options)
    {
        if ($this->getAttribute('mtm_external_table_order')) {
            foreach ($this->getAttribute('mtm_external_table_order') as $key => $opt) {
                $options->orderBy($key, $opt);
            }
        }
    }

    /**
     * @param $results
     */
    private function additionalWhere(&$results)
    {
        $additionalWheres = $this->getAttribute('additional_where');

        if (is_array($additionalWheres)) {
            foreach ($this->getAttribute('additional_where') as $key => $opt) {
                if ($opt['sign'] == 'in') {
                    $results->whereIn($key, $opt['value']);
                } else {
                    $results->where($key, $opt['sign'], $opt['value']);
                }
            }
        }

        if (is_object($additionalWheres)) {
            $additionalWheres($results);
        }
    }
}
