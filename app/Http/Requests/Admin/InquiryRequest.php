<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use App\Models\Project;
use App\Models\User;
use App\Constants\GeneralConst;

class InquiryRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $priority_list = implode(",", array_keys(GeneralConst::PRIORITY));
        $inquiry_status_list = implode(",", array_keys(GeneralConst::INQUIRY_STATUS));

        $now = Carbon::now()->format('Y/m/d');

        $project_list = implode(',', Project::pluck('id')->toArray());
        $user_list = implode(',' , User::pluck('id')->toArray());

        // お問い合わせの検証作成/編集
        if ($request->routeIs('admin.inquiry.save')) {

            if( $this->route('inquiry_id')) {
                $expected_answer_date_validation = 'required|date_format:Y/m/d';
            }else {
                $expected_answer_date_validation = 'required|date_format:Y/m/d|after_or_equal:' . $now;
            }

            if ($this->method() == 'POST') {
                return [
                    'project_name' => 'required|max:255|numeric|in:' . $project_list,
                    'comment_content' => 'required|max:1000',
                    'question_assignee' => 'required|numeric|in:' . $user_list,
                    'expected_answer_date' => $expected_answer_date_validation,
                    'priority' => 'required|numeric|in:' . $priority_list,
                ];
            }
            return [];
        }

        // 問い合わせ検索の検証
        if ($request->routeIs('admin.inquiry.index')) {
            return [
                'project_name' => 'nullable|string|max:255',
                'response_date' => 'nullable|date_format:Y/m/d',
                'inquiry_status' => 'nullable|numeric|in:'. $inquiry_status_list
            ];
        }

        return [];
    }

    public function messages()
    {
        if (App::isLocale('en')) {
            $messages['project_name.in'] = 'It is outside of the project name.';
            $messages['question_assignee.in'] = 'It is outside of the person in charge.';
            $messages['priority.in'] = 'It is outside of the priority.';
            $messages['inquiry_status.in'] = 'It is outside of the status.';
        } else {
            $messages['project_name.in'] = 'プロジェクト名が範囲外です。';
            $messages['question_assignee.in'] = '担当者範囲外です。';
            $messages['priority.in'] = '優先範囲外です。';
            $messages['inquiry_status.in'] = 'ステータス範囲外です。';
        }

        return $messages;
    }
}
