@extends('layouts.admin')

@section('title', 'Фильмы — KinoTower Admin')
@section('header', 'Управление фильмами')

@section('content')
    <!-- Блок фильтрации -->
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="{{ route('admin.films.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 form-group mb-md-0">
                    <label for="country_id">Страна</label>
                    <select name="country_id" id="country_id" class="form-control">
                        <option value="">— Все страны —</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ (string) request('country_id') === (string) $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-md-0">
                    <label for="category_id">Жанр</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">— Все жанры —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Фильтровать
                    </button>
                    @if (request()->filled('country_id') || request()->filled('category_id'))
                        <a href="{{ route('admin.films.index') }}" class="btn btn-secondary ml-1">
                            Сбросить
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица фильмов -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список фильмов</h3>
            <div class="card-tools">
                <a href="{{ route('admin.films.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Добавить фильм
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Название</th>
                        <th>Страна</th>
                        <th>Год</th>
                        <th>Длит. (мин)</th>
                        <th>Возраст</th>
                        <th>Жанры</th>
                        <th style="width: 210px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($films as $film)
                        <tr>
                            <td>{{ $film->id }}</td>
                            <td>
                                <strong>{{ $film->name }}</strong>
                            </td>
                            <td>{{ $film->country->name ?? '—' }}</td>
                            <td>{{ $film->year_of_issue }}</td>
                            <td>{{ $film->duration }}</td>
                            <td><span class="badge badge-secondary">{{ $film->age }}+</span></td>
                            <td>
                                @forelse ($film->categories as $category)
                                    <span class="badge badge-info">{{ $category->name }}</span>
                                @empty
                                    <span class="text-muted small">Не указаны</span>
                                @endforelse
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.ratings.index', ['film_id' => $film->id]) }}" class="btn btn-outline-warning btn-sm" title="Оценки фильма">
                                    <i class="fas fa-star"></i>
                                </a>
                                <a href="{{ route('admin.reviews.index', ['film_id' => $film->id]) }}" class="btn btn-secondary btn-sm" title="Отзывы фильма">
                                    <i class="fas fa-comments"></i>
                                </a>
                                <a href="{{ route('admin.films.categories.index', $film) }}" class="btn btn-info btn-sm" title="Жанры фильма">
                                    <i class="fas fa-tags"></i>
                                </a>
                                <a href="{{ route('admin.films.edit', $film) }}" class="btn btn-warning btn-sm" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Удалить"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.films.destroy', $film) }}"
                                        data-text="Удалить фильм «{{ $film->name }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Фильмов не найдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($films->hasPages())
            <div class="card-footer clearfix">
                {{ $films->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
