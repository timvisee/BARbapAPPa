@extends('layouts.app')

@section('title', __('pages.paymentService.newService'))
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>

    {!! Form::open([
        'action' => [
            'PaymentServiceController@doCreate',
            $community->human_id,
            $economy->id,
        ],
        'method' => 'POST',
        'class' => 'ui form'
    ]) !!}
        {{ Form::hidden('serviceable', $serviceable) }}

        <div class="field disabled">
            {{ Form::label('type', __('pages.paymentService.serviceType') . ':') }}
            {{ Form::text('type', $serviceable::name(true)) }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('enabled') ? 'error' : '' }}">
            <div class="ui checkbox">
                {{ Form::checkbox('enabled', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('enabled', __('pages.paymentService.enabledDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('enabled') }}
        </div>

        {{-- TODO: smart field, show 'no currencies' if none --}}
        <div class="reqeuired field {{ ErrorRenderer::hasError('currency') ? 'error' : '' }}">
            {{ Form::label('currency', __('misc.currency')) }}

            <div class="ui fluid selection dropdown">
                {{ Form::hidden('currency', null) }}
                <i class="dropdown icon"></i>

                <div class="default text">@lang('misc.pleaseSpecify')</div>
                <div class="menu">
                    @foreach($currencies as $c)
                        <div class="item" data-value="{{ $c->id }}">{{ $c->displayName }}</div>
                    @endforeach
                </div>
            </div>

            {{ ErrorRenderer::inline('currency') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('deposit') ? 'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('deposit', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('deposit', __('pages.paymentService.supportDepositDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('deposit') }}
        </div>

        <div class="inline field {{ ErrorRenderer::hasError('withdraw') ? 'error' : '' }}">
            <div class="ui toggle checkbox">
                {{ Form::checkbox('withdraw', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                {{ Form::label('withdraw', __('pages.paymentService.supportWithdrawDescription')) }}
            </div>
            <br />
            {{ ErrorRenderer::inline('withdraw') }}
        </div>

        <div class="ui divider"></div>

        {{-- Embed servicable specific view --}}
        @include($serviceable::view('create'))

        <div class="ui divider"></div>

        <button class="ui button primary" type="submit" name="submit" value="">
            @lang('misc.add')
        </button>
        <a href="{{ route('community.economy.payservice.index', [
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
        ]) }}"
                class="ui button basic">
            @lang('general.cancel')
        </a>

    {!! Form::close() !!}
@endsection
