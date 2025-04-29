<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Events\ServiceCreated;
use App\Notifications\ServiceUpdateWarningNotification;

class ServiceController extends Controller
{
    public function indexPublic()
    {
        $services = Service::with('user')
            ->where('status', 'active')
            ->latest()
            ->get();

        return Inertia::render('Services/Explore', [
            'services' => $services
        ]);
    }

    public function indexAdmin(Request $request)
    {
        $user = Auth::user();
        $query = Service::query();
        
        if ($user->role->name !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        $services = $query->orderBy('created_at', 'desc')
                          ->paginate(9)
                          ->withQueryString();
        
        return Inertia::render('Services/Index', [
            'services' => $services,
            'filters' => $request->only(['search', 'status']),
            'success' => session('success'),
        ]);
    }


    public function show($id)
    {
        return inertia('Services/Show', [
            'service' => Service::findOrFail($id)
        ]);
    }

    public function create()
    {
        //
        return inertia('Services/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
            ]);

            $service = new Service();
            $service->name = $validated['name'];
            $service->description = $validated['description'];
            $service->status = $validated['status'];
            $service->user_id = Auth::id();
            $service->save();


            event(new ServiceCreated($service));
                        
            return redirect()->route('services.admin')
                ->with('success', 'تم إضافة الخدمة بنجاح');
    }

    public function edit($id)
    {
        return inertia('Services/Edit', [
            'service' => Service::findOrFail($id)
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);
    
        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
        ]);
        $service->fill($validated);
        $service->save();

        if ($request->boolean('warning')) {
            $service->user->notify(new ServiceUpdateWarningNotification($service));
       }
    
        return redirect()->route('services.admin')->with('success', 'تم تحديث الخدمة بنجاح');
    }
    

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('services.admin');
    }
}
