@extends('layouts.app')
@section('content')
<div class = 'container'>
    <nav class="navbar bg-body-tertiary" style="background-color: #636b6f">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{route('main.index')}}">Главная страница</a>
            <form class="d-flex" role="result">
                <button class="btn btn-outline-success" type="submit">Результаты тестов</button>
            </form>
        </div>
    </nav>
    </div>
@endsection
