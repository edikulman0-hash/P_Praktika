@extends('layouts.admin')

@section('title', 'Жанры фильма «' . $film->name . '» — KinoTower Admin')
@section('header', 'Жанры фильма: ' . $film->name)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.films.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Назад к списку фильмов
        </a>
    </div>

    <div class="card" style="max-width: 750px;">
        <div class="card-header">
            <h3 class="card-title">Привязанные жанры</h3>
            <div class="card-tools">
                <a href="{{ route('admin.films.categories.create', $film) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Добавить жанр
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Жанр</th>
                        <th>Родительский жанр</th>
                        <th style="width: 120px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                @if ($category->parent)
                                    <span class="badge badge-info">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted">— Корневой —</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Отвязать жанр"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.films.categories.destroy', [$film, $category]) }}"
                                        data-text="Убрать жанр «{{ $category->name }}» у фильма «{{ $film->name }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">У этого фильма пока не указаны жанры</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
