<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\ServiceType\StoreServiceTypeRequest;
use Mbsoft\BanquetHallManager\Http\Requests\ServiceType\UpdateServiceTypeRequest;
use Mbsoft\BanquetHallManager\Models\ServiceType;
use Mbsoft\BanquetHallManager\Http\Resources\ServiceTypeResource;

class ServiceTypeController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', ServiceType::class);
        $query = ServiceType::query();
        if ($q = request()->query('q')) {
            $query->where('name', 'like', "%$q%");
        }
        $per = (int) (request()->query('per_page', 15));
        return ServiceTypeResource::collection($query->paginate($per));
    }

    public function show(ServiceType $service)
    {
        $this->authorize('view', $service);
        return ServiceTypeResource::make($service);
    }

    public function store(StoreServiceTypeRequest $request)
    {
        $this->authorize('create', ServiceType::class);
        $data = $request->validated();
        unset($data['tenant_id']);
        $service = ServiceType::create($data);
        return ServiceTypeResource::make($service)->response()->setStatusCode(201);
    }

    public function update(UpdateServiceTypeRequest $request, ServiceType $service)
    {
        $this->authorize('update', $service);
        $service->update($request->validated());
        return ServiceTypeResource::make($service);
    }

    public function destroy(ServiceType $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return response()->noContent();
    }
}
