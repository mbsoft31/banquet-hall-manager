<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Http\Requests\Staff\StoreStaffRequest;
use Mbsoft\BanquetHallManager\Http\Requests\Staff\UpdateStaffRequest;
use Mbsoft\BanquetHallManager\Models\Event;
use Mbsoft\BanquetHallManager\Models\Staff;

class StaffController extends BaseController
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Staff::class);
        $q = Staff::query();
        if ($s = request()->query('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%$s%")
                  ->orWhere('role', 'like', "%$s%");
            });
        }
        return response()->json($q->paginate((int) request('per_page', 15)));
    }

    public function show(Staff $staff)
    {
        $this->authorize('view', $staff);
        return response()->json($staff);
    }

    public function store(StoreStaffRequest $request)
    {
        $this->authorize('create', Staff::class);
        $data = $request->validated();
        unset($data['tenant_id']);
        $staff = Staff::create($data);
        return response()->json($staff, 201);
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $this->authorize('update', $staff);
        $staff->update($request->validated());
        return response()->json($staff);
    }

    public function destroy(Staff $staff)
    {
        $this->authorize('delete', $staff);
        $staff->delete();
        return response()->noContent();
    }

    public function attachToEvent(int $event, int $staff)
    {
        $eventModel = Event::findOrFail($event);
        $this->authorize('update', $eventModel); // reuse event update gate
        $staffModel = Staff::findOrFail($staff);
        $tenantId = auth()->user()->tenant_id ?? request()->header('X-Tenant-ID');
        $eventModel->staff()->syncWithoutDetaching([$staffModel->id => ['tenant_id' => $tenantId]]);
        return response()->json(['attached' => true]);
    }

    public function detachFromEvent(int $event, int $staff)
    {
        $eventModel = Event::findOrFail($event);
        $this->authorize('update', $eventModel);
        $eventModel->staff()->detach([$staff]);
        return response()->noContent();
    }
}
