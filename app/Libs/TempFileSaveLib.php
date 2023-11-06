<?php

namespace App\Libs;

use Illuminate\Http\UploadedFile;

class TempFileSaveLib
{
    /**
     * @var フォルダ
     */
    private $folder;

    /**
     * @var ディスク
     */
    private $disk;

    /**
     * 新しいライブラリインスタンスを作成
     *
     * @param string $folder フォルダ名
     * @param string $disk ディスク名
     */
    public function __construct(string $folder, string $disk)
    {
        $this->folder = $folder;
        $this->disk = $disk;
    }

    /**
     * ファイル保存プロセス
     *
     * @param UpladedFile ファイル
     * @return string ファイルパス
     */
    public function store(UploadedFile $file)
    {
        // ファイルがnullでないかどうかを確認します
        if ($file) {
            // ファイル名を取得
            $file_name = $file->getClientOriginalName();
            // ファイルを保存する
            $filePath = $file->storeAs(
                $this->folder,
                $file_name,
                $this->disk
            );
        }

        return $filePath;
    }

}
