<?php

namespace Vis\Builder\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class Actions
{
    protected $definition;
    protected $actionsList = [];
    protected $revision;

    public function __construct($definition = null)
    {
        $this->definition = $definition;
        $this->revision = new Revisions();
    }

    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }

    public function fetch($type, $record = null)
    {
        return view("admin::new.list.actions.{$type}", compact('record'));
    }

    public function list($record)
    {
        $collectionActions = $this->definition->actions()->getActionsAccess();
        $collectionActions = Arr::except($collectionActions, 'insert');

        return view('admin::new.list.actions.all', [
            'record' => $record,
            'action' => $this,
            'collectionActions' => $collectionActions
        ]);
    }

    public function getActionsAccess()
    {
        return $this->actionsList;
    }

    public function insert()
    {
        $this->actionsList['insert'] = 'insert';

        return $this;
    }

    public function update()
    {
        $this->actionsList['update'] = 'update';

        return $this;
    }

    public function preview()
    {
        $this->actionsList['preview'] = 'preview';

        return $this;
    }

    public function delete()
    {
        $this->actionsList['delete'] = 'delete';

        return $this;
    }

    public function clone()
    {
        $this->actionsList['clone'] = 'clone';

        return $this;
    }

    public function revisions()
    {
        $this->actionsList['revisions'] = 'revisions';

        return $this;
    }

    public function router($action)
    {
        $method = Str::camel($action);

        return $this->$method(request()->except('query_type'));
    }

    private function deleteRow($request)
    {
        return $this->definition->remove($request['id']);
    }

    private function cloneRecord($request)
    {
        return $this->definition->clone($request['id']);
    }

    private function changeOrder($request)
    {
        return $this->definition->changeOrder($request['order'], $request['params'] ?? '');
    }

    private function changeDirection($request)
    {
        session()->put($this->definition->getSessionKeyOrder(), $request);

        return [
            'status' => 'success',
        ];
    }

    private function clearOrderBy($request) {

        session()->forget($this->definition->getSessionKeyOrder());

        return [
            'status' => 'success',
        ];
    }

    private function showAddForm($request)
    {
        return $this->definition->showAddForm();
    }

    private function showEditForm($request)
    {
        return $this->definition->showEditForm($request['id']);
    }

    private function saveAddForm($request)
    {
        return $this->definition->saveAddForm($request);
    }

    private function showRevisions($request)
    {
        return $this->revision->show($request['id'], $this->definition);
    }

    private function returnRevisions($request)
    {
        return $this->revision->doReturn($request['id'], $this->definition);
    }

    private function setPerPage($request)
    {
        session()->put($this->definition->getSessionKeyPerPage(), $request);

        return [
            'status' => 'success'
        ];
    }

    private function saveEditForm($request)
    {
        return $this->definition->saveEditForm($request);
    }

    private function manyToManyAjaxSearch($request)
    {
        return $this->getThisField()->search($this->definition);
    }

    private function foreignAjaxSearch($request)
    {
        return $this->getThisField()->search($this->definition);
    }

    private function uploadPhoto($request)
    {
        return $this->getThisField()->upload($this->definition);
    }

    private function uploadFile($request)
    {
        return $this->getThisField()->upload($this->definition);
    }

    private function getThisField()
    {
        return $this->definition->getAllFields()[request('ident')];
    }

    private function selectWithUploadedImages($request)
    {
        return $this->getThisField()->selectWithUploadedImages($this->definition);
    }

    private function selectWithUploaded($request)
    {
        return $this->getThisField()->selectWithUploadedFiles($this->definition);
    }

    private function search($request)
    {
        session()->put($this->definition->getSessionKeyFilter(), $request);

        return [
            'status' => 'success',
        ];
    }

    public function getHtmlForeignDefinition($request)
    {
        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $this->definition->getAllFields()[$parseJsonData['ident']];

        return $field->getTable($this->definition, $parseJsonData);
    }

    public function deleteForeignRow($request)
    {
        $parseJsonData = (array) json_decode($request['paramsJson']);
        $field = $this->definition->getAllFields()[$parseJsonData['ident']];

        return $field->remove($this->definition, $parseJsonData);
    }

}
