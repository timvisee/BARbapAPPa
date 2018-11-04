@extends('layouts.app')

@section('content')

    <h2 class="ui header">{{ $member->name }}</h2>
    <p>@lang('pages.barMembers.deleteQuestion')</p>

    <div class="ui warning message visible">
        <span class="halflings halflings-warning-sign"></span>
        @lang('misc.cannotBeUndone')
    </div>

    {{-- TODO: toggle to also remove user from community --}}
    {{-- TODO: toggle to also remove user from other bars in community --}}

    <div class="ui divider"></div>

    {!! Form::open(['action' => ['BarMemberController@doDelete', 'barId' => $bar->human_id, 'memberId' => $member->id], 'method' => 'DELETE', 'class' => 'ui form']) !!}
        <div class="ui buttons">
            <a href="{{ route('bar.member.show', ['barId' => $bar->human_id, 'memberId' => $member->id]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}

@endsection
