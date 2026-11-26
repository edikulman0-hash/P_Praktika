@extends('layouts.admin')

@section('title', (isset($film) ? 'Редактирование фильма' : 'Добавление фильма') . ' — KinoTower Admin')
@section('header', isset($film) ? 'Редактирование фильма' : 'Добавление нового фильма')

@section('content')
    <div class="card card-primary" style="max-width: 800px;">
        <div class="card-header">
            <h3 class="card-title">{{ isset($film) ? 'Изменить данные фильма' : 'Новый фильм' }}</h3>
        </div>

        <form action="{{ isset($film) ? route('admin.films.update', $film) : route('admin.films.store') }}" method="POST">
            @csrf
            @if (isset($film))
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label for="name">Название фильма <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $film->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Например: Интерстеллар"
                           maxlength="150"
                           required
                           autofocus>
                    @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="country_id">Страна <span class="text-danger">*</span></label>
                        <select name="country_id" id="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                            <option value="">— Выберите страну —</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}"
                                    {{ (string) old('country_id', $film->country_id ?? '') === (string) $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="year_of_issue">Год выпуска <span class="text-danger">*</span></label>
                        <input type="number"
                               name="year_of_issue"
                               id="year_of_issue"
                               value="{{ old('year_of_issue', $film->year_of_issue ?? date('Y')) }}"
                               class="form-control @error('year_of_issue') is-invalid @enderror"
                               min="1901"
                               max="{{ date('Y') + 5 }}"
                               required>
                        @error('year_of_issue')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="duration">Длит. (мин) <span class="text-danger">*</span></label>
                        <input type="number"
                               name="duration"
                               id="duration"
                               value="{{ old('duration', $film->duration ?? '') }}"
                               class="form-control @error('duration') is-invalid @enderror"
                               min="1"
                               max="65535"
                               required>
                        @error('duration')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 form-group">
                        <label for="age">Возраст (0+) <span class="text-danger">*</span></label>
                        <input type="number"
                               name="age"
                               id="age"
                               value="{{ old('age', $film->age ?? 0) }}"
                               class="form-control @error('age') is-invalid @enderror"
                               min="0"
                               max="255"
                               required>
                        @error('age')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="link_video">Ссылка на видео <span class="text-danger">*</span></label>
                    <input type="text"
                           name="link_video"
                           id="link_video"
                           value="{{ old('link_video', $film->link_video ?? '') }}"
                           class="form-control @error('link_video') is-invalid @enderror"
                           placeholder="https://..."
                           maxlength="255"
                           required>
                    @error('link_video')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="link_img">Ссылка на постер (необязательно)</label>
                    <input type="text"
                           name="link_img"
                           id="link_img"
                           value="{{ old('link_img', $film->link_img ?? '') }}"
                           class="form-control @error('link_img') is-invalid @enderror"
                           placeholder="https://..."
                           maxlength="255">
                    @error('link_img')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="link_kinopoisk">Ссылка на Кинопоиск (необязательно)</label>
                    <input type="text"
                           name="link_kinopoisk"
                           id="link_kinopoisk"
                           value="{{ old('link_kinopoisk', $film->link_kinopoisk ?? '') }}"
                           class="form-control @error('link_kinopoisk') is-invalid @enderror"
                           placeholder="https://kinopoisk.ru..."
                           maxlength="255">
                    @error('link_kinopoisk')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ isset($film) ? 'Сохранить изменения' : 'Добавить' }}
                </button>
                <a href="{{ route('admin.films.index') }}" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
@endsection
