<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('gender');

        if ($request->get('status') === 'trashed') {
            $query->onlyTrashed();
        } elseif ($request->get('status') === 'all') {
            $query->withTrashed();
        }

        $users = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()
            ->with('success', 'Пользователь успешно удалён.');
    }

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()
            ->with('success', 'Пользователь успешно восстановлен.');
    }
}
