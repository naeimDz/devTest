<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Inertia\Inertia;

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
    public function indexAdmin()
    {
        $services = Service::all();
        return inertia('Services/AdminIndex', ['services' => $services]); 
    }

    public function show($id)
    {
        return inertia('Services/Show', [
            'service' => Service::findOrFail($id)
        ]);
    }

    public function create()
    {
        return inertia('Services/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);

        Service::create($validated);

        return redirect()->route('services.index');
    }

    public function edit($id)
    {
        return inertia('Services/Edit', [
            'service' => Service::findOrFail($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric'
        ]);

        $service->update($validated);

        return redirect()->route('services.index');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route('services.index');
    }
}
