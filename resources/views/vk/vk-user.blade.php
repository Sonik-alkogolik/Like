@extends('layouts.app')


@section('content')
    <div class="container">
        @if($userData)
            <div>
                <h1>Ваша страница вк</h1>
                <p>Имя: {{ $userData['first_name'] }}</p>
                <p>Фамилия: {{ $userData['last_name'] }}</p>
                <p>Ваше id: {{ $userData['screen_name'] }}</p>
            </div>
            <form action="{{ route('vk.delete-link') }}" method="post">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Удалить страницу</button>
            </form>
        @else
            <div>
                <h2>Добавить ссылку на страницу VK</h2>
                <form action="{{ route('vk.save-link') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="vkLink" class="form-label">Ссылка на страницу VK</label>
                        <input type="text" class="form-control" id="vkLink" name="vk_link" placeholder="Введите ссылку">
                    </div>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        @endif
    </div>
@endsection

