@extends('admin.layouts.app')
@section('content')
    <div class="l-inner">
        <h1 class="u-heading-h1 u-mb30 u-mt20">{{ trans('messages.language_setting')}}</h1>

        <div class="MKLOCALE">
            <div class="form-control">
                <label class="lang-label">{{ trans('messages.language')}} :</label>
                <div class="c-select --w300">
                    <select name="language" class="c-select__item js-switch-select" autocomplete="off">
                        <option value="" selected>
                            {{ trans('messages.'. App::getLocale() .'.display')}}
                        </option>
                        @foreach (Config::get('languages') as $lang => $language)
                            @if ($lang != App::getLocale())
                                <option class="custom-dropdown-item" value="{{ route('admin.lang.switch', $lang) }}">
                                {{ trans('messages.'. $lang .'.display')}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <button class="c-btn js-switch-button">{{ trans('messages.save')}}</button>
            </div>
        </div>

        <script>
            // Get the select box element
            var select = document.querySelector(".js-switch-select");

            // Get the button element
            var button = document.querySelector(".js-switch-button");

            // Add an event listener for the click event
            button.addEventListener("click", function() {
                // Get the selected option value
                var value = select.value;

                // If the value is not empty, redirect to the language route
                if (value) {
                    window.location.href = value;
                }
            });
        </script>
    @endsection
