<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Events\ServiceCreated;
use App\Notifications\ServiceUpdateWarningNotification;
use App\Http\Resources\ServiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public function indexPublic(): JsonResponse
    {
        try {
            $services = Service::with('user')
                ->where('status', 'active')
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Active services fetched successfully.',
                'data' => ServiceResource::collection($services)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch services.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function indexAdmin(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $query = Service::query()->with('user');

            if ($user->role->name !== 'admin') {
                $query->where('user_id', $user->id);
            }
            // Apply search filter if provided
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by status if provided
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Get paginated results
            $perPage = $request->input('per_page', 9);
            $services = $query->orderBy('created_at', 'desc')
                              ->paginate($perPage)
                              ->withQueryString();

            return response()->json([
                'status' => 'success',
                'message' => 'Services fetched successfully.',
                'data' => [
                    'services' => ServiceResource::collection($services),
                    'filters' => $request->only(['search', 'status']),
                    'pagination' => [
                        'current_page' => $services->currentPage(),
                        'last_page' => $services->lastPage(),
                        'per_page' => $services->perPage(),
                        'total' => $services->total()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch services.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $service = Service::with('user')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Service details loaded.',
                'data' => new ServiceResource($service)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load service details.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:active,inactive',
            ]);

            $service = Service::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'status' => $validated['status'],
                'user_id' => $request->user()->id,
            ]);

            // Eager load the user relation for the resource
            $service->load('user');
            
            // Dispatch service created event
            event(new ServiceCreated($service));

            return response()->json([
                'status' => 'success',
                'message' => 'Service created successfully.',
                'data' => new ServiceResource($service)
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'required|in:active,inactive',
            ]);

            $service->update($validated);

            // Send notification if warning flag is set
            if ($request->boolean('warning')) {
                $service->user->notify(new ServiceUpdateWarningNotification($service));
            }

            // Reload to get fresh data with user relation
            $service->refresh();
            $service->load('user');

            return response()->json([
                'status' => 'success',
                'message' => 'Service updated successfully.',
                'data' => new ServiceResource($service)
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Service deleted successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete service.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}