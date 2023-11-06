<!-- モーダルコンテンツ（コメント編集）-->
<div class="c-modal js-modal fade text left" id="js-answer-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="c-modal--dialog modal-lg" role="document">
        <div class="c-modal-content">
            <div class="c-modal-header ">
                <h4 class="comment-modal-header">{{ trans('messages.answer_edit') }}</h4>
                <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                    aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="c-modal-body">
                <form action="{{ route('admin.answer.save') }}" method="POST" id="answer-edit-form-data">
                    @csrf
                    <input type="hidden" id="answer_id" name="answer_id">
                    <input type="hidden" id="created_user_id" name="created_user_id">
                    <input type="hidden" id="inquiry_id" name="inquiry_id">
                    <table class="c-table comment-table">
                        <tbody class="c-table-body --horizontal">
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.answer_content') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop comment-td">
                                    <textarea name="answer_content" id="answer_content_edit" class="c-textarea --h100">{{ old('answer_content') }}</textarea>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold errorMessage"
                                        id="answer_content_edit_error" data-error-name="answer_content"></p>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.status') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop comment-td">
                                    <label class="c-select">
                                        <select name="inquiry_status" id="inquiry_status_edit"
                                            class="c-select__item js-switch-select">
                                            <option value=''></option>
                                            @foreach (GeneralConst::INQUIRY_STATUS as $key => $value)
                                                <option @if (old('inquiry_status') == (string) $key) selected @endif
                                                    value="{{ $key }}">{{ trans('generalConst.inquiry_status.' . $key) }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold errorMessage"
                                        id="inquiry_status_edit_error" data-error-name="inquiry_status"></p>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.person_in_charge') }}<sup style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop comment-td">
                                    <label class="c-select">
                                        <select name="question_assignee" id="question_assignee_edit"
                                            class="c-select__item js-switch-select">
                                            <option value=''> </option>
                                            @foreach ($users as $user)
                                                <option @if (old('question_assignee') == (string) $user->id) selected @endif
                                                    value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold errorMessage"
                                        id="question_assignee_edit_error" data-error-name="question_assignee"></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btn-row">
                        <a class="c-btn --frame --bl u-mr20 js-modal-btn--close">{{ trans('messages.cancel') }}</a>
                        <a class="c-btn --bl" type="submit" id="js-answer-edit-submit">{{ trans('messages.save') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
