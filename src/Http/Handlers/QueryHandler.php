<?php

namespace Vis\Builder\Handlers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Vis\Builder\Fields\AbstractField;
use Vis\Builder\JarboeController;

class QueryHandler
{
    protected $cache;
    protected $model;
    protected $table;
    protected $query;
    protected $controller;
    protected $definition;
    protected $extendsTable;
    protected $definitionName;
    protected $extendsTableId;
    protected $extendsFields = [];
    protected $extendsFieldsModel = [];

    public function __construct(JarboeController $controller)
    {
        $this->controller = $controller;
        $this->definition = $controller->getDefinition();
        $this->definitionName = $this->definition->getName();

        $this->cache = $this->definition->getCacheTags() ?: '';
        $this->model = $this->definition->getModel();
        $this->table = $this->model->getTable();

        if (!empty($this->definition->getExtendsTable())) {
            foreach ($this->definition->getExtendsTable() as $extend) {
                $table = $extend['table'];
                $this->extendsTable[$table] = $table;

                $this->extendsTableId[$table] = isset($extend['id']) ? $extend['id'] : $this->dbName.'_id';

                if (isset($extend['polymorph'])) {
                    $this->extendsFieldsModel[$table] = $extend['polymorph'];
                }
            }
        }
    }

    public function getRows($isPagination = true, $isUserFilters = true, $betweenWhere = [], $isSelectAll = false)
    {
        $modelName = $this->model;

        if (!$modelName) {
            return [];
        }

        $this->query = $modelName->newQuery();

        $this->prepareSelectValues();

        if ($isSelectAll) {
            $this->query->addSelect($this->table.'.*');
        }

        $this->prepareFilterValues();

        if ($isUserFilters) {
            $this->onSearchFilterQuery();
        }

        if ($this->extendsTable) {
            $joinedTables = collect($this->query->getQuery()->joins)->pluck('table');
            foreach ($this->extendsTable as $table) {
                if ($joinedTables->contains($table)) {
                    continue;
                }

                $this->query->leftJoin($table, function ($q) use ($table) {
                    $q->on("{$table}.{$this->extendsTableId[$table]}", '=', "{$this->table}.id");

                    if (isset($this->extendsFieldsModel[$table])) {
                        $q->where("{$table}.{$this->extendsFieldsModel[$table]}", '=', get_class($this->model));
                    }
                });
            }
        }

        $this->dofilter();

        $sessionPath = 'table_builder.'.$this->definitionName.'.order';
        $order = Session::get($sessionPath, []);

        if ($order && $isUserFilters) {
            $this->query->orderBy($this->table.'.'.$order['field'], $order['direction']);
        } else {
            $order = $this->definition->getOrder();

            $this->query->orderBy($this->table.'.'.$order[0], $order[1] ?? 'asc');
        }

        if ($betweenWhere) {
            $betweenField = $betweenWhere['field'];
            $betweenValues = $betweenWhere['values'];

            $this->query->whereBetween($betweenField, $betweenValues);
        }

        if ($isPagination) {
            $perPage = $this->getPerPageAmount($this->definition->getPaginationQuantityButtons());

            return $this->query->paginate($perPage);
        }

        return $this->query->get();
    }

    private function dofilter()
    {
        if (request()->has('filter')) {
            $filters = request('filter');

            foreach ($filters as $nameField => $valueField) {
                if ($valueField) {
                    $this->query->where($nameField, $valueField);
                }
            }
        }
    }

    public function getPerPageAmount($info)
    {
        if (!is_array($info)) {
            return $info;
        }

        return session('table_builder.'.$this->definitionName.'.per_page', array_keys($info)[0] ?? 20);
    }

    protected function prepareFilterValues()
    {
        $filters = $this->definition->getFilters();

        if (is_callable($filters)) {
            $filters($this->query);

            return;
        }

        foreach ($filters as $name => $field) {
            $this->query->where($name, $field['sign'], $field['value']);
        }
    }

    protected function doPrependFilterValues(&$values)
    {
        $filters = isset($this->definition['filters']) ? $this->definition['filters'] : [];
        if (is_callable($filters)) {
            return;
        }

        foreach ($filters as $name => $field) {
            $values[$name] = $field['value'];
        }
    }

    protected function prepareSelectValues()
    {
        $this->query->select($this->table.'.id');

        if ($this->definition->isSortable()) {
            $this->query->addSelect($this->table.'.priority');
        }

        $fields = $this->controller->getFields();

        foreach ($fields as $field) {
            $field->onSelectValue($this->query);
        }
    }

    public function getRow($id)
    {
        $this->query = $this->model->newQuery();

        if ($this->extendsTable) {
            foreach ($this->extendsTable as $table) {
                $this->query->leftJoin($table, function ($q) use ($table) {
                    $q->on("{$table}.{$this->extendsTableId[$table]}", '=', "{$this->table}.id");

                    if (isset($this->extendsFieldsModel[$table])) {
                        $q->where("{$table}.{$this->extendsFieldsModel[$table]}", '=', get_class($this->model));
                    }
                });
            }
        }

        $this->prepareSelectValues();

        return $this->query->where($this->table.'.id', $id)->first();
    }

    protected function onSearchFilterQuery()
    {
        foreach (session('table_builder.'.$this->definitionName.'.filters', []) as $name => $value) {
            if ($this->controller->hasCustomHandlerMethod('onSearchFilter')) {
                $res = $this->controller->getCustomHandler()->onSearchFilter($this->query, $name, $value);

                if ($res) {
                    continue;
                }
            }

            $this->controller->getField($name)->onSearchFilter($this->query, $value);
        }
    }

    public function updateRow($values)
    {
        $this->clearCache();

        if ($this->controller->hasCustomHandlerMethod('handleUpdateRow')) {
            $res = $this->controller->getCustomHandler()->handleUpdateRow($values);

            if ($res) {
                return $res;
            }
        }

        $updateData = $this->getRowQueryValues($values);
        $updateDataRes = [];

        $model = $this->model;
        $this->checkFields($updateData);

        if ($this->controller->hasCustomHandlerMethod('onUpdateRowData')) {
            $this->controller->getCustomHandler()->onUpdateRowData($updateData, $values);
        }

        $this->doValidate($updateData);

        $modelObj = $model::find($values['id']);

        if (method_exists($modelObj, 'setFillable')) {
            $modelObj->setFillable(array_keys($updateData));
        }

        foreach ($updateData as $field => $data) {
            if (isset($this->definition['fields'][$field]) && ! isset($this->definition['fields'][$field]['tabs'])) {
                $updateDataRes[$field] = $this->getData($data);
            } else {
                $this->getDataTabs($updateDataRes, $updateData, $field);
            }
        }

        $modelObj->update($updateDataRes);

        foreach ($this->controller->getPatterns() as $pattern) {
            $pattern->update($values, $values['id']);
        }

        $fields = $this->controller->getFields();
        foreach ($fields as $field) {
            if (preg_match('~^many2many~', $field->getFieldName())) {
                $this->onManyToManyValues($field->getFieldName(), $values, $values['id']);
            }

            $this->updateGroupIfUseTable($field, $values['id']);
        }

        $this->updateExtendsTable($values['id']);

        $res = [
            'id'     => $values['id'],
            'values' => $updateData,
        ];
        if ($this->controller->hasCustomHandlerMethod('onUpdateRowResponse')) {
            $this->controller->getCustomHandler()->onUpdateRowResponse($res);
        }

        return $res;
    }

    private function getData($data)
    {
        if (is_array($data)) {
            return json_encode($data);
        }

        return $data;
    }

    private function getDataTabs(&$updateDataRes, $updateData, $field)
    {
        if (isset($this->definition['fields'][$field]['tabs'])) {
            foreach ($this->definition['fields'][$field]['tabs'] as $tab) {
                $updateDataRes[$field.$tab['postfix']] = $this->getTabsDataField($updateData, $field, $tab);
            }
        }
    }

    private function getTabsDataField($updateData, $field, $tab)
    {
        if ($updateData[$field.$tab['postfix']]) {
            return $updateData[$field.$tab['postfix']];
        }

        if (config('builder.translate_cms.auto_translate') === false) {
            return '';
        }

        if ($updateData[$field] && $this->definition['fields'][$field]['type'] != 'image') {
            $translateText = $this->generateTranslation($updateData[$field], ltrim($tab['postfix'], '_'));

            return $translateText ?: '';
        }

        return '';
    }

    private function generateTranslation($phrase, $thisLang)
    {
        try {
            $langsDef = config('translations.config.def_locale');

            $lang = str_replace('ua', 'uk', $thisLang);
            $langsDef = str_replace('ua', 'uk', $langsDef);

            $translator = new \Yandex\Translate\Translator(config('builder.translate_cms.api_yandex_key'));
            $translation = $translator->translate($phrase, $langsDef.'-'.$lang);

            if (isset($translation->getResult()[0])) {
                return $translation->getResult()[0];
            }
        } catch (\Yandex\Translate\Exception $e) {
        }
    }

    private function updateGroupIfUseTable($field, $id)
    {
        if ($field->getAttribute('use_table') && $field->getAttribute('type') == 'group') {
            $nameField = $field->getFieldName();
            $group = request($nameField);
            $tableUse = $field->getAttribute('use_table')['table'];
            $fieldForeign = $field->getAttribute('use_table')['id'];
            DB::table($tableUse)->where($fieldForeign, $id)->delete();

            foreach ($group as $name => $arrayValue) {
                foreach ($arrayValue as $k => $item) {
                    $resultArray[$k][$fieldForeign] = $id;
                    $resultArray[$k][$name] = $item;
                }
            }

            if (isset($resultArray)) {
                DB::table($tableUse)->insert($resultArray);
            }
        }
    }

    public function updateExtendsTable($id)
    {
        if (count($this->extendsFields)) {
            foreach ($this->extendsFields as $tableEx => $fields) {
                $table = DB::table($tableEx);

                $tableExField = $this->extendsTableId[$tableEx];

                $hasExistRecord = DB::table($tableEx)
                    ->where($tableExField, $id);

                if (isset($this->extendsFieldsModel[$tableEx])) {
                    $fields[$this->extendsFieldsModel[$tableEx]] = $this->model;
                    $hasExistRecord = $hasExistRecord->where($this->extendsFieldsModel[$tableEx], $this->model);
                }

                $hasExistRecord = $hasExistRecord->first();

                $fields[$tableExField] = $id;

                if ($hasExistRecord) {
                    $table->where('id', $hasExistRecord->id)->update($fields);
                } else {
                    $table->insert($fields);
                }
            }
        }
    }

    public function cloneRow($id)
    {
        $this->clearCache();

        if ($this->controller->hasCustomHandlerMethod('handleCloneRow')) {
            $res = $this->controller->getCustomHandler()->handleCloneRow($id);

            if ($res) {
                return $res;
            }
        }

        $page = (array) $this->model->newQuery()->where('id', $id)->first(['*']);

        Event::fire('table.clone', [$this->table, $id]);

        unset($page['id']);

        $newId = $this->model->newQuery()->insertGetId($page);

        $this->cloneExtendsTables($id, $newId);

        return [
            'id'     => $id,
            'status' => $page,
        ];
    }

    private function cloneExtendsTables($id, $newId)
    {
        if (isset($this->extendsTable) && count($this->extendsTable)) {
            foreach ($this->extendsTable as $table) {
                $page = (array) DB::table($table)->where($this->extendsTableId[$table], $id)->select('*')->first();
                unset($page['id']);
                $page[$this->extendsTableId[$table]] = $newId;
                DB::table($table)->insertGetId($page);
            }
        }
    }

    public function deleteRow($id)
    {
        $this->clearCache();

        if ($this->controller->hasCustomHandlerMethod('handleDeleteRow')) {
            $res = $this->controller->getCustomHandler()->handleDeleteRow($id);
            if ($res) {
                return $res;
            }
        }

        foreach ($this->controller->getPatterns() as $pattern) {
            $pattern->delete($id);
        }

        $res = $this->model::find($id)->delete();

        $res = [
            'id'     => $id,
            'status' => $res,
        ];

        $this->deleteExtendsTables($id);

        if ($this->controller->hasCustomHandlerMethod('onDeleteRowResponse')) {
            $this->controller->getCustomHandler()->onDeleteRowResponse($res);
        }

        return $res;
    }

    private function deleteExtendsTables($id)
    {
        if (isset($this->extendsTable) && count($this->extendsTable)) {
            foreach ($this->extendsTable as $table) {
                DB::table($table)->where($this->extendsTableId[$table], $id)->delete();
            }
        }
    }

    public function fastSave($input)
    {
        $this->clearCache();

        $nameField = $input['name'];

        if (isset($input['value'])) {
            $valueField = $input['value'];
        } else {
            $fieldArray = request($nameField) ?? [];
            $valueField = json_encode(array_values($fieldArray));
        }

        $modelObj = $this->model::find($input['id']);
        $modelObj->$nameField = $valueField;

        $modelObj->save();
    }

    public function insertRow($values)
    {
        $this->clearCache();
        $insertDataRes = [];

        if ($this->controller->hasCustomHandlerMethod('handleInsertRow')) {
            $res = $this->controller->getCustomHandler()->handleInsertRow($values);
            if ($res) {
                return $res;
            }
        }

        $insertData = $this->getRowQueryValues($values);

        $this->checkFields($insertData);

        $this->doValidate($insertData);
        $id = false;
        if ($this->controller->hasCustomHandlerMethod('onInsertRowData')) {
            $id = $this->controller->getCustomHandler()->onInsertRowData($insertData);
        }

        if (! $id) {
            foreach ($insertData as $field => $data) {
                if (isset($this->definition['fields'][$field]) && ! isset($this->definition['fields'][$field]['tabs'])) {
                    $insertDataRes[$field] = $this->getData($data);
                } else {
                    $this->getDataTabs($insertDataRes, $insertData, $field);
                }
            }

            $modelThis = $this->model;

            $objectModel = new $modelThis();

            foreach ($insertDataRes as $key => $value) {
                $objectModel->$key = $value;
            }

            $objectModel->save();
            $id = $objectModel->id;
        }

        foreach ($this->controller->getPatterns() as $pattern) {
            $pattern->insert($values, $id);
        }

        $fields = $this->controller->getFields();
        foreach ($fields as $field) {
            if (preg_match('~^many2many~', $field->getFieldName())) {
                $this->onManyToManyValues($field->getFieldName(), $values, $id);
            }
            $this->updateGroupIfUseTable($field, $id);
        }

        $this->updateExtendsTable($id);

        $res = [
            'id'     => $id,
            'values' => $insertData,
        ];

        if ($this->controller->hasCustomHandlerMethod('onInsertRowResponse')) {
            $this->controller->getCustomHandler()->onInsertRowResponse($res);
        }

        return $res;
    }

    private function onManyToManyValues($ident, $values, $id)
    {
        $field = $this->controller->getField($ident);
        $vals = isset($values[$ident]) ? $values[$ident] : [];
        $field->onPrepareRowValues($vals, $id);
    }

    private function doValidate($values)
    {
        $errors = [];
        $fields = $this->definition['fields'];

        foreach ($fields as $ident => $options) {
            try {
                $field = $this->controller->getField($ident);
                if ($field->isPattern()) {
                    continue;
                }

                $tabs = $field->getAttribute('tabs');
                if ($tabs) {
                    if (! $field->getAttribute('extends_table')) {
                        foreach ($tabs as $tab) {
                            $fieldName = $ident.$tab['postfix'];
                            $field->doValidate($values[$fieldName]);
                        }
                    }
                } else {
                    if (array_key_exists($ident, $values)) {
                        $field->doValidate($values[$ident]);
                    }
                }
            } catch (\Exception $e) {
                $errors[] = 'Поле "'.$field->getAttribute('caption').'" '.$e->getMessage();
                continue;
            }
        }

        if ($errors) {
            $errors = implode('<br>', $errors);

            throw new \RuntimeException($errors);
        }
    }

    private function getRowQueryValues($values)
    {
        $values = $this->unsetFutileFields($values);

        $fields = $this->definition['fields'];

        foreach ($fields as $ident => $options) {
            $field = $this->controller->getField($ident);

            if ($field->isPattern()) {
                continue;
            }

            $tabs = $field->getAttribute('tabs');
            if ($tabs) {
                foreach ($tabs as $tab) {
                    $fieldName = $ident.$tab['postfix'];
                    $values[$fieldName] = $field->prepareQueryValue($values[$fieldName]);

                    if ($field->getAttribute('extends_table') && array_key_exists($fieldName, $values)) {
                        $this->extendsFields[$field->getAttribute('extends_table')][$fieldName] = $field->prepareQueryValue($values[$fieldName]);

                        unset($values[$fieldName]);
                        continue;
                    }
                }
            } else {
                if (array_key_exists($ident, $values)) {
                    if ($field->getAttribute('extends_table')) {
                        $this->extendsFields[$field->getAttribute('extends_table')][$ident] = $field->prepareQueryValue($values[$ident]);
                        unset($values[$ident]);
                        continue;
                    }

                    $values[$ident] = $field->prepareQueryValue($values[$ident]);

                    if ($field->getAttribute('type') == 'group' && $field->getAttribute('use_table')) {
                        unset($values[$ident]);
                    }
                }
            }
        }

        return $values;
    }

    private function unsetFutileFields($values)
    {
        unset($values['id']);
        unset($values['query_type']);

        foreach ($values as $key => $val) {
            if (preg_match('~^many2many~', $key)) {
                unset($values[$key]);
            }
        }

        // patterns
        unset($values['pattern']);

        // for tree
        unset($values['node']);
        unset($values['__node']);

        return $values;
    }

    private function checkFields(&$values)
    {
        $fields = $this->definition['fields'];

        foreach ($fields as $ident => $options) {
            $field = $this->controller->getField($ident);

            if (method_exists($field, 'getNewValueId') && isset($values[$ident.'_new_foreign'])) {
                if ($new_id = $field->getNewValueId($values[$ident.'_new_foreign'])) {
                    $values[$ident] = $new_id;
                }
                unset($values[$ident.'_new_foreign']);
            }

            if ($field->isPattern()) {
                continue;
            }

            $tabs = $field->getAttribute('tabs');

            if ($tabs) {
                foreach ($tabs as $tab) {
                    $this->checkField($values, $ident, $field);
                }
            } else {
                if (isset($values[$ident])) {
                    $this->checkField($values, $ident, $field);
                }
            }
        }
    }

    private function checkField($values, $ident, $field)
    {
        if (! $field->isEditable()) {
            throw new \RuntimeException("Field [{$ident}] is not editable");
        }
    }

    public function clearOrderBy()
    {
        $sessionPath = 'table_builder.'.$this->definitionName.'.order';
        Session::forget($sessionPath);

        return true;
    }

    public function clearCache()
    {
        $tags = $this->definition->getCacheTags();
        $keys = $this->definition->getCallbacks();

        if (!empty($tags)) {
            Cache::tags($tags)->flush();
        }

        if (!empty($keys)) {
            Cache::forget($keys);
        }
    }

    public function getUploadedFiles()
    {
        $list = File::files(public_path('storage/files'));

        return [
            'status' => 'success',
            'data'   => view('admin::tb.files_list', compact('list'))->render(),
        ];
    }

    public function getUploadedImages(AbstractField $field)
    {
        if ($field->getAttribute('use_image_storage')) {
            return $this->getImagesWithImageStorage();
        }

        return $this->getImagesWithDefaultPath();
    }

    private function getImagesWithImageStorage()
    {
        if (class_exists('\Vis\ImageStorage\Image')) {
            $list = \Vis\ImageStorage\Image::orderBy('created_at', 'desc');

            if (request('tag')) {
                $list->leftJoin('vis_tags2entities', 'id_entity', '=', 'vis_images.id')->where('entity_type', 'Vis\ImageStorage\Image')->where('id_tag', request('tag'));
            }

            if (request('gallary')) {
                $list->leftJoin('vis_images2galleries', 'id_image', '=', 'vis_images.id')->where('id_gallery', request('gallary'));
            }

            if (request('q')) {
                $list->where('vis_images.title', 'like', request('q').'%');
            }

            $list = $list->groupBy('vis_images.id')->paginate(18);

            $tags = \Vis\ImageStorage\Tag::where('is_active', 1)->orderBy('title', 'asc')->get();
            $galleries = \Vis\ImageStorage\Gallery::where('is_active', 1)->orderBy('title', 'asc')->get();

            $data = [
                'status' => 'success',
                'data'   => view('admin::tb.image_storage_list', compact('list', 'tags', 'galleries'))->render(),
            ];
        } else {
            $data = [
                'status' => 'success',
                'data'   => 'Не подключен пакет ImageStorage',
            ];
        }

        return $data;
    }

    private function getImagesWithDefaultPath()
    {
        $files = collect(File::files(public_path('storage/editor/fotos')))->sortBy(function ($file) {
            return filemtime($file);
        })->reverse();

        $page = (int) request('page') ?: 1;
        $onPage = 24;
        $slice = $files->slice(($page - 1) * $onPage, $onPage);

        $list = new \Illuminate\Pagination\LengthAwarePaginator($slice, $files->count(), $onPage);
        $list->setPath(url()->current());

        return [
            'status' => 'success',
            'data'   => view('admin::tb.images_list', compact('list'))->render(),
        ];
    }
}
