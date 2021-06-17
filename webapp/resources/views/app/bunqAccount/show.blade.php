@extends('layouts.app')

@section('title', $account->name)

@php
    // Define menulinks
    $menulinks[] = [
        'name' => __('general.goBack'),
        'link' => route('app.bunqAccount.index'),
        'icon' => 'undo',
    ];
@endphp

@section('content')
    <h2 class="ui header">
        @yield('title')

        <div class="sub header">
            @lang('misc.in')
            <a href="{{ route('app.manage') }}">
                {{ config('app.name') }}
            </a>
        </div>
    </h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.name')</td>
                <td>{{ $account->name }}</td>
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.paymentsEnabled')</td>
                <td>{{ yesno($account->enable_payments) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.bunqAccounts.checksEnabled')</td>
                <td>{{ yesno($account->enable_checks) }}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.accountHolder')</td>
                <td>{{ $account->account_holder }}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.iban')</td>
                <td>{{ format_iban($account->iban) }}</td>
            </tr>
            <tr>
                <td>@lang('barpay::misc.bic')</td>
                <td>
                    @if(!empty($account->bic))
                        {{ format_bic($account->bic) }}
                    @else
                        <i>@lang('misc.unspecified')</i>
                    @endif
                </td>
            </tr>
            <tr>
                <td>@lang('misc.createdAt')</td>
                <td>@include('includes.humanTimeDiff', ['time' => $account->created_at])</td>
            </tr>
            @if($account->created_at != $account->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>@include('includes.humanTimeDiff', ['time' => $account->updated_at])</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p>
        <div class="ui buttons">
            <a href="{{ route('app.bunqAccount.edit', ['accountId' => $account->id]) }}"
                    class="ui button secondary">
                @lang('misc.edit')
            </a>
            {{-- TODO: implement this --}}
            <a href="{{ route('app.bunqAccount.delete', ['accountId' => $account->id]) }}"
                    class="ui button negative disabled">
                @lang('misc.delete')
            </a>
        </div>
    </p>

    <p>
        {!! Form::open(['action' => ['AppBunqAccountController@doHousekeep', $account->id], 'method' => 'POST', 'class' => 'ui form']) !!}
            <button class="ui button orange" type="submit">@lang('pages.bunqAccounts.runHousekeeping')</button>
        {!! Form::close() !!}
    </p>

    <p>
        <a href="{{ route('app.bunqAccount.index') }}"
                class="ui button basic">
            @lang('general.goBack')
        </a>
    </p>
@endsection
