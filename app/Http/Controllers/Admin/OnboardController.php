<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Libs\UserLib;

class OnboardController extends Controller
{

    /**
     * @var \App\Libs\UserLib;
     */
    protected $user_lib;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
        $this->user_lib = new UserLib();
    }

    /**
     * オンボード完了時にユーザーテーブルのオンボード列を変更する
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function changeOnboardStatus(Request $request, $id): JsonResponse
    {           
        //開始ログ
        $this->start();

        $complete_onboard = json_decode($request->input('complete'));

        if($complete_onboard) {
            $this->user_lib->changeOnboardedById($id);

            //終了ログ
            $this->end();
            return response()->json([
                'success' => true,
                'message' => 'user is onboarded!'
            ]);
        }

        //終了ログ
        $this->end();
        return response()->json([
            'success' => false,
            'message' => 'fail in user onboarding!'
        ]);
    }
}
