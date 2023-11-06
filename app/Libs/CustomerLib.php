<?php

namespace App\Libs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Constants\GeneralConst;

/**
 * お客様情報処理クラス
 */
class CustomerLib
{
    /**
     * お客様情報の取得
     *
     * @param array $search_info
     * @return Collection
     */
    public function getCustomers(array $search_info = []): ?Collection
    {
        $query = Customer::query();

        if (isset($search_info['customer_name'])) {
            $query->where('customers.customer_name', 'like', '%' . $search_info['customer_name'] . '%');
        }

        if (isset($search_info['status'])) {
            $query->where('customers.status', $search_info['status']);
        }

        return $query->get();
    }

    /**
     * 編集のためにIDで顧客を取得
     *
     * @param $customer_id
     * @return Customer
     */
    public function getCustomerById($customer_id): ?Customer
    {
        return Customer::find($customer_id);
    }

    /**
     * 顧客をDBに保存
     *
     * @param array $request
     * @return bool
     */
    public function saveCustomerData(array $request): bool
    {
        try {
            DB::beginTransaction();

            $customer_data = array(
                'customer_name'     => $request['customer_name'],
                'description'       => $request['description'],
                'status'            => $request['status'] ?? GeneralConst::PRIVATE,
                'updated_user_id'   => Auth::user()->id
            );

            if (empty($request['customer_id'])) {
                $customer_data['created_user_id'] = Auth::user()->id;
            }

            $customer = $request['customer_id'] ? Customer::find($request['customer_id']) : new Customer();
            $customer->fill($customer_data)->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
