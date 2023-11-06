<?php

use Carbon\Carbon;
use App\Constants\GeneralConst;

if (!function_exists('jsCssForceReload')) {
    /**
     * JavascriptやCssファイルをキャッシュから読まないようにするための
     * サフィックス文字列を生成する
     *
     * @param string $text
     * @return string
     */
    function jsCssForceReload(): string
    {
        return "?" . strtotime('now');
    }
}

if (!function_exists('hashCombine')) {
    /**
     * CakePHPのHash::Combineもどき
     *
     * ↓ こんな配列を
     * [
     *     [
     *         'code' => 'A1',
     *         'name' => '山田',
     *         'age' => 25,
     *     ],
     *     [
     *         'code' => 'B2',
     *         'name' => '鈴木',
     *         'age' => 37,
     *     ],
     * ]
     * ↓ こんな形に整形する
     * [
     *     'A1' => [
     *         'code' => 'A1',
     *         'name' => '山田',
     *         'age' => 25,
     *     ],
     *     'B2' => [
     *         'code' => 'B2',
     *         'name' => '鈴木',
     *         'age' => 37,
     *     ],
     * ]
     * ↓ $values = ['name', 'age'] とか指定すればこうなる
     * [
     *     'A1' => [
     *         'name' => '山田',
     *         'age' => 25,
     *     ],
     *     'B2' => [
     *         'name' => '鈴木',
     *         'age' => 37,
     *     ],
     * ]
     *
     * @param \Illuminate\Database\Eloquent\Collection $collection
     * @param string $key 配列のキーにしたい要素
     * @param mixed $values 取得する項目を絞る場合に使う
     * @return array
     */
    function eloquentHashCombine(Illuminate\Database\Eloquent\Collection $collection, string $key, $values = []): array
    {
        if (!$collection) {
            return [];
        }
        return $collection->mapWithKeys(function (Illuminate\Database\Eloquent\Model $item) use ($key, $values) {
            if (count($values) === 0) {
                return [
                    $item[$key] => $item->ToArray(),
                ];
            }
            return [
                $item[$key] => $item->only($values),
            ];
        })->ToArray();
    }
}

if (!function_exists('mbTrim')) {
    /**
     * マルチバイトの文字列の前後にある空白文字(スペース・タブ)を取り除く
     *
     * @param string $text
     * @return void
     */
    function mbTrim(string $text = null)
    {
        if ($text === null) {
            return '';
        }
        return mb_ereg_replace("^[\s　]+|[\s　]+$", '', $text);
    }
}

if (!function_exists('seirekiToWareki')) {
    /**
     * 西暦を和暦に変換
     *
     * @param  int  西暦
     * @param  int  月
     * @return string  和暦
     */
    function seirekiToWareki(int $year, $month = null)
    {
        $japanese_calendar_list = [
            ['year' => 2018, 'name' => '令和'],
            ['year' => 1988, 'name' => '平成'],
            ['year' => 1925, 'name' => '昭和'],
            ['year' => 1911, 'name' => '大正'],
            ['year' => 1867, 'name' => '明治']
        ];

        $wareki = '';
        foreach ($japanese_calendar_list as $japanese_calendar) {
            $base_year = $japanese_calendar['year'];
            $year_name = $japanese_calendar['name'];

            if ($year_name === '令和' && $year === 2019 && $month !== null) {
                // 2019年4月までは平成、5月からは令和
                if ($month <= 4) {
                    continue;
                }
            }

            if ($year > $base_year) {
                $japanece_year = $year - $base_year;

                $wareki =  $year_name . sprintf("%02d", $japanece_year) . '年';
                break;
            }
        }
        return $wareki;
    }
}

if (!function_exists('convertSeirekiToWareki')) {
    /**
     * Datetime形式(例：2019-10-16 00:00:00) -> 2019年10月16日　に変換する
     * @param string datetime
     * @return string  和暦(令和〇年〇月〇日)
     */
    function convertSeirekiToWareki(string $datetime)
    {
        $japanese_calendar_list = [
            ['year' => 2018, 'name' => '令和'],
            ['year' => 1988, 'name' => '平成'],
            ['year' => 1925, 'name' => '昭和'],
            ['year' => 1911, 'name' => '大正'],
            ['year' => 1867, 'name' => '明治']
        ];

        $carbon = new Carbon($datetime);
        $year = $carbon->year;
        $month = $carbon->month;
        $day = $carbon->day;

        $wareki = '';
        foreach ($japanese_calendar_list as $japanese_calendar) {
            $base_year = $japanese_calendar['year'];
            $year_name = $japanese_calendar['name'];

            if ($year_name === '令和' && $year === 2019 && $month !== null) {
                // 2019年4月までは平成、5月からは令和
                if ($month <= 4) {
                    continue;
                }
            }

            if ($year > $base_year) {
                $japanece_year = $year - $base_year;

                $wareki =  $year_name . sprintf("%02d", $japanece_year) . '年'.$month.'月'.$day.'日';
                break;
            }
        }
        return $wareki;
    }
}

if (!function_exists('warekiFormat')) {
    //例：H091026
    function warekiFormat($wareki)
    {
        $wareki_name = mb_substr($wareki, 0, 2);
        $wareki_year = mb_substr($wareki, 2, 2);

        if ($wareki_name === '大正') { //大正

            $wareki_name = 'T';
        } else if ($wareki_name === '昭和') { //昭和

            $wareki_name = 'S';
        } else if ($wareki_name === '平成') { //平成

            $wareki_name = 'H';
        } else if ($wareki_name === '令和') { //令和

            $wareki_name = 'R';
        }

        return $wareki_name . $wareki_year;
    }
}

if (!function_exists('warekiToSeireki')) {
    /**
     * 和暦(H120101)を西暦(20000101)に変換
     *
     * @param  String  和暦(H120101)
     * @return string  西暦(20000101)
     */
    function warekiToSeireki(String $wareki)
    {
        if (preg_match('!^(T|S|H|R)([0-9]+)$!', $wareki, $matches)) {

            $era_name = $matches[1];
            $year = intval(mb_substr($matches[2], 0, 2));

            if ($era_name === 'T') {
                $year += 1911;
            } else if ($era_name === 'S') {
                $year += 1925;
            } else if ($era_name === 'H') {
                $year += 1988;
            } else if ($era_name === 'R') {
                $year += 2018;
            }

            return (string)$year . mb_substr($matches[2], 2, 2) . mb_substr($matches[2], 4, 2);
        }
        return "";
    }
}

if (!function_exists('dateToWareki')) {
    /**
     * (2000-01-01)を和暦(H120101)に変換
     *
     * @param  String  (2000-01-01)
     * @return string  和暦(H120101)
     */
    function dateToWareki(String $value)
    {
        if (!$value) return "";
        if (str_contains($value, "-") || str_contains($value, "/")) {
            $value = str_replace("-", "", $value);
            $value = str_replace("/", "", $value);
            $carbon = new Carbon($value);
            $month_day = $carbon->isoFormat('MMDD');

            $wareki = seirekiToWareki(substr($value, 0, 4));
            $wareki_name = warekiFormat($wareki);
            return $wareki_name . $month_day;
        }
        return $value;
    }
}

if (!function_exists('toManEn')) {
    /**
     * 金額を万円単位にする
     * 150000[円] → 15[万円]
     *
     * @param integer $yen
     * @return integer
     */
    function toManEn(int $yen): int
    {
        $man_en = floor($yen * 0.0001);
        return $man_en;
    }
}

if (!function_exists('birthdayToAge')) {
    /**
     * 生年月日から年齢を算出
     *
     * @param  Carbon $birthday 生年月日の西暦 Y-m-d
     * @param  Carbon $target_day 年齢算出基準日(保険始期日) Y-m-d
     * @return int $age 年齢
     */
    function birthdayToAge(Carbon $birthday, Carbon $target_day = null)
    {
        $age = '';
        $birthday = $birthday->format("Y-m-d");
        // 年齢算出基準日がなければ現在日時と比較
        if (is_null($target_day)) {
            $target_day = date("Ymd");
        } else {
            $target_day = $target_day->format("Ymd");
        }
        // ハイフンを除去
        $birthday = str_replace("-", "", $birthday);
        $age = intval(floor(($target_day - $birthday) / 10000));

        return $age;
    }
}

if (!function_exists('carrierDateFormat')) {
    /**
     * 日付フォーマット関数。
     *
     * @param  $date 日付(西暦) yyyy-mm-dd または yyyy/mm/dd または yyyymmdd。 yyyy>1867,mm>0,dd>0の場合処理が通り、それ以外はfalseを返す。
     * @param  bool $wareki_flg  trueの場合「和暦〇年〇月〇日」の文字列を返し、 false の場合「20xx年〇月〇日」の文字列を返す。
     * @return string $formatted_date
     */
    function carrierDateFormat($date, bool $wareki_flg = true)
    {
        // 8文字であれば、年、月、日に区切り変数に代入
        if (is_numeric($date) && mb_strlen($date) === 8) {
            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day = substr($date, 6, 2);
            // 8文字以上であれば、/を-に変換し、-で年、月、日に区切りで変数に代入
        } else {
            $date = str_replace('/', '-', $date);
            $separated_date = explode("-", $date);
            $year = $separated_date[0];
            $month = $separated_date[1];
            $day = $separated_date[2];
            if (mb_strlen($day) !== 2) {
                $day = substr($day, 0, 2);
            }
        }

        // yyyy>1867,mm>0,dd>0の場合処理が通り、
        if (($year > 1867) && ($month > 0) && ($day > 0)) {
            // 和暦に変換し返す
            if ($wareki_flg === true) {
                $formatted_date = seirekiToWareki($year, $month) . $month . '月' . $day . '日';
                // 西暦のまま返す
            } else {
                $formatted_date = $year . '年' . $month . '月' . $day . '日';
            }
            return $formatted_date;
        }
        return "";
    }
}

if (!function_exists('yearMonthFormat')) {
    /**
     * 年月表記
     *
     * @param string $date
     * @return string $text
     */
    function yearMonthFormat(string $date, bool $wareki_flg = true)
    {
        $text = carrierDateFormat($date, $wareki_flg);
        if (!$text) {
            return '';
        }
        $text = preg_replace("/(.*月)(\d+日)/", '$1', $text);
        return $text;
    }
}

if (!function_exists('convertDateYmd')) {
    /**
     * Datetime形式(例：2019-10-16 00:00:00) -> 2019年10月16日　に変換する
     */
    function convertDateYmd(string $DateTime)
    {
        $carbon = new Carbon($DateTime);
        return $carbon->isoFormat('YYYY年MM月DD日');
    }
}

if (!function_exists('convertDateJa')) {
    /**
     * Datetime形式(例：2019-10-16 00:00:00) -> 2019年10月16日（水）　に変換する
     */
    function convertDateJa(string $DateTime)
    {
        $carbon = new Carbon($DateTime);
        return $carbon->isoFormat('YYYY年MM月DD日（ddd）');
    }
}

if (!function_exists('convertDatetimeJa')) {
    /**
     * Datetime形式(例：2019-10-16 00:00:00) -> 2019年10月16日（水）00:00　に変換する
     */
    function convertDatetimeJa($DateTime)
    {
        $carbon = new Carbon($DateTime);
        return $carbon->isoFormat('YYYY年MM月DD日（ddd）LT');
    }
}


if (!function_exists('zipcodeformat')) {
    /**
     * 郵便番号を3桁-4桁に分割する
     *
     * @param  string $zipcode
     * @return string
     */
    function zipcodeformat(string $zipcode)
    {
        $code1 = substr($zipcode, 0, 3);
        $code2 = substr($zipcode, 3);
        return "{$code1}-{$code2}";
    }
}

if (!function_exists('getProductSubMenu')) {
    /**
     * 製品サブメニューを取得
     *
     * @param array $products 製品一覧
     * @param $product_id 商品マスタid
     */
    function getProductSubMenu(array $products, $product_id)
    {
        $sub_menu = null;
        foreach ($products as $product) {
            if ($product['id'] == $product_id && isset($product['sub_menu'])) {
                $sub_menu = $product['sub_menu'];
                break;
            }
        }
        return $sub_menu;
    }
}

if (!function_exists('isProductWithCompensation')) {
    /**
     * すべての製品に補償があるかどうかを確認します
     *
     * @param array $products 製品一覧
     */
    function isProductWithCompensation(array $products)
    {
        if (empty($products)) {
            return false;
        }
        foreach ($products as $product) {
            if (empty($product['compensations'])) {
                return false;
            }
            if (!isCompensationWithSet($product['compensations'])) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('isCompensationWithSet')) {
    /**
     * すべての報酬にセットがあるかどうかを確認します
     *
     * @param array $compensations 補償
     */
    function isCompensationWithSet(array $compensations)
    {
        foreach ($compensations as $compensation) {
            if (empty($compensation['sets'])) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('fileName')) {
    /**
     * ファイル名のみ取得
     *
     * @param  string $file_path ファイルパス
     * @return srting ファイル名
     */
    function fileName(string $file_path)
    {
        if (!$file_path) return '';

        $file_paths = explode('/', $file_path);
        $index = sizeof($file_paths) - 1;
        return $file_paths[$index];
    }
}

if (!function_exists('isChange')) {
    /**
     * 比較処理
     *
     * @param array $old_data 前年データ
     * @param array $new_data 申込データ
     * @param string $key_name キー
     * @param string $new_key_name キー
     * @return bool
     *
     */
    function isChange(array $old_data = null, array $new_data = null, string $key_name, string $new_key_name = null)
    {
        $new_key_name = $new_key_name ?? $key_name;
        $old_value = getDisplayValue($old_data,  $key_name);
        $new_value = getDisplayValue($new_data,  $new_key_name);

        return $old_value != $new_value;
    }
}

if (!function_exists('getDisplyValue')) {
    /**
     * 値取得
     *
     * @param array $data 前年データ 又は 申込データ
     * @param string $key_name キー
     * @param string $unit 単位
     * @param array $change_value キーから表示値へ
     * @return bool
     *
     */
    function getDisplayValue(array $data = null, string $key_name, string $unit = null, array $change_value = [])
    {
        $has_value = $data && isset($data[$key_name])
            && (array_key_exists($key_name, $data)
                ? (mbTrim($data[$key_name]) != '') : false) ;
        $return_value = $has_value ? mbTrim($data[$key_name]) : GeneralConst::DEFAULT_DISPLAY;

        if (sizeOf($change_value) && $return_value != GeneralConst::DEFAULT_DISPLAY) {
            if (array_key_exists($return_value, $change_value)) {
                $return_value = $change_value[$return_value];
            }
        }

        if ($unit != null && $return_value != GeneralConst::DEFAULT_DISPLAY) {
            return $return_value . $unit;
        }
        return $return_value;
    }
}

if (!function_exists('getDisplayName')) {
    /**
     * 表示用氏名取得
     *
     * @param array $insured 被保険者情報
     * @param bool $is_style htmlを付けるか
     * 付ける場合：XXXX（太字）＋様、お客さま（太文字）
     * 付けない場合：XXX様、お客さま
     * @return bool
     *
     */
    function getDisplayName(array $insured,bool $is_style = false)
    {
        $honor = null;
        $display_name = 'お客さま';

        if(isset($insured['insured_name'])){
            $honor = '様';
            $display_name = $insured['insured_name'];
        }elseif(isset($insured['insured_family_name_kana']) || isset($insured['insured_given_name_kana'])){
            $honor = '様';
            $display_name = $insured['insured_family_name_kana'].$insured['insured_given_name_kana'];
        }

        if($is_style){
            $display_name = '<strong class="_strong">'.$display_name.'</strong>'.$honor;
        }else{
            $display_name = $display_name.' '.$honor;
        }

        return $display_name;
    }
}

if (!function_exists('normalDateFormat')) {
    /**
     * change wareki to normal date format
     * @param $warekiDate (S010105)
     * @return string (1989/01/01)
     */
    function normalDateFormat(string $warekiDate = null) {
        if($warekiDate) {
            $warekiStr = strval($warekiDate);
            $dateStr = warekiToSeireki($warekiStr);
            return $dateStr;
        }
        return "";
    }
}

if (!function_exists('remainingDateTimeFormat')) {
    /**
     * remaining time date format
     *
     * @param object $datetime
     * @return string
     */
    function remainingDateTimeFormat(object $datetime): string
    {
        $day = $datetime->days;
        $hour = $datetime->h;
        $minute = $datetime->i;

        $formated_remain_date = $day . '日' . $hour  . '時' . $minute  . '分';

        return $formated_remain_date;
    }
}

if (!function_exists('displayDateTimeByTimeZone')) {
    /**
     * display date time by user's browser's time zone
     * @param string $datetime
     * @return string
     */
    function displayDateTimeByTimeZone(string $datetime): string
    {
        $admin_login_session_data = session()->get('mk_admin_session');
        $formatted_date = Carbon::createFromFormat('Y-m-d H:i:s', $datetime)->setTimezone($admin_login_session_data['time_zone'])->format('F j, Y \a\t g:i A');

        return $formatted_date;
    }
}
