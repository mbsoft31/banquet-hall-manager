<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Client\StoreClientRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Client\UpdateClientRequest;
use Mbsoft\BanquetHallManager\Models\Client;

class ClientController extends BaseController
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Client::class);
        $query = Client::query();
        if ($search = request()->query('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        $perPage = (int) (request()->query('per_page', 15));
        return response()->json($query->paginate($perPage));
    }

    public function show(Client $client)
    {
        $this->authorize('view', $client);
        return response()->json($client);
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('create', Client::class);
        $data = $request->validated();

        unset($data['tenant_id']);

        $client = Client::create($data);
        return response()->json($client, 201);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $this->authorize('update', $client);
        $data = $request->validated();

        unset($data['tenant_id']);

        $client->update($data);
        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $this->authorize('delete', $client);
        $client->delete();
        return response()->noContent();
    }
}
