<div class="tab" id="tab">
    <ul class="tab-link">
        <li><a href="#comment" class="a-tab active-link" id="comment">{{ trans('messages.comment') }}</a></li>
        <li><a href="#estimate" class="a-tab" id="estimate">{{ trans('messages.estimated_content') }}</a></li>
    </ul>

    <div id="comment-content" class="content active-content">
        <div class="sorting">
            @sortablelink('created_at', trans('messages.sorting') , [] , ['class' => 'sort-btn'])
        </div>
        @unless (request()->has('sort') && request()->input('sort') == 'created_at')
            @if (count($comments) > 3 )
                <a class="more">
                    {{ trans('messages.previous_comment') }}
                </a>
            @endif
        @endunless
        <div id="comment-list">
            @foreach ($comments as $comment)
            <div class="comment-list clearfix content-tile @if (Auth::user()->id != $comment->created_user_id) permission-disabled @else auth-user @endif" id="comment-{{$comment->id}}">
                <div class="avatar @if($comment->comment_creator_role == GeneralConst::SALES ) bg01 @else bg02 @endif">
                    <span>{{ mb_substr($comment->comment_creator_name, GeneralConst::SALES, GeneralConst::MTM); }}</span>
                </div>
                <div class="content-slide">
                    @if (App::isLocale('en'))
                        <p class="content-msg"><span class="fc-01">{{ $comment->comment_creator_name }}</span>commented<span class="fc-01">{{ displayDateTimeByTimeZone($comment->created_at) }}.</span></p>
                    @else
                        <p class="content-msg"><span class="fc-01">{{ $comment->comment_creator_name }}</span>が<span class="fc-01">{{ displayDateTimeByTimeZone($comment->created_at) }}</span>にコメントしました。</p>
                    @endif
                    <div class="content-box">
                        @if (Auth::user()->id == $comment->created_user_id)
                            <a class="permission-access">
                            </a>
                            <div class="permission--dialog" id="js-permission-modal">
                                <a class="edit-link comment-edit-link"
                                    data-toggle="c-modal" data-id="{{ $comment->id }}" data-comment_assignee="{{ $comment->comment_assignee }}"
                                    data-comment_assignee_name="{{ $comment->comment_assignee_name }}"
                                    data-comment_content="{{ $comment->comment_content }}"
                                    data-target="#js-comment-edit-modal">{{ trans('messages.edit') }}</a>
                                <a class="delete-link comment-delete-link"
                                    data-toggle="c-modal" data-id="{{ $comment->id }}" data-id="{{ $comment->id }}"
                                    data-target="#js-comment-delete-modal">{{ trans('messages.delete') }}</a>
                            </div>
                        @endif
                        <p class="mentioned-person"><span>@</span>{{ $comment->comment_assignee_name }}</p>
                        <p>{!! nl2br(str_replace(' ', '&nbsp;', $comment->comment_content)) !!}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    <div class="content-input-blk">
        <form action="{{ route('admin.comment.create') }}" method="POST" id="comment-form-data"
            enctype="multipart/form-data">
            @csrf
            <table class="c-table comment-table">
                <tbody class="c-table-body --horizontal" id="commentBody">
                    <input type="hidden" name="project_id" value="{{ $project_id }}">
                    <input type="hidden" name="created_user_id" value="{{ Auth::user()->id }}">
                    <tr class="c-table__tr">
                        <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.mentioned_person') }}<sup style="color:red;">*</sup>
                        </th>
                        <td class="c-table__td --form --rightTop comment-td">
                            <label class="c-select">
                                <select name="comment_assignee" class="c-select__item js-switch-select"
                                    id="comment-assignee-field">
                                    <option selected value=""></option>
                                    @foreach ($users as $user)
                                        <option value={{ $user->id }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="comment-assignee-error"></p>
                            </label>
                        </td>
                    </tr>
                    <tr class="c-table__tr">
                        <th class="c-table__th --w200 --leftTop comment-th">{{ trans('messages.comment_content') }}<sup style="color:red;">*</sup></th>
                        <td class="c-table__td --form --rightTop comment-td">
                            <textarea name="comment_content" id="comment-content-field" class="c-textarea --h100 pj-content comment-textarea"></textarea>
                            <p class="u-color --warning u-fs12 u-mt5 u-fw--bold" id="comment-content-error"></p>
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
        <div class="c-modal c-modal--dialog js-modal" id="js-comment-delete-modal">
            <form id="comment-list-form" action="{{ route('admin.comment.delete') }}" method="POST">
                @csrf
                <input type="hidden" id="comment-id" name="id">
                <div class="c-modal-container">
                    <div class="c-modal-header">
                        <h4 class="comment-modal-header">{{ trans('messages.comment_delete') }}</h4>
                        <button type="button" class="comment-modal-close js-modal-btn--close" data-dismiss="modal"
                            aria-label="close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ trans('messages.comment_delete_alert') }}</h4>
                    <div class="c-modal-body">
                        <ul class="c-modal-btns">
                            <li class="c-modal-btns__item --dialog"><a
                                    class="c-btn --frame --bl js-modal-btn--close cancel-btn-border">{{ trans('messages.cancel') }}</a></li>
                            <li class="c-modal-btns__item --dialog"><button class="c-btn --bl js-form-submit" type="submit"
                                    id="js-comment-delete-modal-btn">{{ trans('messages.delete') }}</button></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('admin.includes.modals.comment_edit_modal')

    <div id="estimate-content" class="content">
        @include('admin.estimate.index')
    </div>
</div>
<script>
    var currentLocale = "{{ App::currentLocale() }}";
</script>
