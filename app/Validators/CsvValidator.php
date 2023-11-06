<?php

namespace App\Validators;

use App\Constants\GeneralConst;
use Illuminate\Support\Facades\Validator;

/**
 * CSVバリエーション
 */
class CsvValidator
{
    private $csvData;
    private $rules;
    private $headingRow;
    private $errors;
    private $headingKeys = [];
    private $out_of_bound_data;
    private $db_array_key_worng;

    public function construct() {}

    /**
     * CSVファイルを読み込む
     *
     * @param $csvPath CSVファイルパス
     * @param $rules バリデーションチェックルール
     * @param string $encoding エンコーディング
     * @return $this
     * @throws \Exception
     */
    public function open($csvPath, $rules, $encoding = 'UTF-8')
    {
        $this->csvData = [];
        $this->setRules($rules);

         // ファイルは空で、空白のみが含まれてる
        $fileContent = file_get_contents($csvPath);
        $fileContent = preg_replace('/^\x{EF}\x{BB}\x{BF}/', '', $fileContent); // remove BOM
        $fileContent = preg_replace('/[\s,]+/', '', $fileContent);

        if (empty($fileContent)) {
            session(['algMsg' => ('No Data Found')]);
            return redirect()->route('admin.account.index');
        }

        $valid_encoding = mb_check_encoding(file_get_contents($csvPath), $encoding);
        $csvData = $this->getCsvAsArray($csvPath, $valid_encoding);

        if (empty($csvData)) {
            session(['algMsg' => ('No Data Found')]);
            return redirect()->route('admin.account.index');
            // このCSVファイルはCSVブランクの作成
            throw new \Exception('No data found.');
        }

        $newCsvData = [];
        $ruleKeys = array_keys($this->rules);
        foreach ($csvData as $rowIndex => $csvValues) {
            foreach ($ruleKeys as $ruleKeyIndex) {
                $newCsvData[$rowIndex][$ruleKeyIndex] = $csvValues[$this->headingRow[$ruleKeyIndex]];
            }
        }

        $this->csvData = $newCsvData;

        return $this;
    }

    /**
     * CSVファイルのデータを配列で取得
     *
     * @param string $filePath ファイルパス
     * @param string $valid_encoding エンコードが有効かどうか
     * @param string $delimiter デリミタ
     * @return array
     */
    public function getCsvAsArray($filePath, $valid_encoding, $delimiter=',')
    {
        $rowKeys = GeneralConst::ADMIN_ACCOUNT_COLUMN;
        $checkout_of_bound_row = GeneralConst::CSV_OUT_OF_BOUND_ROW; // バウンドのないために保存された行番号
        $admin_acount_dbcolumn_keys = GeneralConst::ADMIN_ACCOUNT_COLUMN_KEYS; // チェックする配列を作成するデータベーステーブル列
        $first_row = true; // CSVの最初の行、タイトル行
        $formattedData = []; //  検証済みのCSV行を保存
        $title_order_wrong = GeneralConst::TITLE_ORDER_WRONG;

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== FALSE) {

                // 行の各セルにトリムを適用する
                $row = array_map('trim', $row);

                // 行のバウンドからのゴミデータがあるかどうかを確認
                if (count($row) > 5) {

                    $dataGarbage = array_slice($row, 5, count($row) - 1);

                    foreach ($dataGarbage as $garbage)
                        if (trim($garbage) != '') {
                            $this->out_of_bound_data = $checkout_of_bound_row;
                        }
                }
                foreach ($row as $index => $column) {
                    // UTF-8でない場合は、エンコーディングをshift-jisに変換します
                    $row[$index] = $valid_encoding ? $column : iconv("SJIS", "UTF-8", $column);
                }
                if(!$rowKeys) {
                    // BOM対策
                    $rowKeys = preg_replace('/^\xEF\xBB\xBF/','', $row);
                } else {

                    // 不明なエラーを引き起こすCSVのスペースを切り取る
                    $row = array_slice($row, 0, 5);

                    if($first_row){
                        if (!empty($this->out_of_bound_data)) {
                            $this->out_of_bound_data = 'first_row_out_of_bound';
                        }
                        $first_row = false;
                        $row = preg_replace('/^\xEF\xBB\xBF/','', $row);
                         $array1Lower = array_map('strtolower', $admin_acount_dbcolumn_keys);
                         $array2Lower = array_map('strtolower', $row);

                        // タイトルをチェック
                         for ($i = 0; $i <= 4; $i++) {
                           if( $array1Lower[$i] != $array2Lower[$i]){
                            $title_order_wrong = 'O';
                            session(['algMsg' => 'CSVのタイトルが間違った場所にあります。']);
                           }
                        }

                              if ((empty(array_diff($array1Lower, $array2Lower)))) {
                                    continue;
                              }
                              else{

                                $first_title_error = array_diff($array1Lower, $array2Lower);
                                $first_value = array_shift($first_title_error);
                                $this->db_array_key_worng = $first_value; //add first title error
                              }
                    }
                    if (count($row) != count($rowKeys)) {
                        // ここでエラーを処理
                        break;
                    }
                    $formattedData[] = array_combine($rowKeys, $row);
                }
                $checkout_of_bound_row++;
            }

            fclose($handle);
        }

        $allEmpty = true;
        foreach ($formattedData as $row) {
            foreach ($row as $value) {
                if (trim($value) !== '') {
                    $allEmpty = false;
                    break 2;
                }
            }
        }

        if ($allEmpty) {
            // 配列内のすべての値は空
            session(['algMsg' => 'No Data Found']);
        }

        if($title_order_wrong == 'X'){
            // CSVに重複したlogin_idsがあるかどうかを確認
            $login_ids = array_column($formattedData, 'ログインID');
            if (count($login_ids) > 1) {
                $unique_emails = array_unique($login_ids);

                if (count($login_ids) !== count($unique_emails)) {
                    // CSVデータには重複したlogin_idsがあるかどうかを確認

                    // 最初の複製されたメールを取得
                    $first_duplicated_login_id = null;
                    foreach ($unique_emails as $email) {
                        if (in_array($email, array_slice($login_ids, array_search($email, $login_ids) + 1))) {
                            $first_duplicated_login_id = $email;
                            break;
                        }
                    }

                    // 最初の複製されたログインIDで行を見つける
                    $first_duplicated_login_ids_rows = [];
                    foreach ($formattedData as $row_index => $row) {
                        if (trim($row['ログインID']) === $first_duplicated_login_id) {
                            if (trim($row['ログインID'] !== '')) {
                                $first_duplicated_login_ids_rows[] = $row_index;
                            }
                        }
                    }

                    if (isset($first_duplicated_login_ids_rows[0]) && isset($first_duplicated_login_ids_rows[1])) {
                        // 最初の重複ログインIDの配列インデックスを出力する
                        session(['algMsg' => ('CSVの ' . $first_duplicated_login_ids_rows[0] + 2 . ' 行目と ' . $first_duplicated_login_ids_rows[1] + 2 . ' 行目に同じログインIDがあります')]);
                    }
                }
            }

            // CSVの電子メールの一意の行を検証
            $emails = array_column($formattedData, 'メールアドレス');
            if (!empty($emails)) {
                $unique_emails = array_unique($emails);

                if (count($emails) !== count($unique_emails)) {
                    // CSVデータには重複したメールがある

                    // 最初の複製されたメールを取得
                    $first_duplicated_email = null;
                    foreach ($unique_emails as $email) {
                        if (in_array($email, array_slice($emails, array_search($email, $emails) + 1))) {
                            $first_duplicated_email = $email;
                            break;
                        }
                    }

                    // 最初の複製された電子メールで行を見つける
                    $first_duplicated_email_rows = [];
                    foreach ($formattedData as $row_index => $row) {

                        if (trim($row['メールアドレス']) === $first_duplicated_email) {
                            if (trim($row['メールアドレス'] !== '')) {
                                $first_duplicated_email_rows[] = $row_index;
                            }
                        }
                    }

                    // 最初に複製された電子メールの配列インデックスを出力する
                    if (isset($first_duplicated_email_rows[0]) && isset($first_duplicated_email_rows[1])) {
                        session(['algMsg' => ('CSVの ' . $first_duplicated_email_rows[0] + 2 . ' 行目と ' . $first_duplicated_email_rows[1] + 2 . ' 行目に同じメールアドレスがあります')]);
                    }
                }
            }
        }

        return $formattedData;
    }

    /**
     * バリデーションチェック
     *
     * @return void
     */
    public function fails()
    {
        $errors = [];
        foreach ($this->csvData as $rowIndex => $csvValues) {

            // 行がすべて空白かどうかを確認
            if (count(array_filter($csvValues)) === 0) {
                continue;
            }
            $validator = Validator::make($csvValues, $this->rules);
            if (!empty($this->headingRow)) {
                $validator->setAttributeNames($this->headingRow);
            }
            if ($validator->fails()) {
                $errors[$rowIndex] = $validator->messages()->toArray();
            }
        }
        // CSVタイトルのエラーメッセージ
        if (!empty($this->db_array_key_worng)) {
            session(['algMsg' => ( $this->db_array_key_worng . 'のスペルが間違っています。または、タイトル列が空白であってはなりません。' )]);
        }
        // バウンドデータがない行のエラーメッセージ
            if (!empty($this->out_of_bound_data)) {
                if($this->out_of_bound_data == 'first_row_out_of_bound'){
                    session(['algMsg' => ('CSV row 1 行目には範囲外のデータがあります。')]);

                } else {
                    session(['algMsg' => ('CSV row ' . $this->out_of_bound_data +1 . ' 行目には範囲外のデータがあります。')]);

                }
            }

        $this->errors = $errors;

        return (!empty($this->errors));
    }

    /**
     * エラー取得
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * CSVデータ取得
     *
     * @return array
     */
    public function getData()
    {
        return $this->csvData;
    }

    /**
     * CSV項目名設定
     *
     * @param  mixed $attribute_names
     * @return void
     */
    public function setAttributeNames($attribute_names)
    {
        $this->headingRow = $attribute_names;
    }

    /**
     * ルール設定
     */
    private function setRules($rules)
    {
        $this->rules = $rules;
        $this->headingKeys = array_keys($rules);
    }
    
}
