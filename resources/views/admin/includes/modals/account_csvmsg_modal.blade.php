@if (session()->has('algMsg')) 
<div class="c-modal-overlay js-modal-overlay" style="display: block;"></div>
<div class="c-modal c-modal--dialog js-modal is-visible" id="js-modal-confirm">
    <div class="c-modal-container">
        <h4 class="u-color --warning u-fw--bold u-mt50 u-mb45" style="text-align:center;">{{ session('algMsg'); }}</h4>
        <ul class="c-modal-btns">
            <li class="c-modal-btns__item --dialog"><a class="c-btn js-modal-btn--close">
                {{ session()->forget('algMsg'); }}
                OK
            </a></li>
        </ul>
    </div>
</div>
@endif

@if (session()->has('sucMsg')) 
<div class="c-modal-overlay js-modal-overlay" style="display: block;"></div>
<div class="c-modal c-modal--dialog js-modal is-visible" id="js-modal-confirm">
    <div class="c-modal-container">
        <h4 class="c-modal-header__title --dialog u-mt50 u-mb45 u-fw--normal">{{ session('sucMsg'); }}</h4>
        <ul class="c-modal-btns">
            <li class="c-modal-btns__item --dialog"><a class="c-btn js-modal-btn--close">
                {{ session()->forget('sucMsg'); }}
                OK
            </a></li>
        </ul>
    </div>
</div>
@endif