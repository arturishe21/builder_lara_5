<?php

namespace Vis\Builder;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;

/**
 * Class TBController.
 */
class TBController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showDashboard()
    {
        $onLoginFunction = 'builder.login.on_login';

        if (config($onLoginFunction) && config($onLoginFunction)()) {
            return config($onLoginFunction)();
        }

        return Redirect::to('/admin/tree');
    }

    /**
     * change skin.
     */
    public function doChangeSkin()
    {
        $skin = request('skin');

        Cookie::queue('skin', $skin, '100000');
    }

    /**
     * change lang.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doChangeLangAdmin()
    {
        $lang = request('lang');
        Cookie::queue('lang_admin', $lang, '100000000');

        return Redirect::back();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doSaveMenuPreference()
    {
        $option = request('option');
        $cookie = Cookie::forever('tb-misc-body_class', $option);

        $response = Response::json([
            'status' => true,
        ]);
        $response->headers->setCookie($cookie);

        return $response;
    }

    // end doSaveMenuPreference

    /**
     * @param $exception
     * @param $code
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function returnError($exception, $code)
    {
        $message = $exception->getMessage();

        if (! $message) {
            $message = '404 error';
        }

        $data = [
            'status'  => 'error',
            'code'    => $code,
            'message' => $message,
        ];

        return Response::json($data, $code);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function doSaveCropImg()
    {
        $data = request()->all();
        $infoImg = pathinfo($data['originalImg']);
        $fileCrop = '/'.$infoImg['dirname'].'/'.md5($infoImg['filename']).time().'_crop.'.$infoImg['extension'];
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
                'picture'      => ltrim($fileCrop, '/'),
                'pictureSmall' => $smallImg,
            ]
        );
    }
}
