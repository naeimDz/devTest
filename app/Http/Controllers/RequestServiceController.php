<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\RequestService;
use App\Models\Service;
use Inertia\Inertia;
use App\Models\User;

class RequestServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = RequestService::query();

        if ($user->role->name !== 'admin') {
            $query->where('user_id', $user->id);
        }
        $requestServices = $query->orderBy('created_at', 'desc');

        return Inertia::render('RequestService/Index', [
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
    
        return redirect()->back()->with('success', 'تم إنشاء طلب الخدمة بنجاح');
    }
    
    
}
