<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use App\Cms\Admin;

class TBController extends Controller
{
    public function showDashboard(Admin $admin)
    {
        return resolve($admin->login())->onLogin();
    }

    public function changeSkin()
    {
        Cookie::queue('skin', request('skin'), '100000');
    }

    public function changeLanguage()
    {
        Cookie::queue('lang_admin', request('lang'), '100000000');

        return Redirect::back();
    }

    public function saveCropImg()
    {
        $data = request()->all();
        $infoImg = pathinfo($data['originalImg']);
        $fileCrop = $infoImg['dirname'].'/'.md5($infoImg['filename']).time().'_crop.'.$infoImg['extension'];
        $ifp = fopen(public_path().$fileCrop, 'wb');
        $dataFile = explode(',', $data['data']);

        fwrite($ifp, base64_decode($dataFile[1]));
        fclose($ifp);

        $smallImg = isset($data['width']) || isset($data['height']) ?
            glide($fileCrop, ['w' => $data['width'], 'h' => $data['height']]).'?time='.time() :
            $fileCrop;

        return Response::json(
            [
                'status'       => 'success',
                'picture'      => $fileCrop,
                'pictureSmall' => $smallImg,
            ]
        );
    }
}
