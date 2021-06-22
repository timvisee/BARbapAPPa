@php
    use \App\Http\Controllers\BarController;

    if(!isset($r))
        $r = Route::currentRouteName() ?? 'error';
@endphp

<div class="item header spaced">{{ $bar->name }}:</div>

<a href="{{ route('bar.show', ['barId' => $bar->human_id]) }}"
        class="item {{ $r == 'bar.show' || $r == 'bar.buy' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-beer"></i>
    @lang('misc.bar')
</a>
<a href="{{ route('bar.info', ['barId' => $bar->human_id]) }}"
        class="item {{ $r == 'bar.info' ? ' active' : '' }}">
    <i class="glyphicons glyphicons-info-sign"></i>
    @lang('misc.information')
</a>
<a href="{{ route('bar.product.index', ['barId' => $bar->human_id]) }}"
        class="item {{ str_starts_with($r, 'bar.product.') ? ' active' : '' }}">
    <i class="glyphicons glyphicons-shopping-bag"></i>
    @lang('pages.products.title')
</a>
@if(perms(BarController::permsUser()))
    <a href="{{ route('bar.stats', ['barId' => $bar->human_id]) }}"
            class="item {{ $r == 'bar.stats' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-stats"></i>
        @lang('pages.stats.title')
    </a>
@endif
@if($bar->isJoined(barauth()->getSessionUser()))
    <a href="{{ route('community.wallet.list', ['communityId' => $community->human_id, 'economyId' => $bar->economy_id]) }}"
            class="item {{ $r == 'community.wallet.list' ? ' active' : '' }}">
        <i class="glyphicons glyphicons-wallet"></i>
        @lang('pages.wallets.title')
    </a>
@endif
@if(perms(BarController::permsManage()))
    <a href="{{ route('bar.manage', ['barId' => $bar->human_id]) }}"
            class="item">
        <i class="glyphicons glyphicons-new-window"></i>
        @lang('misc.manage')
    </a>
@endif

{{-- Community menu --}}
@include('includes.menu.section.community', ['community' => $bar->community])
