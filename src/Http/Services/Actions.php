<?php

namespace Vis\Builder\Http\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Vis\Builder\Http\ControllersNew\TreeController;
use Illuminate\View\View;
use Vis\Builder\Http\Definitions\Resource;
use Vis\Builder\Http\Fields\Field;

class Actions
{
    protected ?Resource $definition;
    protected array $actionsList = [];
    protected $revision;
    protected bool $isHideActions = false;

    public function __construct(?Resource $definition = null)
    {
        $this->definition = $definition;
        $this->revision = new Revisions();
    }

    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }

    public function fetch(string $type, ?Model $record = null): View
    {
        return view("admin::list.actions.{$type}", compact('record'));
    }

    public function list(Model $record): View
    {
        $collectionActions = $this->definition->actions()->getActionsAccess();
        $collectionActions = Arr::except($collectionActions, 'insert');

        return view('admin::list.actions.all', [
            'record' => $record,
            'action' => $this,
            'collectionActions' => $collectionActions
        ]);
    }

    public function getActionsAccess(): array
    {
        return $this->actionsList;
    }

    public function hideActions(): self
    {
        $this->isHideActions = true;

        return $this;
    }

    public function isHideAction(): bool
    {
        return $this->isHideActions;
    }

    public function insert(): self
    {
        $this->setAccess('insert');

        return $this;
    }

    public function update(): self
    {
        $this->setAccess('update');

        return $this;
    }

    public function preview(): self
    {
        $this->actionsList['preview'] = 'preview';

        return $this;
    }

    public function delete(): self
    {
        $this->setAccess('delete');

        return $this;
    }

    public function clone(): self
    {
        $this->setAccess('clone');

        return $this;
    }

    public function revisions(): self
    {
        $this->setAccess('revisions');

        return $this;
    }

    private function setAccess(string $action): void
    {
        if (app('user')->hasAccessActionsForCms($action)) {
            $this->actionsList[$action] = $action;
        }
    }

    public function router(string $action): mixed
    {
        $method = Str::camel($action);

        return $this->$method(request()->except('query_type'));
    }

    private function deleteRow(array $request): JsonResponse
    {
        return $this->definition->remove($request['id']);
    }

    private function cloneRecord(array $request): JsonResponse
    {
        return $this->definition->clone($request['id']);
    }

    private function changeOrder(array $request): JsonResponse
    {
        return $this->definition->changeOrder($request['order'], $request['params'] ?? '');
    }

    private function changeDirection(array $request): JsonResponse
    {
        session()->put($this->definition->getSessionKeyOrder(), $request);

        return $this->responseSuccess();
    }

    private function clearOrderBy(array $request): JsonResponse
    {
        session()->forget($this->definition->getSessionKeyOrder());

        return $this->responseSuccess();
    }

    private function showAddForm(array $request): JsonResponse
    {
        return $this->definition->showAddForm();
    }

    private function showEditForm(array $request): JsonResponse
    {
        return $this->definition->showEditForm($request['id']);
    }

    private function saveAddForm(array $request): JsonResponse
    {
        return $this->definition->saveAddForm($request);
    }

    private function showRevisions(array $request): JsonResponse
    {
        return $this->revision->show($request['id'], $this->definition);
    }

    private function returnRevisions(array $request): JsonResponse
    {
        return $this->revision->doReturn($request['id']);
    }

    private function setPerPage(array $request): JsonResponse
    {
        session()->put($this->definition->getSessionKeyPerPage(), $request);

        return $this->responseSuccess();
    }

    private function saveEditForm(array $request): JsonResponse
    {
        return $this->definition->saveEditForm($request);
    }

    private function manyToManyAjaxSearch(array $request): JsonResponse
    {
        return $this->getThisField()->search($this->definition);
    }

    private function foreignAjaxSearch(array $request): JsonResponse
    {
        return $this->getThisField()->search($this->definition);
    }

    private function uploadFile(array $request): JsonResponse
    {
        return $this->getThisField()->upload($this->definition);
    }

    private function getThisField(): Field
    {
        return $this->definition->getAllFields()[request('ident')];
    }

    private function selectWithUploaded(array $request)
    {
        return $this->getThisField()->selectWithUploadedFiles($this->definition);
    }

    private function doChangePosition(array $request)
    {
       return (new TreeController($this->definition))->doChangePosition();
    }

    private function doFastChangeField(array $request): JsonResponse
    {
        $this->getThisField()->fastSave($this->definition, $request);

        return $this->responseSuccess();
    }

    private function fastSave(array $request): JsonResponse
    {
        $model = $this->definition->model()->find($request['id']);
        $model->{$request['name']} = $request['value'];
        $model->save();

        return $this->responseSuccess();
    }

    private function search(array $request): JsonResponse
    {
        session()->put($this->definition->getSessionKeyFilter(), $request);

        return $this->responseSuccess();
    }

    private function cloneForeignRow(array $request)
    {
        $this->cloneRecord($request);

        return $this->getHtmlForeignDefinition($request);
    }

    public function getHtmlForeignDefinition(array $request)
    {
        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $this->definition->getAllFields()[$parseJsonData['ident']];

        return $field->getTable($this->definition, $parseJsonData);
    }

    public function deleteForeignRow(array $request)
    {
        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $this->definition->getAllFields()[$parseJsonData['ident']];

        return $field->remove($this->definition, $parseJsonData);
    }

    private function responseSuccess(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
        ]);
    }
}
