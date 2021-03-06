@extends('layouts.app')

@section('title', __('pages.balanceImportChange.approveAll'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.balanceImportChange.approveAllQuestion', [
        'event' => $event->name,
    ])</p>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doApproveAll',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.balanceimport.change.index', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesApprove')</button>
        </div>
    {!! Form::close() !!}
@endsection
