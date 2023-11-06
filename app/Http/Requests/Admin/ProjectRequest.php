<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Models\Customer;
use App\Constants\GeneralConst;

class ProjectRequest extends FormRequest
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
        $date = 'date_format:Y/m/d';
        $string = 'string';
        $separator = '|';
        $max = 'max:255';
        $nullable = 'nullable';
        $numeric = 'numeric';
        $customer_in = 'in:' . implode(',', Customer::pluck('customer_name')->toArray());
        $assignee_in = 'in:' . implode(',', User::pluck('name')->toArray());
        $status_in = 'in:' . implode(',', array_keys(GeneralConst::PROJECT_STATUS));

        $rules['project_name'] = $nullable . $separator . $string . $separator . $max;
        $rules['customer_name'] = $nullable . $separator . $string . ($this->filled('customer_name') ? ($separator . $customer_in) : '');
        $rules['assignee'] = $nullable . $separator . $string . ($this->filled('assignee') ? ($separator . $assignee_in) : '');
        $rules['submit_date'] = $nullable . $separator . $date;
        $rules['status'] = $nullable . $separator . $numeric . ($this->filled('status') ? ($separator . $status_in) : '');
        return $rules;
    }

    public function messages()
    {
        if (App::isLocale('en')) {
            $messages['customer_name.in'] = 'It is outside of the customer name.';
            $messages['assignee.in'] = 'It is outside of the person in charge.';
            $messages['status.in'] = 'It is outside of the status.';
        } else {
            $messages['customer_name.in'] = '顧客名が範囲外です。';
            $messages['assignee.in'] = '担当者は圏外です。';
            $messages['status.in'] = 'ステータスが範囲外です。';
        }

        return $messages;
    }
}
