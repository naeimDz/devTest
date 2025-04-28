<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RequestService;
use App\Models\Service;
use Inertia\Inertia;
use App\Models\User;
use App\Events\ServiceRequested;


class RequestServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = RequestService::query();

        if ($user->role->name === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($user->role->name === 'service_provider') {
            $query->whereHas('service', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        $requestServices = $query->orderBy('created_at', 'desc')
        ->with(['service', 'user'])
        ->get()
        ->map(function ($request) {
            return [
                'id' => $request->id,
                'email' => $request->email,
                'status' => $request->status,
                'created_at' => $request->created_at,
                'service' => $request->service ? [
                    'id' => $request->service->id,
                    'name' => $request->service->name,
                ] : null,
                'user' => $request->user ? [
                    'id' => $request->user->id,
                    'name' => $request->user->name,
                ] : null,
            ];
        });


        return Inertia::render('Requests/Index', [
            'requestServices' => $requestServices,
        ]);
    }

    public function storeForGuest(Request $request, $serviceId)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255', 
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user) {
            $userId = $user->id;
        } else {
            $userId = null;
        }


        $requestService = new RequestService();
        $requestService->email = $validated['email'];
        $requestService->service_id = $serviceId;
        $requestService->user_id = $userId;
        $requestService->save();
        return redirect()->back()->with('success', 'تم إنشاء طلب الخدمة بنجاح');

    }
    public function storeForAuthenticatedUser(Request $request,$serviceId)
    {    
        $user = auth()->user();
    
        $requestService = RequestService::create([
            'service_id' => $serviceId,
            'user_id' => $user->id,
            'status' => 'confirmed',
            'email' => $user->email,
        ]);
        event(new ServiceRequested($requestService->service_id, $user->id));
        return redirect()->back()->with('success', 'تم إنشاء طلب الخدمة بنجاح');
    }

    public function update(Request $request, RequestService $requestService)
    {
        
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,processing,completed,cancelled',
        ]);
        
        $requestService->update([
            'status' => $validated['status']
        ]);
        
        // Notification::send($requestService->email, new ServiceRequestStatusUpdated($requestService));
        even(new RequestServiceStatusUpdated($requestService->id, $validated['status']));
        return redirect()->back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    
}
