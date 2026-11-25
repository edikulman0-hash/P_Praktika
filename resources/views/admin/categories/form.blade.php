@extends('layouts.admin')

@section('title', (isset($category) ? 'Редактирование жанра' : 'Добавление жанра') . ' — KinoTower Admin')
@section('header', isset($category) ? 'Редактирование жанра' : 'Добавление нового жанра')

@section('content')
    <div class="card card-primary" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">{{ isset($category) ? 'Изменить данные' : 'Новый жанр' }}</h3>
        </div>

        <form action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if (isset($category))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Название жанра</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $category->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Например: Фантастика"
                           maxlength="150"
                           required
                           autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parent_id">Родительский жанр (необязательно)</label>
                    <select name="parent_id" id="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">— Без родительского жанра —</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ isset($category) ? 'Сохранить изменения' : 'Добавить' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
