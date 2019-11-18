@extends('layouts.app')

@section('title', __('pages.economies.title'))

@php
    use \App\Http\Controllers\EconomyController;

    // Get a list of economies
    $economies = $community->economies()->get();

    // Define menulinks
    $menulinks[] = [
        'name' => __('pages.community.backToCommunity'),
        'link' => route('community.manage', ['communityId' => $community->human_id]),
        'icon' => 'group',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title') ({{ count($economies) }})

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}">
                {{ $community->name }}
            </a>
        </div>
    </h2>
    <p>@lang('pages.economies.description')</p>

    <div class="ui vertical menu fluid">
        {{--
            <div class="item">
                <div class="ui transparent icon input">
                    {{ Form::text('search', '', ['placeholder' => 'Search communities...']) }}
                    <i class="icon link">
                        <span class="glyphicons glyphicons-search"></span>
                    </i>
                </div>
            </div>
        --}}

        @forelse($economies as $economy)
            <a href="{{ route('community.economy.show', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}" class="item">
                {{ $economy->name }}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.economies.noEconomies')</i>
            </div>
        @endforelse
    </div>

    @if(perms(EconomyController::permsManage()))
        <a href="{{ route('community.economy.create', ['communityId' => $community->human_id]) }}"
                class="ui button basic positive">
            @lang('misc.create')
        </a>
    @endif

    <a href="{{ route('community.manage', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.backToCommunity')
    </a>
@endsection
