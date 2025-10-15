<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Hall\StoreHallRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Hall\UpdateHallRequest;
use Mbsoft\BanquetHallManager\Models\Hall;

class HallController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Hall::class);
        $query = Hall::query();
        if ($q = request()->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('location', 'like', "%$q%");
            });
        }
        $per = (int) (request()->query('per_page', 15));
        return response()->json($query->paginate($per));
    }

    public function show(Hall $hall)
    {
        $this->authorize('view', $hall);
        return response()->json($hall);
    }

    public function store(StoreHallRequest $request)
    {
        $this->authorize('create', Hall::class);
        $data = $request->validated();
        unset($data['tenant_id']);
        $hall = Hall::create($data);
        return response()->json($hall, 201);
    }

    public function update(UpdateHallRequest $request, Hall $hall)
    {
        $this->authorize('update', $hall);
        $hall->update($request->validated());
        return response()->json($hall);
    }

    public function destroy(Hall $hall)
    {
        $this->authorize('delete', $hall);
        $hall->delete();
        return response()->noContent();
    }
}

