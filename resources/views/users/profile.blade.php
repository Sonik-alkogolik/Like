@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Профиль пользователя</h1>
        <p>Имя: {{ $user->name }}</p>
        <p>Email: {{ $user->email }}</p>
    </div>
@endsection
