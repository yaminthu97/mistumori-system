<div class="tab">
    <div id="comment-content" class="content active-content">
        <h2 class="u-heading-h1 u-mb20">{{ trans('messages.answer') }}</h2>
        <div id="comment-list">
            @foreach ($answers as $answer)
                <div class="comment-list clearfix" id="comment-each-list" data-answer-id="{{ $answer->id }}">
                    <a id="answer-{{ $answer->id }}"></a>
                    <div class="comment-list-col1">
                        @if (App::isLocale('en'))
                            <p>
                                {{ $answer->answer_creator }}
                                <span>answered at</span>
                                <span
                                id="create-edit-time">{{ displayDateTimeByTimeZone($answer->created_at)}}</span>
                            </p>
                        @else
                            <p>
                                {{ $answer->answer_creator }}
                                <span>が</span>
                                <span
                                id="create-edit-time">{{ displayDateTimeByTimeZone($answer->created_at)}}</span>
                                <span>に回答しました。</span>
                            </p>
                        @endif
                        <p class="txt-overview u-mr20" id="answer-content">{!! nl2br(str_replace(' ', '&nbsp;', $answer->answer_content)) !!}</p>
                    </div>
                    <div class="comment-list-col2">
                        <a class="answer-edit-link @if ($answer->created_user_id != Auth::user()->id) answer-link-disable @endif"
                            href="{{ route('admin.answer.getAnswerData', ['answer_id' => $answer->id]) }}"
                            data-toggle="c-modal" data-id="{{ $answer->id }}"
                            data-target="#js-answer-edit-modal">{{ trans('messages.edit') }}</a>
                        <a class="answer-delete-link @if ($answer->created_user_id != Auth::user()->id) answer-link-disable @endif"
                            data-toggle="c-modal" data-id="{{ $answer->id }}"
                            data-created_user_id="{{ $answer->created_user_id }}"
                            data-target="#js-answer-delete-modal">{{ trans('messages.delete') }}</a>
                    </div>
                </div>
            @endforeach
        </div>
        <form action="{{ route('admin.answer.save') }}" method="POST" id="answer-form-data">
            @csrf
            <table class="c-table comment-table">
                <tbody class="c-table-body --horizontal" id="commentBody">
                    <input type="hidden" name="inquiry_id" value="{{ $new_created_inquiry->id }}">
                    <input type="hidden" name="created_user_id" value="{{ Auth::user()->id }}">

                    <tr class="c-table__tr">
                        <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.answer_content') }}<sup
                                style="color:red;">*</sup></th>
                        <td class="c-table__td --form --rightTop comment-td">
                            <textarea class="c-textarea --h100 inquiry-content" name="answer_content" id="ac">{{ old('answer_content') }}</textarea>
                            <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="answer-content-error"></p>
                        </td>
                    </tr>

                    <tr class="c-table__tr">
                        <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.status') }}<sup
                                style="color:red;">*</sup>
                        </th>
                        <td class="c-table__td --form --rightTop comment-td">
                            <label class="c-select">
                                <select name="inquiry_status" class="c-select__item js-switch-select" id="is">
                                    <option selected value=""></option>
                                    @foreach (GeneralConst::INQUIRY_STATUS as $key => $value)
                                        <option @if (old('inquiry_status') == (string) $key) selected @endif
                                            value="{{ $key }}">
                                            {{ trans('generalConst.inquiry_status.' . $key) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="inquiry-status-error"></p>
                            </label>
                        </td>
                    </tr>

                    <tr class="c-table__tr">
                        <th class="c-table__th --w200 --leftTop comment-th">
                            {{ trans('messages.person_in_charge') }}<sup style="color:red;">*</sup>
                        </th>
                        <td class="c-table__td --form --rightTop comment-td">
                            <label class="c-select">
                                <select name="question_assignee"
                                    class="c-select__item js-switch-select answer-assignee-field" id="aa">
                                    <option selected value=""></option>
                                    @foreach ($users as $user)
                                        <option @if (old('question_assignee', $new_created_inquiry->question_assignee ?? '') == (string) $user->id) selected @endif
                                            value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="question-assignee-error"></p>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="btn-row">
                <a href="{{ route('admin.inquiry.index') }}" class="c-btn --frame --bl u-mr20 js-modal-btn--close"
                    id="js-cancel">{{ trans('messages.clear') }}</a>
                <button class="c-btn --bl" type="submit" id="js-comment-btn-submit">{{ trans('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>
@include('admin.includes.modals.answer_edit_modal')
@include('admin.includes.modals.answer_delete_modal')
<script>
    var currentLocale = "{{ App::currentLocale() }}";
</script>
