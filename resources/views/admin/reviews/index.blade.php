@extends('layouts.admin')

@section('title', 'Отзывы — KinoTower Admin')
@section('header', $selectedFilm ? 'Отзывы к фильму: ' . $selectedFilm->name : 'Управление отзывами')

@section('content')
    <!-- Блок фильтрации -->
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form action="{{ route('admin.reviews.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-5 form-group mb-md-0">
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

                <div class="col-md-3 form-group mb-md-0">
                    <label for="status">Статус модерации</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">— Все статусы —</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>На модерации</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Одобренные</option>
                    </select>
                </div>

                <div class="col-md-4 form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Показать
                    </button>
                    @if (request()->filled('film_id') || request()->filled('status'))
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary ml-1">
                            Сбросить
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица отзывов -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список отзывов</h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Фильм</th>
                        <th>Пользователь</th>
                        <th>Текст отзыва</th>
                        <th style="width: 140px;">Дата</th>
                        <th style="width: 130px;">Статус</th>
                        <th style="width: 150px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->id }}</td>
                            <td>
                                <a href="{{ route('admin.reviews.index', ['film_id' => $review->film_id]) }}">
                                    <strong>{{ $review->film->name ?? 'Фильм удалён' }}</strong>
                                </a>
                            </td>
                            <td>{{ $review->user->fio ?? 'Пользователь удалён' }}</td>
                            <td style="max-width: 350px; white-space: normal;">{{ $review->message }}</td>
                            <td>{{ $review->created_at ? $review->created_at->format('d.m.Y H:i') : '—' }}</td>
                            <td>
                                @if ($review->is_approved)
                                    <span class="badge badge-success">Одобрен</span>
                                @else
                                    <span class="badge badge-warning">На модерации</span>
                                @endif
                            </td>
                            <td class="text-right text-nowrap">
                                @if (! $review->is_approved)
                                    <form action="{{ route('admin.reviews.approved', $review) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Одобрить отзыв">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Удалить отзыв"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.reviews.destroy', $review) }}"
                                        data-text="Удалить отзыв от «{{ $review->user->fio ?? 'пользователя' }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Отзывов не найдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reviews->hasPages())
            <div class="card-footer clearfix">
                {{ $reviews->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
