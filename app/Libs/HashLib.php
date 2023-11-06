<?php

namespace App\Libs;

/**
 * ハッシュ化クラス
 * 親団体コードなどをハッシュ化する部品クラス
 */
class HashLib
{
    /**
     * 値をハッシュ化し短縮したパラメータに変換する
     *
     * @param string $value 短縮パラメータにしたい値
     * @return string 短縮パラメータ
     */
    public static function value2ShortParam(string $value): string
    {
        return rtrim(strtr(base64_encode(crc32(hash('sha256', $value))), '+/', '-_'), '=');
    }

    /**
     * 短縮パラメータ化した値をcrc32に変換する
     *
     *
     * @param string $short_param 短縮パラメータ
     * @return string crc32に変換した値
     */
    public static function shorParam2Crc32(string $short_param): string
    {
        return base64_decode(str_pad(strtr($short_param, '-_', '+/'), strlen($short_param) % 4, '=', STR_PAD_RIGHT));
    }
}
