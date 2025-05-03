<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);  
        }

        if ($request->has('full_name')) {
            $query->where('full_name', 'like', '%' . $request->full_name . '%');
        }

        if ($request->has('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->has('phone')) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        $perPage = $request->input('per_page', 10);

        return $query->paginate($perPage);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'full_name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients,email',
        'phone' => 'required|string|max:20',
    ]);

    return Client::create($validated);
}

public function update(Request $request, Client $client)
{
    $validated = $request->validate([
        'full_name' => 'sometimes|required|string|max:255',
        'email' => 'sometimes|required|email|unique:clients,email,' . $client->id,
        'phone' => 'sometimes|required|string|max:20',
    ]);

    $client->update($validated);
    return $client;
}

    public function destroy(Client $client)
    {
        $client->delete();
        return response()->noContent();
    }
}
