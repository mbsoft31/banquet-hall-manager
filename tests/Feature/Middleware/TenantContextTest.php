<?php

use Mbsoft\BanquetHallManager\Support\Middleware\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;

beforeEach(function () {
    $this->middleware = new TenantContext();
    $this->user = $this->createAuthenticatedUser();
});

it('sets tenant context from authenticated user', function () {
    $request = Request::create('/api/bhm/events', 'GET');
    $request->setUserResolver(fn() => $this->user);
    
    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['tenant_id' => config('banquethallmanager.current_tenant_id')]);
    });
    
    expect($response->getStatusCode())->toBe(200);
});

it('handles requests without authentication gracefully', function () {
    $request = Request::create('/api/bhm/events', 'GET');
    
    $response = $this->middleware->handle($request, function ($req) {
        return response()->json(['message' => 'success']);
    });
    
    expect($response->getStatusCode())->toBe(200);
});

it('maintains tenant context throughout request lifecycle', function () {
    $tenant = $this->createAuthenticatedUser(['id' => 999]);
    $request = Request::create('/api/bhm/events', 'GET');
    $request->setUserResolver(fn() => $tenant);
    
    $capturedTenantId = null;
    
    $this->middleware->handle($request, function ($req) use (&$capturedTenantId) {
        $capturedTenantId = config('banquethallmanager.current_tenant_id');
        return response()->json(['success' => true]);
    });
    
    expect($capturedTenantId)->toBe(999);
});