@extends('layouts.admin')

@section('title', (isset($country) ? 'Редактирование страны' : 'Добавление страны') . ' — KinoTower Admin')
@section('header', isset($country) ? 'Редактирование страны' : 'Добавление новой страны')

@section('content')
    <div class="card card-primary" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">{{ isset($country) ? 'Изменить данные' : 'Новая страна' }}</h3>
        </div>

        <form action="{{ isset($country) ? route('admin.countries.update', $country) : route('admin.countries.store') }}" method="POST">
            @csrf
            @if (isset($country))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Название страны</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $country->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Например: Россия"
                           maxlength="50"
                           required
                           autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ isset($country) ? 'Сохранить изменения' : 'Добавить' }}
                </button>
                <a href="{{ route('admin.countries.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
