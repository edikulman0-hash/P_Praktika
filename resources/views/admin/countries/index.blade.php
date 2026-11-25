@extends('layouts.admin')

@section('title', 'Страны — KinoTower Admin')
@section('header', 'Управление странами')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список стран</h3>
            <div class="card-tools">
                <a href="{{ route('admin.countries.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Добавить страну
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Название</th>
                        <th style="width: 180px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($countries as $country)
                        <tr>
                            <td>{{ $country->id }}</td>
                            <td>{{ $country->name }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.countries.edit', $country) }}" class="btn btn-warning btn-sm" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        title="Удалить"
                                        data-toggle="modal"
                                        data-target="#deleteConfirmModal"
                                        data-url="{{ route('admin.countries.destroy', $country) }}"
                                        data-text="Удалить страну «{{ $country->name }}»?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Стран пока нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($countries->hasPages())
            <div class="card-footer clearfix">
                {{ $countries->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
