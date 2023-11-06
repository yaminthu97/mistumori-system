<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Constants\GeneralConst;

class EstimateRequest extends FormRequest
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
        $estimate_status_list = implode(",", array_keys(GeneralConst::PROJECT_STATUS));
        $user_list = implode(',' , User::pluck('id')->toArray());
        if ($this->method() == 'POST') {
            $rules = [
                'estimation_content'    => 'required|string|max:1000',
                'PG_man_months'         => 'required|regex:/^\d+(\.\d{1,2})?$/|numeric|min:1.00|max:99.99',
                'BSE_man_months'        => 'required|regex:/^\d+(\.\d{1,2})?$/|numeric|min:1.00|max:99.99',
                'estimate_assignee'     => 'required|numeric|in:' . ($user_list),
                'status'                => 'required|numeric|in:' . ($estimate_status_list)
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
            $messages['estimation_content.required']    = 'Estimated Content is required.';
            $messages['PG_man_months.required']         = 'PG man hours is required.';
            $messages['BSE_man_months.required']        = 'BSE man hours is required.';
            $messages['estimate_assignee.required']     = 'Person in charge is required.';
            $messages['status.required']                = 'Status is required.';
            $messages['estimation_content.max']         = 'Estimated Content must be less than 1000 characters.';
            $messages['estimate_assignee.max']          = 'Person in charge must be less than 255 characters.';
            $messages['status.max']                     = 'Status must be less than 255 characters.';
            $messages['PG_man_months.regex']            = 'Invalid value';
            $messages['BSE_man_months.regex']           = 'Invalid value';
            $messages['PG_man_months.min']              = 'PG man hours must be 1.00 or higher.';
            $messages['PG_man_months.max']              = 'PG man hours must be less than or equal to 99.99.';
            $messages['BSE_man_months.min']             = 'BSE man hours must be 1.00 or higher.';
            $messages['BSE_man_months.max']             = 'BSE man hours must be less than or equal to 99.99.';
            $messages['estimate_assignee.in']           = 'It is outside of the person in charge.';
            $messages['status.in']                      = 'It is outside of status.';
        }else {
            $messages['estimation_content.required']    = '見積内容は必須です。';
            $messages['PG_man_months.required']         = 'PG工数は必須です。';
            $messages['BSE_man_months.required']        = 'BSE工数は必須です。';
            $messages['estimate_assignee.required']     = '担当者は必須です。';
            $messages['status.required']                = 'ステータスは必須です。';
            $messages['estimation_content.max']         = '見積内容は1000文字以下の文字列を指定してください。';
            $messages['estimate_assignee.max']          = '作成者は255文字以下の文字列を指定してください。';
            $messages['status.max']                     = 'ステータスは255文字以下の文字列を指定してください。';
            $messages['PG_man_months.regex']            = '無効な 10 進数値です。';
            $messages['BSE_man_months.regex']           = '無効な 10 進数値です。';
            $messages['PG_man_months.min']              = 'PG工数は1.00以上でなければなりません。';
            $messages['PG_man_months.max']              = 'PG工数は99.99以下でなければなりません。';
            $messages['BSE_man_months.min']             = 'BSE工数は1.00以上でなければなりません.';
            $messages['BSE_man_months.max']             = 'BSE工数は99.99以下でなければなりません。';
            $messages['estimate_assignee.in']           = '担当者範囲外です。';
            $messages['status.in']                      = 'ステータス範囲外です。';
        }

        return $messages;
    }
}
