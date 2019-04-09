<?php

namespace Vis\Builder\Handlers;

use Illuminate\Support\Facades\Cache;

class ActionsHandler
{
    protected $def;
    protected $controller;

    public static function canDisplayAction(string $actionName, array $actionData) : bool
    {
        if ($actionName == 'insert' || $actionName == 'filter' || $actionName == 'custom') {
            return false;
        }

        if (!isset($actionData['check'])) {
            return true;
        }

        $check = $actionData['check'];

        if (is_callable($check)) {
            return $check();
        }

        return (bool)$check;
    }

    public function __construct(array $actionsDefinition, &$controller)
    {
        $this->def = $actionsDefinition;
        $this->controller = $controller;
    }

    public function fetch($type, $row = [], $buttonDefinition = [])
    {
        switch ($type) {
            case 'insert':
                return $this->onInsertButton();

            case 'update':
                return $this->onUpdateButton($row);

            case 'clone':
                return $this->onCloneButton($row);

            case 'revisions':
                return $this->onRevisionsButton($row);

            case 'preview':
                return $this->onPreviewButton($row);

            case 'delete':
                return $this->onDeleteButton($row);

            case 'views_statistic':
                return $this->onViewStatisticButton($row);

            case 'constructor':
                return $this->onConstructorButton($row, $buttonDefinition);

            case 'custom':
                return $this->onCustomButton($row, $buttonDefinition);

            default:
                return;
        }
    }

    private function onCustomButton($row, $button)
    {
        $action = view('admin::tb.action_custom');
        $action->row = $row;
        $action->def = $button;
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onInsertButton()
    {
        if ($this->controller->hasCustomHandlerMethod('onInsertButtonFetch')) {
            $res = $this->controller->getCustomHandler()->onInsertButtonFetch($this->def['insert']);
            if ($res) {
                return $res;
            }
        }

        $action = view('admin::tb.action_insert');
        $action->def = $this->def['insert'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onUpdateButton($row)
    {
        if ($this->controller->hasCustomHandlerMethod('onUpdateButtonFetch')) {
            $res = $this->controller->getCustomHandler()->onUpdateButtonFetch($this->def['update']);
            if ($res) {
                return $res;
            }
        }

        $action = view('admin::tb.action_update');
        $action->row = $row;
        $action->def = $this->def['update'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onRevisionsButton($row)
    {
        $action = view('admin::tb.action_revisions');
        $action->row = $row;
        $action->def = $this->def['revisions'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onCloneButton($row)
    {
        if ($this->controller->hasCustomHandlerMethod('onCloneButtonFetch')) {
            $res = $this->controller->getCustomHandler()->onUpdateButtonFetch($this->def['clone']);
            if ($res) {
                return $res;
            }
        }

        $action = view('admin::tb.action_clone');
        $action->row = $row;
        $action->def = $this->def['clone'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onViewStatisticButton($row)
    {
        $action = view('admin::tb.action_views_statistic');
        $action->row = $row;
        $action->def = $this->def['views_statistic'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }

    private function onPreviewButton($row)
    {
        $action = view('admin::tb.action_preview');
        $action->row = $row;
        $action->def = $this->def['preview'];
        $action->definition = $this->controller->getDefinition();
        $model = $action->definition->getModel();
        $action->model = $model;
        $action->url = $this->getUrl($action, $model, $row['id']);

        $params['show'] = 1;

        $action->url .= '?'.http_build_query($params);

        return $action;
    }

    private function onConstructorButton($row)
    {
        $action = view('admin::tb.action_constructor');
        $action->row = $row;
        $action->def = $this->def['constructor'];
        $action->definition = $this->controller->getDefinition();
        $model = $action->definition->getModel();
        $action->model = $model;
        $action->url = $this->getUrl($action, $model, $row['id']).'?mode=construct';

        return $action;
    }

    private function getUrl($action, $model, $id)
    {
        if (!empty($action->definition->getCacheTags())) {
            return Cache::tags($action->definition->getCacheTags())
                ->rememberForever('url'.$model.$id, function () use ($model, $id) {
                    return $model::find($id)->getUrl();
                });
        }

        return $model::find($id)->getUrl();
    }

    private function onDeleteButton($row)
    {
        if ($this->controller->hasCustomHandlerMethod('onDeleteButtonFetch')) {
            $res = $this->controller->getCustomHandler()->onDeleteButtonFetch($this->def['delete']);
            if ($res) {
                return $res;
            }
        }

        $action = view('admin::tb.action_delete');
        $action->row = $row;
        $action->def = $this->def['delete'];
        $action->definition = $this->controller->getDefinition();

        return $action;
    }
}
