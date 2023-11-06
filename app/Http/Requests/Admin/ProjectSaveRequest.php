<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;
use App\Libs\AdminSessionLib;
use App\Models\User;
use App\Models\Customer;
use App\Constants\GeneralConst;

class ProjectSaveRequest extends FormRequest
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
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;
    private $admin_login_session_data;

    /**
     * リクエストに適用される検証ルールを取得
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $this->admin_session_lib = new AdminSessionLib();
        $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();

        $rules = [];
        $date = 'date_format:Y/m/d';
        $string = 'string';
        $separator = '|';
        $string_max = 'max:255';
        $max = 'max:1000';
        $required = 'required';
        $nullable = 'nullable';
        $unique = Rule::unique('projects')->ignore($this->route('id'));
        $file = 'file';
        $file_max = 'max:2000';
        $after = 'after:';
        $numeric = 'numeric';
        $after_today = 'after_or_equal:'. \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', \Carbon\Carbon::now())->setTimezone($this->admin_login_session_data['time_zone'])->format('Y/m/d');
        $after_development_start_date = 'after_or_equal:' . 'development_start_date';
        $before_development_end_date = 'before_or_equal:' . 'development_end_date';
        $customer_in = 'in:' . implode(',', Customer::pluck('id')->toArray());
        $project_type_in = 'in:' . implode(',', array_keys(GeneralConst::PROJECT_TYPE));
        $assignee_in = 'in:' . implode(',', User::pluck('id')->toArray());
        $priority_in = 'in:' . implode(',', array_keys(GeneralConst::PRIORITY));

        if ($this->method() == 'POST') {
            $rules['project_name'] = $required . $separator . $string . $separator . $string_max . $separator . $unique;
            $rules['customer_id'] = $required . $separator . $numeric . ($this->filled('customer_id') ? ($separator . $customer_in) : '');
            $rules['project_type'] = $required . $separator . $numeric . ($this->filled('project_type') ? ($separator . $project_type_in) : '');
            $rules['system_content'] = $required . $separator . $string . $separator . $max;
            $rules['system_path'] = $nullable . $separator . $file . $separator . $file_max;
            $rules['development_process'] = $required . $separator . $string . $separator . $string_max;
            $rules['development_language'] = $required . $separator . $string . $separator . $string_max;
            $rules['server_environment'] = $nullable . $separator . $string . $separator . $string_max;
            $rules['development_start_date'] = $nullable . $separator . $date . $separator . $after_today . $separator . 'required_with:development_end_date';
            $rules['development_end_date'] = $nullable . $separator . $date . $separator . $after_today . $separator . $after . 'development_start_date' . $separator . 'required_with:development_start_date';
            $rules['submit_date'] = $required . $separator . $date . $separator . $after_today . $separator . $after_development_start_date . $separator . $before_development_end_date;
            $rules['priority'] = $required . $separator . $numeric . ($this->filled('priority') ? ($separator . $priority_in) : '');
            $rules['assignee'] = $required . $separator . $numeric . ($this->filled('assignee') ? ($separator . $assignee_in) : '');
        }
        return $rules;
    }

    public function messages()
    {
        if (App::isLocale('en')) {
            $messages['customer_id.in'] = 'It is outside of the customer name.';
            $messages['project_type.in'] = 'It is outside of the project type.';
            $messages['priority.in'] = 'It is outside of the priority';
            $messages['assignee.in'] = 'It is outside of the person in charge.';
        } else {
            $messages['customer_id.in'] = '顧客名が範囲外です。';
            $messages['project_type.in'] = 'プロジェクトの種類が範囲外です。';
            $messages['priority.in'] = '優先順位が範囲外です。';
            $messages['assignee.in'] = '担当者は圏外です。';
        }

        return $messages;
    }
}
