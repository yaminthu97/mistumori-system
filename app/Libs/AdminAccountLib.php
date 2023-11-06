<?php

namespace App\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Carbon\Carbon;
use App\Mail\CommonMail;
use App\Models\User;
use App\Constants\GeneralConst;

/**
 * アカウント情報処理クラス
 */
class AdminAccountLib
{
    /**
     * アドミンアカウントIDによりデータ取得
     *
     * @param $admin_account_id アドミンアカウントID
     * @return User
     */
    public function getAdminAccountById($admin_account_id): ?User
    {
        // ログインユーザー判定
        $account = User::find($admin_account_id);
        return $account;
    }

    /**
     * ログインユーザ情報に紐づくデータを取得する
     *
     * @param array $admin_login_data ログインユーザ情報
     * @param array $search_info 検索条件
     * @return Collection|int
     */
    public function getAccountByLoginData(array $search_info, bool $is_count = false): Collection|int
    {
        $query = User::query()->where('id', '!=', GeneralConst::ADMIN_ACCOUNT_ID1);

        if (isset($search_info['name'])) {
            $query->where('name', 'like', '%' . addcslashes($search_info['name'], '%_\\') . '%');
        }

        if (isset($search_info['email'])) {
            $query->where('email', 'like', '%' . addcslashes($search_info['email'], '%_\\') . '%');
        }

        if ($is_count) {
            $accounts = $query->count();
        } else {
            $accounts = $query->limit(GeneralConst::ADMIN_ACCOUNT_LIST_DISPLAY_MAX_LIMIT)->get();
        }
        return $accounts;
    }

    /**
     * 選択した複数アドミンアカウントを削除
     *
     * @param array $admin_account_ids チェックボックスからのアドミンアカウントIDリスト
     * @return bool
     */
    public function deleteSelectedAccounts(array $admin_account_ids): bool
    {
        try {
            DB::beginTransaction();

            // 複数アドミンアカウント削除
            User::whereIn('id', $admin_account_ids)->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /** アドミンアカウントIDと該当データを削除
     *
     * @param $admin_account_id アドミンアカウントID
     * @return bool
     */
    public function deleteAdminAccount($admin_account_id): bool
    {
        try {
            DB::beginTransaction();

            // アドミンアカウント削除
            $admin_account = User::where('id', $admin_account_id);
            $admin_account->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * アドミンアカウント保存
     *
     * @param array $account_data アドミンアカウント情報
     * @return void
     */
    public function saveAdminAccountData(array $account_data)
    {
        try {
            DB::beginTransaction();

            $new_password = $this->makePassword();
            $role_id = $account_data['role'];
            $admin_account_data = array(
                'role' => $role_id,
                'login_id' => $account_data['email'],
                'password' => Hash::make($new_password),
                'name' => $account_data['name'],
                'email' => $account_data['email'],
                'created_user_id' => Auth::user()->id
            );

            $admin_account = $account_data['account_id'] ? User::find($account_data['account_id']) : new User();
            $admin_account->fill($admin_account_data)->save();

            $mail_title = 'アカウント情報';
            $mail_subject = '名前　：' . $admin_account['name'] . "\nパスワード　：" . $new_password;
            Mail::to($admin_account['email'])->send(new CommonMail($mail_title, $mail_subject));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof TransportException) {
                return false;
            }
            throw $e;
        }
    }

    /**
     * パスワードリセットメール送信
     *
     * @param array $account_data アドミンアカウント情報
     * @return bool
     */
    public function sendResetPasswordMail(array $account_data): bool
    {
        try {
            DB::beginTransaction();

            // メール存在チェック
            $query = User::query();
            $admin_account = $query->where('email', $account_data['email'])->first();
            if (!$admin_account) {
                abort(400);
            }

            do {
                $new_password = $this->makePassword();
            } while (Hash::check($new_password, $admin_account->password));

            $admin_account_data = array(
                'password' => Hash::make($new_password),
                'password_modified_at' => Carbon::now()
            );

            $admin_account_id = $admin_account['id'];
            $admin_account = User::find($admin_account_id);
            $admin_account->fill($admin_account_data)->save();

            // リセットパスワードメール送信
            $mail_title = '【リセットパスワード】アカウント情報';
            $mail_subject = '名前　：' . $admin_account['name'] . "\nパスワード　：" . $new_password;
            Mail::to($admin_account['email'])->send(new CommonMail($mail_title, $mail_subject));

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof TransportException) {
                return false;
            }
            throw $e;
        }
    }

    /**
     * パスワードを生成する
     *
     * アルファベット大文字、小文字、数字を全て1文字以上含む、計8文字
     *
     * @param integer $password_length パスワードの文字数
     * @return string
     */
    public function makePassword(int $password_length = 16): string
    {
        // 必須のアルファベット大文字、小文字、数字の3文字取得
        $upper  = collect(range('A', 'Z'))->random(1)->all();
        $lower  = collect(range('a', 'z'))->random(1)->all();
        $num    = collect(range('0', '9'))->random(1)->all();

        // 必須の分を文字列にする
        $password = implode(array_merge($upper, $lower, $num));

        // 残りの文字数の分をアルファベット大文字、小文字、数字から適当に取得
        $str = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $add_length = $password_length - strlen($password);
        for ($i = 0; $i < $add_length; $i++) {
            $password .= $str[rand(0, count($str) - 1)];
        }

        // シャッフル
        return str_shuffle($password);
    }

    /**
     * アカウント CSVダウンロード
     *
     * @param array $account_data_id
     * @return BinaryFileResponse
     */
    public function accountDownloadCSV(array $account_data_id): BinaryFileResponse
    {
        $query = User::query()
            ->whereIn('id', $account_data_id)
            ->get();
        $folder_path = storage_path(GeneralConst::TEMP_FOLDER_PATH);
        $fileName = GeneralConst::ACCOUNT_CSV_NAME;
        $csv_title = GeneralConst::ACCOUNT_CSV_HEADER;

        if (!is_dir($folder_path)) {
            $oldmask = umask(0);
            mkdir($folder_path, 0777);
            umask($oldmask);
        }
        $file = fopen($folder_path . $fileName, 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($file, mb_convert_encoding($csv_title, 'UTF-8'));
        foreach ($query as $accountField) {
            $account_row = [];
            $account_row['id'] = $accountField->id;
            $account_row['name'] = $accountField->name;
            $account_row['email'] = $accountField->email;
            $account_row['role_id'] = GeneralConst::ROLE_LIST[$accountField->role_id];

            fputcsv($file, mb_convert_encoding($account_row, 'UTF-8'));
        }
        fclose($file);
        chmod($folder_path . $fileName, 0777);

        return response()->download($folder_path . $fileName);
    }

    /**
     * アドミンアカウントIDによりデータ取得
     *
     * @param $admin_account_id アドミンアカウントID
     * @return bool
     */
    public function addCsvToDatabase($account_data): ?bool
    {

        try {
            DB::beginTransaction();

            User::insert($account_data); //Upload csv account data row to database

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
