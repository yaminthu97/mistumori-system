<?php

namespace App\Libs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Wiki;
use App\Models\User;
use App\Constants\GeneralConst;

class WikiLib
{
    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * DBからWikiデータを取得
     *
     * @param $id
     * @return object
     */
    public function getWikiDataById($id = 1): ?object
    {
        return Wiki::where('id', $id)->first();
    }

    /**
     * WikiDataをDBに保存
     *
     * @param object $request
     * @param $id
     * @return int
     */
    public function saveWikiData(object $request, $id): int
    {
        $file_name = GeneralConst::WIKI_FILEPATH . $id . '/' . $request->wiki_file;
        try {
            DB::beginTransaction();

            $wiki_data = array(
                'title' => $request->wiki_title,
                'content' => $request->wiki_content,
            );

            $wiki = $id ? Wiki::find($id) : new Wiki();

            // モディファー
            $modifier = json_decode($wiki->modifier, true) ?? [];

            $new_modifier = [Auth::user()->id => Carbon::now()->format('Y-m-d H:i:s')];
            $modifier[] = $new_modifier;
            $wiki->modifier = json_encode($modifier);

            $wiki->fill($wiki_data);
            $wiki->save();

            // ファイル
            $id = $wiki->id;
            $folder = GeneralConst::WIKI_FILEPATH . $id;
            $input_file_name_arr = array_map('trim', explode(', ', $request->wiki_file));
            $check_flg = Storage::disk('public')->exists($folder);

            if ($input_file_name_arr && $check_flg) {
                $files = Storage::disk('public')->allFiles($folder);
                if ($files) {
                    foreach ($files as $image_file) {
                        $file_names_arr[] = basename($image_file);
                    }
                    $is_true = array_diff($file_names_arr, $input_file_name_arr);
                    if (!empty($is_true)) $this->deleteFilePath($id);
                } else {
                    $this->deleteFilePath($id);
                }
            }

            // ファイルパスをDBに保存
            foreach ($input_file_name_arr as $index => $input_file_name) {
                $file_name = GeneralConst::WIKI_FILEPATH . $id . '/' . $input_file_name;
                if ($request->file('wiki_path')) $file_path = $request->file('wiki_path')[$index];
                $wiki->file_path = isset($request->wiki_file)
                ? ((!Storage::disk('public')->exists($file_name) || isset($file_path))
                    ? $this->updateFile($file_path, $id)
                    : $folder)
                : $this->deleteFilePath($id);
            }
            $wiki->save();

            DB::commit();
            return $wiki->id;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * アップデートファイル
     *
     * @param UploadedFile $original_file_path
     * @param $id
     * @return string
     */
    public function updateFile(UploadedFile $original_file_path, $id): string
    {
        $file_name = GeneralConst::WIKI_FILEPATH . $id;
        $this->uploadFile($original_file_path, $file_name, $id);
        return $file_name;
    }

    /**
     * ファイルをアップロードする
     *
     * @param UploadedFile $file_path
     * @param string $file_folder
     * @return void
     */
    public function uploadFile(UploadedFile $file_path, string $file_folder): void
    {
        $file_name = $file_folder . '/';
        $original_file_name = $file_path->getClientOriginalName();
        $file_name = $file_name . $original_file_name;
        Storage::disk('public')->put($file_name, file_get_contents($file_path));
    }

    /**
     * IDでDBからファイルパスを取得
     *
     * @param $id
     * @return string
     */
    public function getFilePath($id): ?string
    {
        $file_path = Wiki::select('file_path')
            ->where('id', $id)
            ->first();

        return $file_path->file_path;
    }

    /**
     * IDでファイルを削除
     *
     * @param $id
     * @return void
     */
    public function deleteFilePath($id): void
    {
        $get_file_path = $this->getFilePath($id);
        if ($get_file_path) {
            if (Storage::disk('public')->exists($get_file_path)) {
                Storage::disk('public')->deleteDirectory($get_file_path);
            }
        }
    }

    /**
     * IDでユーザー名を取得
     *
     * @param $id
     * @return string
     */
    public function getUserNameById($id): string
    {
        $user = User::select('name')->where('id', $id)->first();
        return $user->name;
    }

    /**
     * Wikiデータの削除
     *
     * @param $id
     * @return bool
     */
    public function deleteWikiData($id): bool
    {
        try {
            DB::beginTransaction();

            if ($id !== "1") {
                $wiki = Wiki::find($id);

                $file_path = $wiki->file_path;
                if ($file_path && Storage::disk('public')->exists($file_path)) {
                    Storage::disk('public')->deleteDirectory($file_path);
                }

                $wiki->delete();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 修飾子の取得
     *
     * @return object
     */
    public function getModifier($id): object
    {
        return Wiki::select('modifier')->where('id', $id)->first();
    }

    /**
     * すべての Wiki データを取得する
     *
     * @return object
     */
    public function getAllWikiData(): object
    {
        return Wiki::get();
    }
}
