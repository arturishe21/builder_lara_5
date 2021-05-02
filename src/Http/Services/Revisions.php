<?php

namespace Vis\Builder\Services;

use Vis\Builder\Revision;

class Revisions {

    public function show(int $id, $definition) : array
    {
        $model = $definition->model()->find($id);
        $history = $model->revisionHistory()->orderBy('created_at', 'desc')->get();

        return [
            'html' => view('admin::list.modal_revision', compact('history'))->render(),
            'status' => true
        ];
    }

    public function doReturn(int $idRevision) : array
    {
        $thisRevision = Revision::find($idRevision);

        $model = $thisRevision->revisionable_type;
        $key = $thisRevision->key;
        $modelObject = $model::find($thisRevision->revisionable_id);
        $modelObject->$key = $thisRevision->old_value;
        $modelObject->save();

        return [
            'status' => true
        ];
    }
}
