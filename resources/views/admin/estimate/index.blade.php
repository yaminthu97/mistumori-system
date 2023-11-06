<div class="sorting">
    @sortablelink('created_at', trans('messages.sorting'), [] , ['class' => 'sort-btn'])
</div>
@unless (request()->has('sort') && request()->input('sort') == 'created_at')
    @if (count($estimates) > 3 )
        <a class="more-btn">
            {{ trans('messages.previous_estimate') }}
        </a>
    @endif
@endunless
<div id="estimate-list">
    @foreach ($estimates as $estimate)
    <div id="estimate-{{$estimate->id}}" class="estimate-list content-tile @if (Auth::user()->id !=  $estimate->created_user_id) permission-disabled @else auth-user @endif">
        <div class="avatar @if($estimate->estimate_creator_role == GeneralConst::SALES) bg01 @else bg02 @endif">
            <span>{{ mb_substr($estimate->estimate_creator_name, GeneralConst::SALES, GeneralConst::MTM); }}</span>
        </div>
        <div class="content-slide">
            @if (App::isLocale('en'))
                <p class="content-msg"><span class="fc-01">{{ $estimate->estimate_creator_name }}</span>recorded the estimation<span class="fc-01">{{ displayDateTimeByTimeZone($estimate->created_at) }}.</span></p>
            @else
                <p class="content-msg">
                    <span class="fc-01">{{ $estimate->estimate_creator_name }}</span>が<span class="fc-01">{{ displayDateTimeByTimeZone($estimate->updated_at) }}</span>{{$estimate->created_at == $estimate->updated_at ? "に見積内容を記録しました。" : "に見積内容を更新しました。"}}
                </p>
            @endif
            <div class="content-box">
                @if (Auth::user()->id == $estimate->created_user_id)
                    <a class="permission-access-estimate">
                    </a>
                    <div class="permission--dialog" id="js-permission-modal">
                        <a href="{{ route('admin.estimate.getEstimationData', ['estimate_id' => $estimate->id]) }}"
                            class="edit-link estimate-edit-link"
                            data-toggle="c-modal" data-id="{{ $estimate->id }}" data-target="#js-estimate-edit-modal">{{ trans('messages.edit') }}</a>
                        <a class="delete-link estimate-delete-link"
                            data-id="{{ $estimate->id }}" data-created_user_id="{{ $estimate->created_user_id }}"
                            data-target="#js-estimate-delete-modal">{{ trans('messages.delete') }}</a>
                    </div>
                @endif
                <p>{!! nl2br(str_replace(' ', '&nbsp;', $estimate->estimation_content)) !!}</p>
                <p>{{ trans('messages.PG_month') }} - {{ $estimate->PG_man_months }}</p>
                <p class="mb-0">{{ trans('messages.BSE_month') }} - {{ $estimate->BSE_man_months }}</p>
                <a href="{{ route('admin.estimateFile.download', ['id' => $estimate->id, 'filepath' => basename($estimate->estimation_file_path)]) }}"
                    class="a-link js-system_overview-download">{{ $estimate->estimation_file_path !== null ? basename($estimate->estimation_file_path) : '' }}</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="content-input-blk">
<form action="{{ route('admin.estimate.create') }}" method="POST" id="estimate-form-data"
    enctype="multipart/form-data">
    @csrf
    <table class="c-table estimate-table">
        <tbody class="c-table-body --horizontal">
            <input type="hidden" name="project_id" value="{{ $project_id }}">
            <input type="hidden" name="created_user_id" value="{{ Auth::user()->id }}">

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.estimated_content') }}<sup style="color:red;">*</sup></th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <textarea name="estimation_content" id="estimate-content-field" class="c-textarea pj-content"></textarea>
                    <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="estimate-content-error"></p>
                </td>
            </tr>

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th"></th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <div class="file-wrapper">
                        <label class="file-content --frame --file-box">
                            <input class="c-input --file-input" type="text" name="estimation_file" id="estimation-file"
                                value="" placeholder="Choose file" readonly />
                            <input type="file" class="js_input" id="estimation-file-path" name="estimation_path"
                                value="" hidden />
                            <label class="c-btn --gr --file-btn" for="estimation-file-path">Browse</label>
                        </label>
                        <button class="c-btn --gr clear-btn" type="button">Clear</button>
                    </div>
                </td>
            </tr>

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.PG_man_hours') }}<sup style="color:red;">*</sup></th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <input class="c-input" type="text" name="PG_man_months" id="PG-man-months-field" value=""
                        autocomplete="off">
                    <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="PG-man-months-error"></p>
                </td>
            </tr>

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.BSE_man_hours') }}<sup style="color:red;">*</sup></th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <input class="c-input" type="text" name="BSE_man_months" id="BSE-man-months-field" value=""
                        autocomplete="off">
                    <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="BSE-man-months-error"></p>
                </td>
            </tr>

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.person_in_charge') }}<sup style="color:red;">*</sup>
                </th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <label class="c-select">
                        <select name="estimate_assignee" class="c-select__item js-switch-select"
                            id="estimate-assignee-field">
                            <option value=''> </option>
                            @foreach ($users as $user)
                                <option value={{ $user->id }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="estimate-assignee-error"></p>
                    </label>
                </td>
            </tr>

            <tr class="c-table__tr">
                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.status') }}<sup style="color:red;">*</sup>
                </th>
                <td class="c-table__td --form --rightTop estimate-td">
                    <label class="c-select">
                        <select name="status" class="c-select__item js-switch-select" id="status-field">
                            <option value=''> </option>
                            @foreach (GeneralConst::PROJECT_STATUS as $key => $value)
                                <option value="{{ $key }}">{{ trans('generalConst.project_status.' . $key) }}</option>
                            @endforeach
                        </select>
                        <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="status-error"></p>
                    </label>
                </td>
            </tr>

        </tbody>
    </table>

    <div class="btn-row">
        <a class="c-btn --frame --bl u-mr20 js-modal-btn--close" id="js-cancel">{{ trans('messages.cancel') }}</a>
        <button class="c-btn --bl" type="submit" id="js-comment-btn-submit">{{ trans('messages.save') }}</button>
    </div>
</form>
</div>
<div class="c-modal c-modal--dialog js-modal" id="js-estimate-delete-modal">
    <form id="estimate-delete-form-data" action="{{ route('admin.estimate.delete') }}" method="POST">
        @csrf
        <input type="hidden" id="estimate_id" name="id">
        <input type="hidden" id="created_userID" name="created_user_id">
        <div class="c-modal-container">
            <div class="c-modal-header">
                <h4 class="estimate-modal-header">{{ trans('messages.estimate_delete') }}</h4>
                <button type="button" class="estimate-modal-close js-modal-btn--close" data-dismiss="modal"
                    aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.estimate_delete_alert') }}</h4>
            <div class="c-modal-body">
                <ul class="c-modal-btns">
                    <li class="c-modal-btns__item --dialog"><a
                            class="c-btn --frame --bl js-modal-btn--close cancel-btn-border">{{ trans('messages.cancel') }}</a></li>
                    <li class="c-modal-btns__item --dialog"><button class="c-btn --bl" type="submit"
                            id="js-estimate-delete-modal-btn">{{ trans('messages.delete') }}</button></li>
                </ul>
            </div>
        </div>
    </form>
</div>

@include('admin.includes.modals.estimate_edit_modal')
<script>
    var currentLocale = "{{ App::currentLocale() }}";
</script>
