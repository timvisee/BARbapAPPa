@extends('layouts.app')

@section('title', __('pages.balanceImportChange.undoChange'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.balanceImportChange.undoQuestion')</p>

    {!! Form::open([
        'action' => [
            'BalanceImportChangeController@doUndo',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'systemId' => $system->id,
            'eventId' => $event->id,
            'changeId' => $change->id,
        ],
        'method' => 'PUT',
        'class' => 'ui form'
    ]) !!}

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.balanceimport.change.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'systemId' => $system->id,
                        'eventId' => $event->id,
                        'changeId' => $change->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesUndo')</button>
        </div>
    {!! Form::close() !!}
@endsection
