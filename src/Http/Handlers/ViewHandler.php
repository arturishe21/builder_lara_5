<?php

namespace Vis\Builder\Handlers;

class ViewHandler
{
    protected $controller;
    protected $definition;
    protected $definitionName;
    protected $model;

    public function __construct(\Vis\Builder\JarboeController $controller)
    {
        $this->controller = $controller;
        $this->definition = $controller->getDefinition();
        $this->definitionName = $this->definition->getName();
        $this->model = $this->definition->getModel();
    }

    public function showEditFormPage($id)
    {
        if ($id === false) {
            if (! $this->controller->actions->isAllowed('insert')) {
                throw new \RuntimeException('Insert action is not permitted');
            }
        } else {
            if (!$this->controller->actions->isAllowed('update')) {
                throw new \RuntimeException('Update action is not permitted');
            }
            if (!$this->controller->isAllowedID($id)) {
                throw new \RuntimeException('Not allowed to edit row #'.$id);
            }
        }

        $data = [
            'is_page' => true,
            'is_tree' => false,
            'def' => $this->definition,
            'controller' => $this->controller,
            'is_blank' => true
        ];

        if ($id) {
            $data['row'] = $this->controller->query->getRow($id);
            $data['is_blank'] = false;

            $form = view('admin::tb.form_edit', $data);
            $js = view('admin::tb.form_edit_validation', $data);
        } else {
            $form = view('admin::tb.form_create', $data);
            $js = view('admin::tb.form_create_validation', $data);
        }

        $definition = $this->definition;
        $templatePostfix = $id ? 'edit' : 'create';

        return view('admin::table_page_'.$templatePostfix, compact('form', 'js', 'definition', 'id'))->render();
    }

    public function showList()
    {
        $rows = null;

        if ($this->controller->hasCustomHandlerMethod('onShowList')) {
            $res = $this->controller->getCustomHandler()->onShowList();

            if ($res) {
                $rows = $res;
            }
        }

        if (!$rows) {
            $rows = $this->controller->query->getRows();
        }

        if (!is_null($this->definition->getAnnotations())) {
            $annotation = \Vis\Builder\Helpers\AnnotationHelper::staticHandler($this->definition->getAnnotations());
        }

        $def = $this->definition;
        $controller = $this->controller;
        $per_page = session('table_builder.'.$this->definitionName.'.per_page');
        $fieldsList = $this->controller->getDefinition()->getFieldsList();
        $filterView = $this->getViewFilter($def);

        return view('admin::tb.table_builder', compact('rows', 'annotation', 'def', 'controller', 'per_page', 'fieldsList', 'filterView'));
    }

    private function getViewFilter($def)
    {
        if ($this->controller->hasCustomHandlerMethod('onViewFilter')) {
            $res = $this->controller->getCustomHandler()->onViewFilter();
            if ($res) {
                return $res;
            }
        }

        return view('admin::tb.table_filter', ['def' => $def]);
    }

    public function showHtmlForeignDefinition()
    {
        $params = (array) json_decode(request('paramsJson'));
        $result = [];
        $fileDefinition = 'builder.tb-definitions.'.$params['definition'];

        foreach ($params['show'] as $field) {
            $arrayDefinitionFields[$field] =
                config($fileDefinition.'.fields.'.$field);
        }

        if (request('id')) {
            $modelThis = config($fileDefinition.'.options.model');
            $result = $modelThis::where($params['foreign_field'], request('id'));

            $result = isset($params['sortable'])
                    ? $result->orderBy($params['sortable'], 'asc')->orderBy('id', 'desc')
                    : $result->orderBy('id', 'desc');

            $this->filterDefinition($fileDefinition, $result);

            $result = $result->get();
        }

        $idUpdate = request('id') ?: '';
        $attributes = request('paramsJson');

        return [
            'html' => view(
                'admin::tb.input_definition_table_data',
                compact('arrayDefinitionFields', 'result', 'idUpdate', 'attributes')
            )
                        ->render(),
            'count_records' => count($result),
        ];
    }

    private function filterDefinition($fileDefinition, $result)
    {
        $filters = config($fileDefinition.'.filters') ? config($fileDefinition.'.filters') : [];
        if (is_callable($filters)) {
            $filters($result);

            return;
        }

        foreach ($filters as $name => $field) {
            $result->where($name, $field['sign'], $field['value']);
        }
    }

    public function deleteForeignDefinition()
    {
        $this->controller->query->clearCache();

        $params = (array) json_decode(request('paramsJson'));
        $modelThis = config('builder.tb-definitions.'.$params['definition'].'.options.model');

        $modelThis::find(request('idDelete'))->delete();

        return $this->showHtmlForeignDefinition();
    }

    public function changePositionDefinition()
    {
        $this->controller->query->clearCache();

        $params = (array) json_decode(request('paramsJson'));

        if (! isset($params['sortable'])) {
            throw new \RuntimeException('Не определено поле для сортировки');
        }
        $idsPositionUpdate = (array) json_decode(request('idsPosition'));
        $modelThis = config('builder.tb-definitions.'.$params['definition'].'.options.model');
        $sortField = $params['sortable'];

        $records = $modelThis::whereIn('id', $idsPositionUpdate)
            ->orderByRaw('FIELD(id, '.implode(',', $idsPositionUpdate).')')->get();

        foreach ($records as $k => $record) {
            $record->$sortField = $k;
            $record->save();
        }

        return true;
    }

    public function showEditForm($id = false, $isTree = false)
    {
        $table = $id ? view('admin::tb.modal_form_edit') : view('admin::tb.modal_form');

        $table->is_tree = $isTree;
        $table->def = $this->definition;
        $table->controller = $this->controller;
        $table->is_blank = true;

        $table->definitionName = $table->controller->getDefinitionName();

        if ($id) {
            $table->row = (array) $this->controller->query->getRow($id);
            $table->is_blank = false;
        }

        return $table->render();
    }

    public function showRevisionForm($id = false, $isTree = false)
    {
        $table = view('admin::tb.modal_revision');

        $objModel = $this->model::find($id);

        $table->is_tree = $isTree;
        $table->def = $this->definition;
        $table->controller = $this->controller;
        $table->history = $objModel->revisionHistory()->orderBy('created_at', 'desc')->get();

        return $table->render();
    }

    public function showViewsStatistic($id = false, $isTree = false)
    {
        $table = view('admin::tb.modal_views_statistic');

        $table->is_tree = $isTree;
        $table->def = $this->definition;
        $table->controller = $this->controller;
        $table->id = $id;
        $table->model = $this->model;

        return $table->render();
    }

    public function getRowHtml($data)
    {
        $row = view('admin::tb.single_row');
        $data['values'] = $this->controller->query->getRow($data['id']);

        $row->controller = $this->controller;
        $row->actions = $this->controller->actions;
        $row->def = $this->definition;
        $row->row = (array) $data['values'];

        return $row->render();
    }

    public function fetchActions($row)
    {
        $def = $this->definition;
        $actions = $this->controller->actions;

        return view('admin::tb.single_row_actions', compact('row', 'def', 'actions'))->render();
    }
}
