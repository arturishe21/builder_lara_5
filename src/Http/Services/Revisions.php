<?php

namespace Vis\Builder\Http\Services;

use Vis\Builder\Definitions\Resource;
use Vis\Builder\Revision;
use Illuminate\Http\JsonResponse;

class Revisions
{
    public function show(int $id, Resource $definition): JsonResponse
    {
        $model = $definition->model()->find($id);
        $history = $model->revisionHistory()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'html' => view('admin::list.modal_revision', compact('history'))->render(),
            'status' => true
        ]);
    }

    public function doReturn(int $idRevision): JsonResponse
    {
        $thisRevision = Revision::find($idRevision);

        $model = $thisRevision->revisionable_type;
        $key = $thisRevision->key;
        $modelObject = $model::find($thisRevision->revisionable_id);
        $modelObject->$key = $thisRevision->old_value;
        $modelObject->save();

        return response()->json([
            'status' => true
        ]);
    }
}
