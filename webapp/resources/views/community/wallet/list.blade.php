@extends('layouts.app')

@section('title', __('pages.wallets.myWallets'))
@php
    $breadcrumbs = Breadcrumbs::generate('community.wallet.list', $economy);
    $menusection = 'community';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    <p>@lang('pages.wallets.description')</p>

    <div class="ui vertical menu fluid">
        @forelse($wallets as $wallet)
            <a href="{{ route('community.wallet.show', [
                'communityId' => $community->human_id,
                'economyId' => $economy->id,
                'walletId' => $wallet->id,
            ]) }}" class="item">
                {{ $wallet->name }}
                {!! $wallet->formatBalance(BALANCE_FORMAT_LABEL) !!}
            </a>
        @empty
            <div class="item">
                <i>@lang('pages.wallets.noWallets')</i>
            </div>
        @endforelse
    </div>
    {{ !is_array($wallets) ? $wallets->links() : '' }}

    {{-- TODO: check whether the user can create a wallet in any currency --}}
    <a href="{{ route('community.wallet.create', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic positive">
        @lang('misc.create')
    </a>

    <a href="{{ route('community.wallet.merge', ['communityId' => $community->human_id, 'economyId' => $economy->id]) }}"
            class="ui button basic">
        @lang('misc.merge')
    </a>

    <a href="{{ route('community.show', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('pages.community.goTo')
    </a>
@endsection
