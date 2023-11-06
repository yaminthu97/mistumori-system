<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
{
    /**
     * ユーザーがこのリクエストを行う権限を与えられているかどうかを判断する
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * リクエストに適用される検証ルールを取得
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if ($this->method() == 'POST') {
            $rules['email'] = [
                'required',
                'string',
                'mailaddress',
                Rule::exists('admin_accounts', 'email')->whereNull('deleted_at')
            ];
        }

        return $rules;
    }

    /**
     * バリデーションメッセージ
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        if (App::isLocale('en')) {
            $messages['email.exists'] = 'Invalid email address.';
        } else {
            $messages['email.exists'] = '無効なメールアドレスです。';
        }

        return $messages;
    }
}
