<?php

namespace App\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Constants\GeneralConst;
use Illuminate\Support\Collection;

class UserLib
{

    /**
     * すべてのユーザーを取得
     *
     * @return Collection
     */
    public function getAllUserNames(): Collection
    {
        return User::select('id', 'name')->get();
    }

    /**
     * IDでユーザーのオンボーディングデータを取得
     *
     * @return int onboarded
     */
    public function getUserOnboardedById(): int
    {
        return User::where('id', Auth::user()->id)->value('onboarded');
    }

    /**
     * IDで補償されたユーザーを変更
     *
     * @param $id
     * @return void
     */
    public function changeOnboardedById($id): void
    {
        try {
            DB::beginTransaction();
                $user = User::find($id);
                $user->onboarded = GeneralConst::DONE_ONBOARD;
                $user->save();
            DB::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * ロールごとにユーザーを取得する
     *
     * @return Object
     */
    public function getMTMUsers(): Object
    {
        return User::where('role', GeneralConst::MTM)->get();
    }

    /**
     * ユーザーを獲得する
     *
     * @return Object
     */
    public function getUsers(): Object
    {
        return User::get();
    }
}
