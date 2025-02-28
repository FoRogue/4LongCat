<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller {
    public function index() {
        return User::with('visitor')->get();
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:150',
            'login' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return $user;
    }

    public function show($id) {
        return User::with('visitor')->findOrFail($id);
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'login' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|required|string|min:6',
            'role' => ['sometimes', Rule::in(['admin', 'user'])],
        ]);

        if ($request->has('password')) {
            $request->merge(['password' => Hash::make($request->password)]);
        }

        $user->update($request->all());
        return $user;
    }

    public function destroy($id) {
        return User::destroy($id);
    }

    // UserController.php

    public function getCurrentUser(Request $request)
    {
        return response()->json($request->user());
    }

}
