@extends('layouts.app')

@section('title', __('account.resetPassword'))

@section('content')
    <h2 class="ui header">@yield('title')</h2>
    <p>@lang('pages.passwordReset.enterResetToken')</p>

    {!! Form::open(['action' => ['PasswordResetController@doReset'], 'method' => 'POST', 'class' =>'ui form']) !!}

        <div class="required field {{ ErrorRenderer::hasError('token') ? 'error' : '' }}">
            {{ Form::label('token', __('misc.token') . ':') }}
            @if(!empty($token))
                {{ Form::text('token', $token) }}
            @else
                {{ Form::text('token', '') }}
            @endif
            {{ ErrorRenderer::inline('token') }}
        </div>

        <div class="ui divider"></div>

        <p>@lang('pages.passwordReset.enterNewPassword')</p>

        <div class="two fields">
            <div class="required field {{ ErrorRenderer::hasError('password') ? 'error' : '' }}">
                {{ Form::label('password', __('account.password') . ':') }}
                {{ Form::password('password') }}
                {{ ErrorRenderer::inline('password') }}
            </div>

            <div class="required field {{ ErrorRenderer::hasError('password_confirmation') ? 'error' : '' }}">
                {{ Form::label('password_confirmation', __('account.confirmPassword') . ':') }}
                {{ Form::password('password_confirmation') }}
                {{ ErrorRenderer::inline('password_confirmation') }}
            </div>
        </div>

        <div class="ui divider"></div>

        <div class="inline field">
            <div class="ui toggle checkbox {{ Request::query('compromised') ? 'disabled' : '' }}">
                {{ Form::checkbox('invalidate_other_sessions', true, true, ['tabindex' => 0, 'class' => 'hidden']) }}
                @if(barauth()->isAuth())
                    {{ Form::label('invalidate_other_sessions', __('account.invalidateOtherSessions')) }}
                @else
                    {{ Form::label('invalidate_other_sessions', __('account.invalidateAllSessions')) }}
                @endif
            </div>
            {{ ErrorRenderer::inline('invalidate_other_sessions') }}
        </div>

        <br />

        <button class="ui button primary" type="submit">@lang('pages.changePassword')</button>

    {!! Form::close() !!}
@endsection
