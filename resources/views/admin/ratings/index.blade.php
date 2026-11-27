@extends('layouts.admin')

@section('title', 'Оценки фильмов — KinoTower Admin')
@section('header', $selectedFilm ? 'Оценки фильма: ' . $selectedFilm->name : 'Управление оценками фильмов')

@section('content')
    <!-- Блок фильтрации -->
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="{{ route('admin.ratings.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-6 form-group mb-md-0">
                    <label for="film_id">Фильм</label>
                    <select name="film_id" id="film_id" class="form-control">
                        <option value="">— Все фильмы —</option>
                        @foreach ($films as $film)
                            <option value="{{ $film->id }}" {{ (string) request('film_id') === (string) $film->id ? 'selected' : '' }}>
                                {{ $film->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Показать
                    </button>
                    @if (request()->filled('film_id'))
                        <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary ml-1">
                            Сбросить
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица оценок -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список оценок</h3>
            @if ($selectedFilm && $averageRating !== null)
                <div class="card-tools">
                    <span class="badge badge-warning p-2" style="font-size: 0.9rem;">
                        <i class="fas fa-star"></i> Средний рейтинг: {{ number_format($averageRating, 2) }}
                    </span>
                </div>
            @endif
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Фильм</th>
                        <th>Пользователь</th>
                        <th style="width: 120px;">Оценка</th>
                        <th style="width: 160px;">Дата</th>
                        <th style="width: 120px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ratings as $rating)
                        <tr>
                            <td>{{ $rating->id }}</td>
                            <td>
                                <a href="{{ route('admin.ratings.index', ['film_id' => $rating->film_id]) }}">
                                    <strong>{{ $rating->film->name ?? 'Фильм удалён' }}</strong>
                                </a>
                            </td>
                            <td>{{ $rating->user->fio ?? 'Пользователь удалён' }}</td>
                            <td>
                                <span class="badge badge-warning">
                                    <i class="fas fa-star"></i> {{ $rating->ball }}
                                </span>
                            </td>
                            <td>{{ $rating->created_at ? $rating->created_at->format('d.m.Y H:i') : '—' }}</td>
                            <td class="text-right">
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Удалить оценку"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.ratings.destroy', $rating) }}"
                                        data-text="Удалить оценку ({{ $rating->ball }}) пользователя «{{ $rating->user->fio ?? 'неизвестного' }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Оценок не найдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ratings->hasPages())
            <div class="card-footer clearfix">
                {{ $ratings->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
