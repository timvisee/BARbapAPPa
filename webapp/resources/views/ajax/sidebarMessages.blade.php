<div class="item header">
    @lang('pages.notifications.title')
</div>

@if(isset($notificationsUnread) && $notificationsUnread->isNotEmpty())
    <a href="{{ route('notification.index') }}" class="item header">
        {{ trans_choice('pages.notifications.unread#', $notificationsUnread->count()) }}
    </a>
    @foreach($notificationsUnread as $notification)
        @include('ajax.include.notification', $notification->viewData())
    @endforeach
    <div class="item">
        @if($notificationsUnread->count() > 1)
            {{-- Mark all as read button --}}
            {!! Form::open([
                'action' => ['NotificationController@doMarkAllRead'],
                'method' => 'POST',
                'class' => 'ui form'
            ]) !!}
                <button class="ui compact inverted tiny button green basic" type="submit">@lang('pages.notifications.markAllAsRead')</button>
            {!! Form::close() !!}
        @endif
    </div>
@endif

@if(isset($notifications) && $notifications->isNotEmpty())
    <a href="{{ route('notification.index') }}" class="item header">
        {{ trans_choice('pages.notifications.persistent#', $notifications->count()) }}
    </a>
    @foreach($notifications as $notification)
        @include('ajax.include.notification', $notification->viewData())
    @endforeach
    <div class="item"></div>
@endif

@if((!isset($notificationsUnread) || $notificationsUnread->isEmpty())
    && (!isset($notifications) || $notifications->isEmpty()))
    <div class="item"><i>@lang('pages.notifications.noNotifications')</i></div>
@endif

<a href="{{ route('notification.index') }}" class="item">
    <i class="glyphicons glyphicons-message-full"></i>
    @lang('pages.notifications.all')
</a>
