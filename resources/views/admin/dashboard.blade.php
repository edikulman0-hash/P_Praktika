@extends('layouts.admin')

@section('title', 'Главная — KinoTower Admin')
@section('header', 'Главная страница админ-панели')

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>
                    <p>Фильмов</p>
                </div>
                <div class="icon">
                    <i class="fas fa-film"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Пользователей</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>Отзывов</p>
                </div>
                <div class="icon">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            Добро пожаловать в панель управления онлайн-кинотеатром <strong>KinoTower</strong>!
        </div>
    </div>
@endsection
