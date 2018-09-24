@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $bar->name }}</h2>

    <p>@lang('pages.bar.leaveQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {{-- TODO confirm with toggle --}}

    <div class="ui divider"></div>

    {!! Form::open(['action' => ['BarController@doLeave', 'barId' => $bar->id], 'method' => 'POST', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('bar.show', ['barId' => $bar->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesContinue')</button>
        </div>
    {!! Form::close() !!}

@endsection
