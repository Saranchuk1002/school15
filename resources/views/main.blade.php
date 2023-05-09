@extends('layouts.main')
@section('content')
        <div class="container text-center">
            <div class="row align-items-center" style="margin-top: -100px;">
                <div class="g-col-6">
                    Выберите предмет для прохождения теста
                </div>
            </div>
        </div>
    <div class="container-sm" style="background-color: #636b6f; width: 50%; margin-top: 30px;">
        @foreach($objects as $object)
            <div>
                <a class="nav-link" href="#"  data-bs-toggle="modal" data-bs-target="#select">{{$object->title}}</a>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="select" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Выбор типа теста</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h2 class="fs-5">Выберите тип теста</h2>
                            <p><a href="#" data-bs-toggle="tooltip" title="Tooltip">Учебные тесты</a> или <a href="#" data-bs-toggle="tooltip" title="Tooltip">Проверочные тесты</a></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                        </div>
                    </div>
                </div>
            </div>

        @endforeach
    </div>
@endsection

