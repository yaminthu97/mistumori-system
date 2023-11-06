<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WikiRequest;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\WikiLib;
use App\Constants\GeneralConst;

class WikiController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * @var \App\Libs\AdminAccountLib
     */
    protected $admin_account_lib;

    /**
     * @var \App\Libs\WikiLib
     */
    protected $wiki_lib;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    private $admin_login_session_data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib        = new AdminSessionLib();
            $this->admin_account_lib        = new AdminAccountLib();
            $this->wiki_lib                 = new WikiLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();

            if (!$this->admin_login_session_data) {
                abort(400);
            }

            if (!in_array($this->admin_login_session_data['role_id'], [GeneralConst::SALES, GeneralConst::MTM])) {
                abort(200, 'E091200');
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_WIKI
            ]);

            return $next($request);
        });
    }

    /**
     * Wiki データを表示する
     *
     * @return View
     */
    public function index(): View
    {
        // 開始ログ
        $this->start();

        $wiki = $this->wiki_lib->getWikiDatabyId();
        $latest_modifier_arr = [];
        if (!empty($wiki)) {
            $latest_modifier_arr = $this->getModifierArray();
        }

        // 終了ログ
        $this->end();
        return view('admin.wiki.index', compact('wiki', 'latest_modifier_arr'));
    }

    /**
     * Wiki データを保存する
     *
     * @param WikiRequest $request
     * @param $id
     * @return View|JsonResponse|RedirectResponse
     */
    public function save(WikiRequest $request, $id = null): View|JsonResponse|RedirectResponse
    {
        // 開始ログ
        $this->start();

        $check_flg = $file_names = null;
        $wiki = $image_files = $latest_modifier_arr = array();
        if ($request->isMethod('post')) {
            $check_flg = $this->wiki_lib->saveWikiData($request, $id);
            if (!$check_flg) {
                // 終了ログ
                $this->end();
                return redirect()->back()->withInput();
            }

            // 終了ログ
            $this->end();
            return response()->json([
                'id' => $check_flg
            ]);
        }

        // Wikiの編集
        if (isset($id)) {
            $wiki = $this->wiki_lib->getWikiDataById($id);
            if (!$wiki) {
                // 終了ログ
                $this->end();
                return redirect()->route('admin.wiki.index');
            }
            $file_names = $wiki->file_path ? $this->getFileNames($wiki->file_path) : null;
            $latest_modifier_arr = $this->getModifierArray($id);
        }

        // 終了ログ
        $this->end();
        return view('admin.wiki.save', compact('wiki', 'file_names', 'latest_modifier_arr'));
    }

    /**
     * ファイル名の取得
     *
     * @param string $file_path
     * @return string
     */
    public function getFileNames(string $file_path): string
    {
        $image_files = Storage::disk('public')->allFiles($file_path);
        $file_names = "";

        if (count($image_files) > 1) {
            foreach ($image_files as $image_file) {
                $file_names_arr[] = basename($image_file);
            }
            $file_names = join(", ", $file_names_arr);
        } else {
            $file_names = basename($image_files[0]);
        }
        return $file_names;
    }

    /**
     * Wikiデータを削除する
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $this->wiki_lib->deleteWikiData($request->id);

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Wiki 詳細ページを表示する
     *
     * @param $id
     * @return View|RedirectResponse
     */
    public function detail($id): View|RedirectResponse
    {
        // 開始ログ
        $this->start();

        $latest_datetime = $latest_user_id = null;
        $wiki = $this->wiki_lib->getWikiDatabyId($id);
        if (empty($wiki)) {
            // 終了ログ
            $this->end();
            return redirect()->route('admin.wiki.index');
        }
        $all_wikis = $this->wiki_lib->getAllWikiData();
        $result_arr = [];

        foreach ($all_wikis as $all_wiki) {
            if (strpos($all_wiki->title, $wiki->title) !== false) {
                $result_arr[] = $all_wiki;
            }
        }
        $latest_modifier_arr = $this->getModifierArray($id);
        $titles = $this->orderWikiTitle();

        // ブレッドクラム配列
        $parent_title = explode('/', $wiki->title);
        for ($i = 1; $i <= count($parent_title); $i++) {
            $parent_ttl[] = implode('/', array_slice($parent_title, 0, $i));
        }

        foreach (array_keys($titles) as $title) {
            if (reset($parent_ttl) === $title) {
                $parent_ttl_arr = $titles[reset($parent_ttl)];
            }
        }

        foreach ($parent_ttl as $parent) {
            foreach ($parent_ttl_arr as $title) {
                if ($parent == $title['title']) {
                    $breadcrumb_arr[] = $title;
                }
            }
        }

        // 終了ログ
        $this->end();
        return view('admin.wiki.detail', compact('wiki', 'latest_modifier_arr', 'result_arr', 'breadcrumb_arr'));
    }

    /**
     * モディファイア配列を取得する
     *
     * @param $id
     * @return array
     */
    public function getModifierArray($id = 1): array
    {
        $modifier = $this->wiki_lib->getModifier($id);
        $datetime_arr = $latest_datetime = $latest_modifier_arr = [];
        $datetime_arr = json_decode($modifier->modifier, true);

        // 一意の配列
        foreach ($datetime_arr as $item) {
            foreach ($item as $key => $datetime) {
                if (!isset($latest_datetime[$key]) || $datetime > $latest_datetime[$key]) {
                    $latest_datetime[$key] = $datetime;
                }
            }
        }

        // 昇順で並べ替える
        uasort($latest_datetime, function ($a, $b) {
            $first_datetime = strtotime($a);
            $second_datetime = strtotime($b);

            return strcmp($first_datetime, $second_datetime);
        });

        // ユーザー名と日時を含む配列を作成する
        foreach ($latest_datetime as $key => $value) {
            $names = $this->wiki_lib->getUserNameById($key);
            $latest_modifier_arr[$names] = $value;
        }
        return $latest_modifier_arr;
    }

    /**
     * 注文 Wiki タイトル
     *
     * @return array
     */
    public function orderWikiTitle(): array
    {
        $all_wikis = $this->wiki_lib->getAllWikiData();
        $group_titles = [];

        foreach ($all_wikis as $all_wiki) {
            $parts = explode('/', $all_wiki->title);
            $key = $parts[0];

            if (!isset($group_titles[$key])) {
                $group_titles[$key] = [];
            }

            $group_titles[$key][] = [
                'title' => $all_wiki->title,
                'id' => $all_wiki->id
            ];
        }

        foreach ($group_titles as &$group) {
            usort($group, function($a, $b) {
                return strcmp($a['title'], $b['title']);
            });
        }
        unset($group);
        return $group_titles;
    }

    /**
     * wikiファイルをダウンロードする
     *
     * @param $id
     * @param string $filepath
     * @return BinaryFileResponse
     */
    public function wikiDownload($id, string $filepath): BinaryFileResponse
    {
        $filePath = storage_path('app/public/' . GeneralConst::WIKI_FILEPATH . $id . '/' . $filepath);
        $is_file_exist = Storage::disk('public')->exists(GeneralConst::WIKI_FILEPATH . $id . '/' . $filepath);
        if ($is_file_exist) return response()->download($filePath);
    }
}
