<?php

namespace Mbsoft\BanquetHallManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use Mbsoft\BanquetHallManager\Models\Payment;

class AnalyticsController extends BaseController
{
    use AuthorizesRequests;

    public function revenue()
    {
        $this->authorize('viewAny', Payment::class);
        $from = request('from') ? Carbon::parse(request('from')) : Carbon::now()->subDays(30);
        $to = request('to') ? Carbon::parse(request('to')) : Carbon::now();
        $sum = (float) Payment::whereBetween('paid_at', [$from, $to])->sum('amount');
        return response()->json([
            'from' => $from->toDateTimeString(),
            'to' => $to->toDateTimeString(),
            'revenue' => $sum,
        ]);
    }
}

