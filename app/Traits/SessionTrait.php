<?php

namespace App\Traits;

use Log;

/**
 * セッショントレイト
 *
 * セッションの登録・取得・削除など共通部品
 */
trait SessionTrait
{
    /**
     * セッションデータを取得する
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $session_data = session($this->session_key);
        if (isset($session_data[$key])) {
            return $session_data[$key];
        }
        return null;
    }

    /**
     * セッションデータを取得する
     *
     * @return array ログインセッションデータ配列
     */
    public function getSessionAry(): array
    {
        $session_data = session($this->session_key);
        if (!$session_data) {
            $session_data = [];
        }
        return $session_data;
    }

    /**
     * セッションデータを保存する
     *
     * @param array $data
     * @return array
     */
    public function setSession(array $data): array
    {
        $session_data = $this->getSessionAry();
        $session_data = array_merge($session_data, $data);
        session([
            $this->session_key => $session_data,
        ]);
        return $session_data;
    }

    /**
     * セッションデータを破棄する
     *
     * @return void
     */
    public function deleteSession(): void
    {
        session()->forget($this->session_key);
    }

}
