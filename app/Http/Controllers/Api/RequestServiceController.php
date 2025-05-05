<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\RequestServiceResource;
use App\Models\RequestService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RequestServiceController extends Controller
{
    /**
     * Display a paginated list of request services.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $query = RequestService::query();
        
        // No role checking, fetch data based on user ID directly
        if ($user->role->name === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role->name === 'service_provider') {
            $query->whereHas('service', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
    
        $requestServices = $query->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 6));
    
        return response()->json([
            'status' => 'success',
            'message' => 'Request services fetched successfully.',
            'data' => RequestServiceResource::collection($requestServices),
            'pagination' => [
                'total' => $requestServices->total(),
                'per_page' => $requestServices->perPage(),
                'current_page' => $requestServices->currentPage(),
                'last_page' => $requestServices->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created request service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $serviceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $serviceId): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);
    
        $email = $validated['email'];
        $user = Auth::user() ?? User::where('email', $email)->first();
    
        $requestService = RequestService::create([
            'email' => $email,
            'service_id' => $serviceId,
            'user_id' => $user?->id,
            'status' => $user ? 'confirmed' : 'pending',
        ]);
    
        // event(new ServiceRequested($requestService));
    
        return response()->json([
            'status' => 'success',
            'message' => 'Service request created successfully.',
            'data' => new RequestServiceResource($requestService),
        ], 201);
    }

    /**
     * Update the specified request service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RequestService  $requestService
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, RequestService $requestService): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,processing,completed,cancelled',
        ]);
        $requestService->update($validated);
        return response()->json([
            'status' => 'success',
            'message' => 'Request status updated successfully.',
            'data' => new RequestServiceResource($requestService),
        ]);
    }
}