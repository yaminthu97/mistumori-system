<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use App\Constants\GeneralConst;

class CustomerRequest extends FormRequest
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
        $status = implode(",", array_keys(GeneralConst::CUSTOMER_STATUS));

        // 顧客検索の検証
        if ($request->routeIs('admin.customer.index')) {
            return [
                'customer_name' => "nullable|string|max:255",
                'status' => "nullable|int" . ($this->filled('status') ? "|in:" . $status : '')
            ];
        }

        // 顧客の検証の作成/編集
        if ($request->routeIs('admin.customer.save')) {
            if ($this->method() == 'POST') {
                return [
                    'customer_name' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'status' => "int" . ($this->filled('status') ? "|in:" . $status : '')
                ];
            }
        }
        return [];
    }
}
