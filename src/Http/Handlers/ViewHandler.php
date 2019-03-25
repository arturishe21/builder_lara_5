<?php

namespace Vis\Builder\Handlers;

use Illuminate\Support\Facades\Session;
use Vis\Builder\Helpers\AnnotationHelper;
use Vis\Builder\JarboeController;

/**
 * Class ViewHandler.
 */
class ViewHandler
{
    /**
     * @var JarboeController
     */
    protected $controller;
    /**
     * @var mixed
     */
    protected $definition;
    /**
     * @var mixed
     */
    protected $definitionName;
    /**
     * @var
     */
    protected $model;

    /**
     * ViewHandler constructor.
     *
     * @param JarboeController $controller
     */
    public function __construct(JarboeController $controller)
    {
        $this->controller = $controller;
        $this->definition = $controller->getDefinition();
        $this->definitionName = $this->definition->getName();
        $this->model = $this->definition->getModel();
    }

    /**
     * @param $id
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function showEditFormPage($id)
    {
        if ($id === false) {
            if (! $this->controller->actions->isAllowed('insert')) {
                throw new \RuntimeException('Insert action is not permitted');
            }
        } else {
            if (! $this->controller->actions->isAllowed('update')) {
                throw new \RuntimeException('Update action is not permitted');
            }
            if (! $this->controller->isAllowedID($id)) {
                throw new \RuntimeException('Not allowed to edit row #'.$id);
            }
        }

        if ($id) {
            $form = view('admin::tb.form_edit');
            $js = view('admin::tb.form_edit_validation');
        } else {
            $form = view('admin::tb.form_create');
            $js = view('admin::tb.form_create_validation');
        }

        $form->is_page = true;
        $form->is_tree = false;
        $js->is_tree = false;

        $form->def = $this->definition;
        $form->controller = $this->controller;
        $js->def = $this->definition;
        $js->controller = $this->controller;

        $form->is_blank = true;
        $js->is_blank = true;

        if ($id) {
            $row = $this->controller->query->getRow($id);

            $form->row = $row;
            $form->is_blank = false;
            $js->row = $row;
            $js->is_blank = false;
        }

        $definition = $this->definition;
        $templatePostfix = $id ? 'edit' : 'create';

        return view(
            'admin::table_page_'.$templatePostfix,
            compact('form', 'js', 'definition', 'id')
        )
                ->render();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showList()
    {
        $table = view('admin::tb.table_builder');

        if ($this->controller->hasCustomHandlerMethod('onShowList')) {
            $res = $this->controller->getCustomHandler()->onShowList();

            if ($res) {
                $table->rows = $res;
            }
        }

        if (! $table->rows) {
            $table->rows = $this->controller->query->getRows();
        }

        if (!is_null($this->definition->getAnnotations())) {
            $annotation = new AnnotationHelper($this->definition->getAnnotations());
            $table->annotation = $annotation->handle();
        }

        $table->def = $this->definition;
        $table->controller = $this->controller;
        $table->per_page = Session::get('table_builder.'.$this->definitionName.'.per_page');
        $table->fieldsList = $this->controller->getDefinition()->getFieldsList();
        $table->filterView = $this->getViewFilter($table->def);

        return $table;
    }

    /**
     * @param $def
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
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

    /**
     * @throws \Throwable
     *
     * @return array
     */
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

    /**
     * @throws \Throwable
     *
     * @return array
     */
    public function deleteForeignDefinition()
    {
        $this->controller->query->clearCache();

        $params = (array) json_decode(request('paramsJson'));
        $modelThis = config('builder.tb-definitions.'.$params['definition'].'.options.model');

        $modelThis::find(request('idDelete'))->delete();

        return $this->showHtmlForeignDefinition();
    }

    /**
     * @return bool
     */
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

    /**
     * @param bool $id
     * @param bool $isTree
     *
     * @throws \Throwable
     *
     * @return string
     */
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

    /**
     * @param bool $id
     * @param bool $isTree
     *
     * @throws \Throwable
     *
     * @return string
     */
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

    /**
     * @param bool $id
     * @param bool $isTree
     *
     * @throws \Throwable
     *
     * @return string
     */
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

    /**
     * @param $data
     *
     * @throws \Throwable
     *
     * @return string
     */
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

    /**
     * @param $row
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function fetchActions($row)
    {
        $actions = view('admin::tb.single_row_actions');

        $actions->row = $row;
        $actions->def = $this->definition;
        $actions->actions = $this->controller->actions;

        return $actions->render();
    }
}
