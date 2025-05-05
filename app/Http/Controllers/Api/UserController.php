<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;
use App\Events\UserRoleUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;


class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Extract filters from request
            $filters = $request->only(['search', 'role', 'status']);
            $perPage = $request->input('per_page', 8);
            
            // Build the query
            $query = User::with('role');
            
            // Apply filters if provided
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            if (!empty($filters['role'])) {
                $query->where('role_id', $filters['role']);
            }
            
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            
            // Order and paginate
            $users = $query->orderBy('created_at', 'desc')
                          ->paginate($perPage);
            
            // Get roles from cache or database
            $roles = Cache::remember('all_roles', 3600, function () {
                return Role::all();
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Users fetched successfully.',
                'data' => [
                    'users' => UserResource::collection($users),
                    'roles' => $roles,
                    'filters' => $filters,
                    'pagination' => [
                        'total' => $users->total(),
                        'per_page' => $users->perPage(),
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function toggleStatus(User $user): JsonResponse
    {
        try {
            $oldStatus = $user->status;
            $user->status = $oldStatus === 'active' ? 'inactive' : 'active';
            $user->save();
            
            Log::info("User status changed: ID {$user->id} from {$oldStatus} to {$user->status}");
            
            return response()->json([
                'status' => 'success',
                'message' => "User status updated to {$user->status} successfully.",
                'data' => [
                    'user' => new UserResource($user),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling user status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user status.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        try {
            $validated = $request->validate([
                'role_id' => 'required|exists:roles,id',
            ]);
            
            $oldRoleId = $user->role_id;
            $user->role_id = $validated['role_id'];
            $user->save();
            
            event(new UserRoleUpdated($user->id, $validated['role_id'], $oldRoleId));
            
            Log::info("User role updated: ID {$user->id} from role {$oldRoleId} to {$user->role_id}");
            
            // Clear user cache if you're caching user data
            Cache::forget("user_{$user->id}");
            
            return response()->json([
                'status' => 'success',
                'message' => 'User role updated successfully.',
                'data' => [
                    'user' => new UserResource($user->fresh('role')),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user role: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user role.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        try {
            $user->load('role');
            
            return response()->json([
                'status' => 'success',
                'message' => 'User details fetched successfully.',
                'data' => [
                    'user' => new UserResource($user),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
