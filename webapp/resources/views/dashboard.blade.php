@extends('layouts.app')

@section('title', __('pages.dashboard.title'))
@php
    $breadcrumbs = Breadcrumbs::generate('dashboard');
@endphp

@section('content')
    {{-- Explore notice if user does not have bars or communities --}}
    @if($communities->isEmpty() && $bars->isEmpty())
        <div class="ui info message visible">
            <div class="header">@lang('pages.dashboard.noBarsOrCommunities')</div>
            <p>@lang('pages.dashboard.nothingHereNoMemberUseExploreButtons')</p>
            <a href="{{ route('explore.community') }}" class="ui button basic">
                @lang('pages.explore.exploreCommunities')
            </a>
            <a href="{{ route('explore.bar') }}" class="ui button basic">
                @lang('pages.explore.exploreBars')
            </a>
        </div>
    @endif

    {{-- User bar list --}}
    @if($bars->isNotEmpty())
        @include('bar.include.list', [
            'header' => __('pages.bar.yourBars') . ' (' . count($bars) . ')',
        ])
    @endif

    {{-- User community list --}}
    @if($communities->isNotEmpty())
        @include('community.include.list', [
            'header' => __('pages.community.yourCommunities') . ' (' .  count($communities) . ')',
        ])
    @endif
@endsection
