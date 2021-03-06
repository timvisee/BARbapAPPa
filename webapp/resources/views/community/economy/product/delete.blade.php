@extends('layouts.app')

@section('title', $product->name)
@php
    $menusection = 'community_manage';
@endphp

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.products.deleteQuestion')</p>

    {!! Form::open([
        'action' => [
            'ProductController@doDelete',
            'communityId' => $community->human_id,
            'economyId' => $economy->id,
            'productId' => $product->id,
        ],
        'method' => 'DELETE',
        'class' => 'ui form'
    ]) !!}
        <div class="ui top attached warning message visible">
            <span class="halflings halflings-warning-sign"></span>
            @lang('pages.products.permanentDescription')
            @lang('misc.cannotBeUndone')
        </div>
        <div class="ui bottom attached segment">
            <div class="field {{ ErrorRenderer::hasError('permanent') ? 'error' : '' }}">
                <div class="ui checkbox {{ $product->trashed() ? 'disabled' : ''}}">
                    {{ Form::checkbox('permanent', true, $product->trashed(), ['tabindex' => 0, 'class' => 'hidden']) }}
                    {{ Form::label('permanent', __('pages.products.permanentlyDelete')) }}
                </div>
                <br />
                {{ ErrorRenderer::inline('permanent') }}
            </div>
        </div>

        <br />

        <div class="ui buttons">
            <a href="{{ route('community.economy.product.show', [
                        'communityId' => $community->human_id,
                        'economyId' => $economy->id,
                        'productId' => $product->id,
                    ]) }}"
                    class="ui button negative">
                @lang('general.noGoBack')
            </a>
            <div class="or" data-text="@lang('general.or')"></div>
            <button class="ui button positive basic" type="submit">@lang('general.yesRemove')</button>
        </div>
    {!! Form::close() !!}
@endsection
