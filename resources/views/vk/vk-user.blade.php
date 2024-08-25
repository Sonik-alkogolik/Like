@extends('layouts.app')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<div class="container">
    <h2>Профиль VK</h2>

    <!-- Выводим ссылку на страницу пользователя или его ID -->
    @if($users_vk_serfing->isNotEmpty())
        @foreach($users_vk_serfing as $user)
            <p>Ваш VK ID: {{ $user->vk_user_id }}</p>
          
            <p>Ссылка на страницу VK: <a href="{{ $user->vk_link }}" target="_blank">{{ $user->vk_link }}</a></p>
        @endforeach
    @else
        <p>Нет данных о странице VK.</p>
    @endif

    <div id="group_num">Группа id="110152438"</div>
    <!-- Выводим группы пользователя -->
    <h3>Ваши группы VK</h3>
    @if($groups && count($groups) > 0)
    <ul>
        @foreach($groups as $group)
            <li>
                <a href="https://vk.com/{{ $group['screen_name'] }}" target="_blank">
                    <img src="{{ $group['photo_50'] }}" alt="{{ $group['name'] }}">
                    {{ $group['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p>Нет групп для отображения.</p>
@endif

<button id="check-group">Проверить группу</button>
<div id="group-check-result"></div>

<button id="get-groups">Получить группы</button>
    <div id="groups-list"></div>

    <input type="text" id="group-link" placeholder="Введите ссылку на группу">
    <button id="save-group">Сохранить группу</button>

<div id="save-group-result"></div>

    <!-- Кнопка для авторизации через VK -->
    <form id="auth-vk-form" action="{{ route('vk.auth') }}" method="GET" style="display: inline;">
        <button type="submit" class="btn btn-primary">Добавить страницу VK</button>
    </form>

    <!-- Кнопка для удаления страницы VK -->
    <form id="delete-form" action="{{ route('vk.delete') }}" method="POST" style="display: inline;">
        @csrf
        <button type="submit" class="btn btn-danger">Удалить страницу VK</button>
    </form>
</div>


@if(!empty($user->vk_user_id))
<script>
    var getGroupsUrl = '{{ url('/get-groups') }}';
    var checkgroup = '{{ url('/check-group') }}';
    var userId = @json($user->vk_user_id);
</script>
     @vite('resources/js/app.js')
@endif

@endsection
