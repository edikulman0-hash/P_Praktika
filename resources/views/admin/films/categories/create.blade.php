@extends('layouts.admin')

@section('title', 'Добавить жанр к фильму — KinoTower Admin')
@section('header', 'Добавить жанр к фильму: ' . $film->name)

@section('content')
    <div class="card card-primary" style="max-width: 600px;">
        <div class="card-header">
            <h3 class="card-title">Выбор жанра</h3>
        </div>

        <form action="{{ route('admin.films.categories.store', $film) }}" method="POST">
            @csrf
            <div class="card-body">
                @if ($availableCategories->isEmpty())
                    <p class="text-muted mb-0">Все существующие жанры уже добавлены к этому фильму.</p>
                @else
                    <div class="form-group mb-0">
                        <label for="category_id">Жанр <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                            <option value="">— Выберите жанр —</option>
                            @foreach ($availableCategories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <div class="card-footer">
                @if ($availableCategories->isNotEmpty())
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Добавить жанр
                    </button>
                @endif
                <a href="{{ route('admin.films.categories.index', $film) }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
