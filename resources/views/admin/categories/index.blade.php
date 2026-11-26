@extends('layouts.admin')

@section('title', 'Жанры — KinoTower Admin')
@section('header', 'Управление жанрами (категориями)')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список жанров</h3>
            <div class="card-tools">
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Добавить жанр
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Название</th>
                        <th>Родительский жанр</th>
                        <th style="width: 180px;" class="text-right">Действия</th>
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
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Удалить"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.categories.destroy', $category) }}"
                                        data-text="Удалить жанр «{{ $category->name }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Жанров пока нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="card-footer clearfix">
                {{ $categories->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
