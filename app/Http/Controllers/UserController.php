<?php

namespace App\Http\Controllers;

use app\Models\User;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('outlet')->get();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $outlets = \app\Models\Outlet::all();
        return view('admin.users.create', compact('outlets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:30|unique:un_tb_user',
            'password' => 'required|string|min:8',
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'role' => 'required|in:admin,kasir,owner',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        User::create($data);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('outlet');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $outlets = \app\Models\Outlet::all();
        return view('admin.users.edit', compact('user', 'outlets'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'username' => 'required|string|max:30|unique:un_tb_user,username,' . $user->id,
            'password' => 'nullable|string|min:8',
            'id_outlet' => 'required|exists:un_tb_outlet,id',
            'role' => 'required|in:admin,kasir,owner',
        ]);

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
