@extends('layouts.app')

@section('content')
    <h1>Мои группы</h1>
    <ul>
        @forelse($userGroups as $group)
            <li>
                <strong>Group ID:</strong> {{ $group->group_id }} <br>
                <strong>Group Link:</strong> <a href="{{ $group->group_link }}" target="_blank">{{ $group->group_link }}</a>
            </li>
        @empty
            <p>У вас пока нет добавленных групп.</p>
        @endforelse
    </ul>
@endsection