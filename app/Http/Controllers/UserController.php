<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Role;
use App\Events\UserRoleUpdated;

class UserController extends Controller
{
    public function index()
    {        
        $users = User::with('role')
            ->orderBy('created_at', 'desc')
            ->paginate(8);
            
        $roles = Role::all();
        
        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => request()->all(['search', 'role', 'status']),
        ]);
    }
    
    public function toggleStatus(User $user)
    {
        
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        
        return back()->with('message', 'تم تحديث حالة المستخدم بنجاح');
    }

    public function updateRole(Request $request, User $user)
    {
    
    $validated = $request->validate([
        'role_id' => 'required|exists:roles,id',
    ]);
    
    $user->role_id = $validated['role_id'];
    $user->save();
    event(new UserRoleUpdated($user->id, $validated['role_id']));
    
    return back()->with('message', 'تم تحديث دور المستخدم بنجاح');
    }
}
