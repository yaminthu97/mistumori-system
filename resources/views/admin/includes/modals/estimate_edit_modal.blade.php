<!-- モーダルコンテンツ（見積編集）-->
<div class="c-modal fade text left" id="js-estimate-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="c-modal--dialog modal-lg" role="document">
        <div class="c-modal-content">
            <div class="c-modal-header ">
                <h4 class="estimate-modal-header">{{ trans('messages.estimate_edit') }}</h4>
                <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                    aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="c-modal-body">
                <form action="{{ route('admin.estimate.edit') }}" method="POST" id="estimate-edit-form-data"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="estimate-id-edit" name="id">
                    <table class="c-table estimate-table">
                        <tbody class="c-table-body --horizontal">
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.estimate_content') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <textarea name="estimation_content" id="estimate-content-edit" class="c-textarea pj-content"></textarea>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold" id="estimate-content-edit-error"
                                        data-error-name="estimation_content"></p>
                                </td>
                            </tr>

                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th"></th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <div class="file-wrapper">
                                        <label class="file-content --frame --file-box">
                                            <input class="c-input --file-input" type="text" name="estimation_file"
                                                id="estimation-file-edit" value="" placeholder="Choose file"
                                                readonly />
                                            <input type="file" class="js_input_edit" id="estimation-file-path_edit"
                                                name="estimation_path" value="" hidden />
                                            <label class="c-btn --gr --file-btn"
                                                for="estimation-file-path_edit">Browse</label>
                                        </label>
                                        <button class="c-btn --gr clear-btn" type="button">Clear</button>
                                    </div>
                                </td>
                            </tr>

                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.PG_man_hours') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <input class="c-input" type="text" name="PG_man_months" id="PG-man-months-edit"
                                        value="{{ old('PG_man_months') }}">
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold" id="PG-man-months-edit-error"
                                        data-error-name="PG_man_months"></p>
                                </td>
                            </tr>

                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.BSE_man_hours') }}<sup
                                        style="color:red;">*</sup></th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <input class="c-input" type="text" name="BSE_man_months" id="BSE-man-months-edit"
                                        value="{{ old('BSE_man_months') }}">
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold" id="BSE-man-months-edit-error"
                                        data-error-name="BSE_man_months"></p>
                                </td>
                            </tr>

                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.person_in_charge') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <label class="c-select">
                                        <select name="estimate_assignee" class="c-select__item js-switch-select"
                                            id="estimate-assignee-edit">
                                            <option value=''> </option>
                                            @foreach ($users as $user)
                                                <option @if (old('estimate_assignee') === (string) $user->id) selected @endif
                                                    value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>

                                        <p class="u-color --warning u-mt5 u-fs12 u-fw--bold" id="estimate-assignee-edit-error"
                                            data-error-name="estimate_assignee"></p>
                                    </label>
                                </td>
                            </tr>

                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop estimate-th">{{ trans('messages.status') }}<sup
                                        style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop estimate-td">
                                    <label class="c-select">
                                        <select name="status" class="c-select__item js-switch-select" id="status-edit">
                                            <option value=''> </option>
                                            @foreach (GeneralConst::PROJECT_STATUS as $key => $value)
                                                <option @if (old('status') === (string) $project->id) selected @endif
                                                    value="{{ $key }}">{{ trans('generalConst.project_status.' . $key) }}</option>
                                            @endforeach
                                        </select>
                                        <p class="u-color --warning u-mt5 u-fs12 u-fw--bold" id="status-edit-error"
                                            data-error-name="status"></p>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btn-row">
                        <a class="c-btn --frame --bl u-mr20 js-modal-btn--close">{{ trans('messages.cancel') }}</a>
                        <button class="c-btn --bl" type="submit" id="js-estimate-edit-submit"
                            data-url="{{ route('admin.estimate.edit') }}">{{ trans('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
