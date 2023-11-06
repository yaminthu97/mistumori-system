<!-- モーダルコンテンツ（コメント編集）-->
<div class="c-modal fade text left" id="js-comment-edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="c-modal--dialog modal-lg" role="document">
        <div class="c-modal-content">
            <div class="c-modal-header ">
                <h4 class="comment-modal-header">{{ trans('messages.comment_edit') }}</h4>
                <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                    aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="c-modal-body">
                <form action="{{ route('admin.comment.edit') }}" method="POST" id="comment-edit-form-data"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="comment-id-edit" name="id">
                    <table class="c-table comment-table">
                        <tbody class="c-table-body --horizontal">
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.mentioned_person') }}<sup
                                        style="color:red;">*</sup>
                                </th>
                                <td class="c-table__td --form --rightTop comment-td">
                                    <label class="c-select">
                                        <select name="comment_assignee" id="comment-assignee-edit"
                                            class="c-select__item js-switch-select">
                                            <option value=''> </option>
                                            @foreach ($users as $user)
                                                <option @if (old('comment_assignee') === (string) $user->id) selected @endif
                                                    value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold"
                                        id="comment-assignee-edit-error" data-error-name="comment_assignee"></p>
                                </td>
                            </tr>
                            <tr class="c-table__tr">
                                <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.comment_content') }}<sup
                                        style="color:red;">*</sup></th>
                                <td class="c-table__td --form --rightTop comment-td">
                                    <textarea name="comment_content" id="comment-content-edit" class="c-textarea --h100 pj-content comment-textarea">{{ old('comment_content') }}</textarea>
                                    <p class="u-color --warning u-mt5 u-fs12 u-fw--bold"
                                        id="comment-content-edit-error" data-error-name="comment_content"></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btn-row">
                        <a class="c-btn --frame --bl u-mr20 js-modal-btn--close">{{ trans('messages.cancel') }}</a>
                        <a class="c-btn --bl" type="submit" id="js-comment-edit-submit"
                            data-url="{{ route('admin.comment.edit') }}">{{ trans('messages.save') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
