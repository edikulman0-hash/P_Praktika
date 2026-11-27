@extends('layouts.admin')

@section('title', 'Пользователи — KinoTower Admin')
@section('header', 'Управление пользователями')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Список пользователей</h3>
            <div class="card-tools">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.users.index') }}"
                       class="btn {{ !request()->filled('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                        Активные
                    </a>
                    <a href="{{ route('admin.users.index', ['status' => 'trashed']) }}"
                       class="btn {{ request('status') === 'trashed' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Удалённые
                    </a>
                    <a href="{{ route('admin.users.index', ['status' => 'all']) }}"
                       class="btn {{ request('status') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Все
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Пол</th>
                        <th>Дата рождения</th>
                        <th>Статус</th>
                        <th style="width: 150px;" class="text-right">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="{{ $user->trashed() ? 'table-secondary text-muted' : '' }}">
                            <td>{{ $user->id }}</td>
                            <td><strong>{{ $user->fio }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->gender->name ?? '—' }}</td>
                            <td>{{ $user->birthday ? $user->birthday->format('d.m.Y') : '—' }}</td>
                            <td>
                                @if ($user->trashed())
                                    <span class="badge badge-danger">Удалён</span>
                                @else
                                    <span class="badge badge-success">Активен</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @if ($user->trashed())
                                    <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Восстановить">
                                            <i class="fas fa-trash-restore"></i> Восстановить
                                        </button>
                                    </form>
                                @else
                                    <button type="button"
                                            class="btn btn-danger btn-sm"
                                            title="Удалить"
                                            data-toggle="modal"
                                            data-target="#deleteConfirmModal"
                                            data-url="{{ route('admin.users.destroy', $user) }}"
                                            data-text="Удалить пользователя «{{ $user->fio }}»?">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Пользователей не найдено</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer clearfix">
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endsection
