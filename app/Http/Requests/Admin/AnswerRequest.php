<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Constants\GeneralConst;

class AnswerRequest extends FormRequest
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
        $inquiry_status_list = implode(",", array_keys(GeneralConst::INQUIRY_STATUS));
        $user_list = implode(',' , User::pluck('id')->toArray());

        if ($this->method() == 'POST') {
            return [
                'answer_content' => "required|max:1000",
                'inquiry_status' => 'required|numeric|in:' . $inquiry_status_list,
                'question_assignee' => 'required|numeric|in:' . $user_list
            ];
        }
        return [];
    }

    public function messages()
    {
        if (App::isLocale('en')) {
            $messages['question_assignee.in'] = 'It is outside of the person in charge.';
            $messages['inquiry_status.in'] = 'It is outside of the status.';
        } else {
            $messages['question_assignee.in'] = '担当者範囲外です。';
            $messages['inquiry_status.in'] = 'ステータス範囲外です。';
        }

        return $messages;
    }
}
