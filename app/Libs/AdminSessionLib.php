<?php

namespace App\Libs;

use App\Traits\SessionTrait;

/**
 * 管理側共通のセッション処理クラス
 */
class AdminSessionLib
{
    use SessionTrait;
    private $session_key = 'mk_admin_session';

    public function __construct()
    {
    }
}
