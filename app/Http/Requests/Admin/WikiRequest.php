<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WikiRequest extends FormRequest
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
        $required = 'required';
        $nullable = 'nullable';
        $string = 'string';
        $separator = '|';
        $string_max = 'max:255';
        $file_validate = 'file';
        $file_max = 'max:10240';

        if ($this->method() == 'POST') {
            $rules['wiki_title'] = $required . $separator . $string . $separator . $string_max;
            $rules['wiki_content'] = $required;
            $rules['wiki_path'] = $nullable;
            $rules['wiki_path.*'] = $file_validate . $separator . $file_max;
        }
        return $rules;
    }

    public function messages()
    {
        $messages = [];
        $messages['wiki_path.*.uploaded'] = trans('messages.file_uploaded_message');
        $messages['wiki_path.*.max'] = trans('messages.file_max_message');
        return $messages;
    }
}
