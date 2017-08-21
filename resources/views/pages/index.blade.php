@extends('layouts.app')

@section('content')
    <h1>{{ $title }}</h1>
    <p>Index page.</p>

    <hr />

    <table>
        <tr>
            <td>Locale:</td>
            <td>{{ (langManager()->hasSelectedLocale() ? 'yes' : 'no') }}</td>
        </tr>
        <tr>
            <td>Auth:</td>
            <td>{{ $auth }}</td>
        </tr>
        <tr>
            <td>Verified:</td>
            <td>{{ $verified }}</td>
        </tr>
    </table>
@endsection
