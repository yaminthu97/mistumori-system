<?php

namespace App\Http\Requests\Rules;

use  App\Constants\GeneralConst;
use DateTime;

class CustomValidator extends \Illuminate\Validation\Validator
{

    /**
     *半角ｶﾅのみのバリデーション

     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool カタカナ（半角）のみならtrue、そうでないならfalse
     */
    public function validateKana($attribute, $value, $parameters)
    {
        return preg_match('/^[ｦ-ﾟ]+$/u', $value);
    }

    /**
     * 半角ｶﾅ・英数字のチェック
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateKanaAlphaNum($attribute, $value)
    {
        return (preg_match("/^[a-zA-Z0-9ｦ-ﾟ]+$/u", $value));
    }

    /**
     * 半角ｶﾅ・英数字・記号のチェック
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateKanaAlphaNumSymbols($attribute, $value)
    {
        return (preg_match("/^[a-zA-Z0-9!@#$%^&*()_+\-=[\]{};?.ｦ-ﾟ\ ]+$/u", $value));
    }

    /**
     * 住所フリガナ用（フリガナ・数字・ハイフン・アポストロフィー）のバリデーション
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAddressKana($attribute, $value, $parameters)
    {
        return preg_match('/^[0-9 ｦ-ﾟ\-]+$/u', $value);
    }

    /**
     * メールアドレスバリデーション用
     * Laravelデフォのメールアドレスバリデーションルールだとイマイチなので
     */
    public function validateMailaddress($attribute, $value, $parameters)
    {
        return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\?\*\[|\]%'=~^\{\}\/\+!#&\$\._-])*@([a-zA-Z0-9_-])+\.([a-zA-Z0-9\._-]+)+$/", $value);
    }

    /**
     *　select入力項目バリデーション
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool 入力可能項目に含まれていない値の場合エラー
     */
    public function validateSelectCheck($attribute, $value, $parameters)
    {
        //入力可能項目取得
        $input_items = constant('GeneralConst::COMMON_SELECT_' . strtoupper($attribute));
        //入力可能項目に存在する値か
        return in_array($value, array_column($input_items, 'value'));
    }

    /**
     *　select入力項目バリデーション
     * @param $attribute
     * @param $value
     * @param $parameters 0:バリデーション対象位置
     * @return bool 入力可能項目に含まれていない値の場合エラー
     */
    public function validateSelectCheckArr($attribute, $value, $parameters)
    {
        $attribute_list = explode('.', $attribute);
        $target_attribute = $attribute_list[$parameters[0]];
        //入力可能項目取得
        $input_items = constant('GeneralConst::COMMON_SELECT_' . strtoupper($target_attribute));
        //入力可能項目に存在する値か
        return in_array($value, array_column($input_items, 'value'));
    }

    /** 住所フリガナ用（フリガナ・数字・ハイフン・アポストロフィー）のバリデーション
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateSelectableJobCode($attribute, $value, $parameters)
    {
        return preg_match('/^[0-9 ｦ-ﾟ\-]+$/u', $value);
    }

    /**
     *氏名カナ半角ｶﾅ、スペースのみのバリデーション

     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool カタカナ（半角）＋半角スペース＋カタカナ（半角）であればtrue、そうでないならfalse
     */
    public function validateFullNameKana($attribute, $value, $parameters)
    {
        return preg_match('/^[ｦ-ﾟ ]/u', $value);
    }

    /**
     * 入力文字チェック　冒頭桁不備
     * 先頭文字が「0」以外の場合はNG
     *
     * @param string $val
     * @return boolean
     */
    public static function validateTelPrefixZero($attribute, $value, $parameters)
    {
        return preg_match('/^0/', $value) === 1;
    }

    /**
     * 入力文字チェック　Only half-width numbers with hyphen
     * If no hypen or not numbers or the input ends with hyphen, it's NG.
     *
     * @param string $val
     * @return boolean
     */
    public static function validateTelDigitsHyphen($attribute, $value, $parameters)
    {
        return preg_match('/^[0-9]+(-[0-9]+)+$/u', $value) === 1;
    }

    /**
     * パスワード条件文字チェック
     * 半角英数字+大文字があること
     *
     * @param string $val
     * @return boolean
     */
    public static function validatePasswordCheck($attribute, $value, $parameters)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\d).+$/', $value) === 1;
    }

    /**
     * 英数字のチェック
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateAlphaNum($attribute, $value)
    {
        return (preg_match("/^[a-zA-Z0-9ｦ-ﾟ]+$/", $value));
    }

    /**
     *半角ｶﾅのみのバリデーション

     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool 「全角または半角文字が含まれている場合はtrue、それ以外の場合はfalseを出力する。」
     */
    public function validateFullHalfKana($attribute, $value, $parameters)
    {
        return preg_match('/^[^\x01-\x7E]*$|^[\x20-\x7E]*$/', $value);
    }

    /**
     * 1か月の最初の日付を検証
     *
     * @param $value
     * @param string $attribute
     */
    function validateFirstDayOfMonth($attribute, $value)
    {
        // PHPの内蔵DateTimeクラスを使用して入力日を解析する
        $date = DateTime::createFromFormat('Y/m/d', $value);
        // 日付が月の初日であるかどうかを確認する
        if ($date !== false  && $date->format('d') == GeneralConst::FIRST_DAY_OF_MONTH) {
            return true; // 検証が成功
        }
        return false; // 検証に失敗
    }

    /**
     * グループ名を検証
     *
     * @param $value
     */
    public function validateGroupNameAlphaNum($attribute, $value, $parameters)
    {
        return preg_match('/^[０-９ 0-9 ｦ-ﾟa-zA-Zあ-んア-ンァ-ヶａ-ｚＡ-Ｚ一-龯 \s]+$/u', $value);
    }

    /**
     * プロシージャステータスページでfrom_numberとto_numberを検証
     *
     * @param $attribute, $value, $parameters, $validator
     * @return boolean
     */
    public function validateAfterNumber($attribute, $value, $parameters, $validator)
    {
        $from_number = intval($value);
        $to_number = intval($validator->getData()[$parameters[0]]);

        if ($to_number >= $from_number) {
            return false;
        }

        return true;
    }
}
