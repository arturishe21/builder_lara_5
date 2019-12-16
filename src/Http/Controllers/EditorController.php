<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class EditorController.
 */
class EditorController extends Controller
{
    /**
     * Loading photos from froala Editor.
     */
    public function uploadFoto()
    {
        $photo = request()->file('file');

        $rules = [
            'file'  => 'required|image',
        ];

        $validator = Validator::make(request()->ll(), $rules);

        if ($validator->fails()) {
            return Response::json([
                'status'          => 'error',
                'errors_messages' => $validator->messages(),
            ]);
        }

        $destinationPath = 'storage/editor/fotos';

        $ext = $photo->getClientOriginalExtension();  // Get real extension according to mime type
        $fullname = $photo->getClientOriginalName(); // Client file name, including the extension of the client
        $hashname = md5(date('H.i.s').'_'.$fullname).'.'.$ext;

        $fullPathImg = '/'.$destinationPath.'/'.$hashname;

        request()->file('file')->move($destinationPath, $hashname);

        return Response::json(['link' => $fullPathImg]);
    }

    /**
     * Loading files from froala Editor.
     */
    public function uploadFile()
    {
        $file = request()->file('file');

        $rules = [
            'file'  => 'required',
        ];

        $validator = Validator::make(request()->all(), $rules);

        if ($validator->fails()) {
            return Response::json(['status' => 'error', 'errors_messages' => $validator->messages()]);
        }

        $destinationPath = 'storage/editor/files';

        $ext = $file->getClientOriginalExtension();  // Get real extension according to mime type
        $fullname = $file->getClientOriginalName();
        $fullname = str_replace('.'.$ext, '', $fullname);

        $hashname = str_slug($fullname).'.'.$ext;
        $fullPathImg = '/'.$destinationPath.'/'.$hashname;

        if (file_exists(public_path().$fullPathImg)) {
            $hashname = str_slug($fullname).'_'.time().'.'.$ext;
            $fullPathImg = '/'.$destinationPath.'/'.$hashname;
        }

        request()->file('file')->move($destinationPath, $hashname);

        return Response::json(['link' => $fullPathImg]);
    }

    //end uploadFile

    /**
     * load img manager.
     */
    public function loadImages()
    {
        $imgs = scandir(public_path().'/storage/editor/fotos');

        unset($imgs[0]);
        unset($imgs[1]);

        $imgRes = [];
        $k = 0;
        $pathToImg = '/storage/editor/fotos/';
        foreach ($imgs as $img) {
            if (is_file(public_path().$pathToImg.$img)) {
                $imgRes[$k]['url'] = $pathToImg.$img;
                $imgRes[$k]['thumb'] = $pathToImg.$img;
                $k++;
            }
        }

        return Response::json($imgRes);
    }

    /**
     * delete img.
     */
    public function deleteImages()
    {
        unlink(public_path().request('src'));
    }

    /**
     * Quick edit in list.
     */
    public function doQuickEdit()
    {
        $model = request('model');
        $id = request('id');
        $field = request('field');
        $text = request('text');

        $page = $model::find($id);
        $page->$field = $text;
        $page->save();
    }
}
