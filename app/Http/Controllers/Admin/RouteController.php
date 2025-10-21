<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => implode(', ', $route->middleware()),
                'prefix' => $route->getPrefix(),
                'domain' => $route->getDomain() ?: 'localhost',
            ];
        })->filter(function ($route) {
            // Filter out debug and internal routes
            return !str_starts_with($route['uri'], '_') &&
                   !str_starts_with($route['uri'], 'horizon') &&
                   !str_starts_with($route['uri'], 'sanctum');
        })->values();

        // Group routes by their prefix for better organization
        $groupedRoutes = $routes->groupBy('prefix')->sortKeys();

        return view('admin.routes.index', compact('groupedRoutes'));
    }
}
