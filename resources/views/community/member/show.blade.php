@extends('layouts.app')

@php
    use \App\Http\Controllers\CommunityMemberController;
    use \App\Perms\CommunityRoles;
@endphp

@section('content')
    <h2 class="ui header">{{ $member->name }}</h2>

    <table class="ui compact celled definition table">
        <tbody>
            <tr>
                <td>@lang('misc.user')</td>
                <td>{{ $member->name }}</td>
            </tr>
            <tr>
                <td>@lang('misc.role')</td>
                <td>{{ CommunityRoles::roleName($member->pivot->role) }}</td>
            </tr>
            <tr>
                <td>@lang('pages.communityMembers.memberSince')</td>
                <td>{{ $member->pivot->created_at }}</td>
            </tr>
            @if($member->pivot->visited_at != null)
                <tr>
                    <td>@lang('pages.communityMembers.lastVisit')</td>
                    <td>{{ $member->pivot->visited_at }}</td>
                </tr>
            @endif
            @if($member->pivot->created_at != $member->pivot->updated_at)
                <tr>
                    <td>@lang('misc.lastChanged')</td>
                    <td>{{ $member->pivot->updated_at }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(perms(CommunityMemberController::permsManage()))
        <p>
            <a href="{{ route('community.member.edit', ['communityId' => $community->human_id, 'memberId' => $member->id]) }}"
                    class="ui button basic secondary">
                @lang('pages.communityMembers.editMember')
            </a>
            <a href="{{ route('community.member.delete', ['communityId' => $community->human_id, 'memberId' => $member->id]) }}"
                    class="ui button basic negative">
                @lang('pages.communityMembers.deleteMember')
            </a>
        </p>
    @endif

    <a href="{{ route('community.member.index', ['communityId' => $community->human_id]) }}"
            class="ui button basic">
        @lang('general.goBack')
    </a>
@endsection
