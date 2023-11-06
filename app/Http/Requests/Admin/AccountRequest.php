<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\App;
use App\Constants\GeneralConst;

class AccountRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [];
        $role_list = implode(",", array_keys(GeneralConst::ROLE_LIST));
        if ($this->method() == 'POST') {
            $rules['email'] = [
                'required',
                'string',
                'max:255',
                'mailaddress',
                Rule::unique('users')
                    ->ignore($this->account_id)
                    ->where('users.email', $this->input('email'))
            ];
            $rules['name'] = 'required|string|max:255';
            $rules['role'] = "required|int:in($role_list)";
        }
        return $rules;
    }

    /**
     * バリデーターエラーのカスタムメッセージを取得
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];
        if (App::isLocale('en')) {
            $messages['email.required'] = 'User ID (email address) is required.';
            $messages['email.string'] = 'Specify a character string for the user ID (email address).';
            $messages['email.mailaddress'] = 'Specify an email address in the correct format for the user ID (email address).';
            $messages['email.max'] = 'Specify a number less than :max for the user ID (email address).';
            $messages['email.unique'] = 'That user ID (email address) is already in use.';
        } else {
            $messages['email.required'] = 'ユーザID(メールアドレス) は必須です。';
            $messages['email.string'] = 'ユーザID(メールアドレス）には文字列を指定してください。';
            $messages['email.mailaddress'] = 'ユーザID(メールアドレス）には正しい形式のメールアドレスを指定してください。';
            $messages['email.max'] = 'ユーザID(メールアドレス）には:max以下の数値を指定してください。';
            $messages['email.unique'] = 'そのユーザID(メールアドレス）はすでに使われています。';
        }
        return $messages;
    }
}
