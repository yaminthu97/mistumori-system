<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerRequest;
use App\Libs\AdminSessionLib;
use App\Libs\CustomerLib;
use App\Constants\GeneralConst;

class CustomerController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * アドミンログインセッション情報
     */
    private $admin_login_session_data;

    /**
     * @var \App\Libs\CustomerLib;
     */
    protected $customer_lib;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib        = new AdminSessionLib();
            $this->customer_lib             = new CustomerLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();

            if (!$this->admin_login_session_data) {
                abort(400);
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_CUSTOMER_MANAGEMENT
            ]);

            return $next($request);
        });
    }

    /**
     * お客様情報一覧画面表示
     *
     * @param CustomerRequest $request
     * @return View
     */
    public function index(CustomerRequest $request): View
    {
        // 開始ログ
        $this->start();

        $search_info = $request->input();

        $customers = $this->customer_lib->getCustomers($search_info);

        // 終了ログ
        $this->end();
        return view('admin.customer.index', compact('customers', 'search_info'));
    }

    /**
     * お客様情報登録
     *
     * @param CustomerRequest $request
     * @param $customer_id
     * @return View|RedirectResponse
     */
    public function save(CustomerRequest $request, $customer_id = null): View|RedirectResponse
    {
        // 開始ログ
        $this->start();

        // お客様登録編集
        if ($request->isMethod('post')) {
            $this->customer_lib->saveCustomerData($request->input());

            //終了ログ
            $this->end();
            return redirect()->route('admin.customer.index');
        }

        $customer = null;
        if (isset($customer_id)) {
            $customer = $this->customer_lib->getCustomerById($customer_id);
            if (!$customer) {

                //終了ログ
                $this->end();
                return redirect()->route('admin.customer.index');
            }
        }

        // 終了ログ
        $this->end();
        return view('admin.customer.save', compact('customer'));
    }
}
