<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use App\Models\User;

class CommentRequest extends FormRequest
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
        $user_list = implode(',' , User::pluck('id')->toArray());
        if ($this->method() == 'POST') {
            $rules = [
                'comment_assignee'        => 'required|numeric|in:' . ($user_list),
                'comment_content'         => 'required|string|max:1000'
            ];
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
            $messages['comment_assignee.required'] = 'The mentioned people is required.';
            $messages['comment_assignee.max']      = 'The mentioned people must be less than 255 characters.';
            $messages['comment_assignee.in']       = 'It is outside of the person in charge.';
            $messages['comment_content.required']  = 'The comment content is required.';
            $messages['comment_content.max']       = 'The comment content must be less than 1000 characters.';
        }else {
            $messages['comment_assignee.required'] = 'メンションしたい人は必須です。';
            $messages['comment_assignee.max']      = 'メンションしたい人は255文字以下の文字列を指定してください。';
            $messages['comment_assignee.in']       = '担当者範囲外です。';
            $messages['comment_content.required']  = 'コメント内容は必須です。';
            $messages['comment_content.max']       = 'コメント内容は1000文字以下の文字列を指定してください。';
        }
        return $messages;
    }
}
