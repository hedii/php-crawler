<?php

if (! function_exists('routes_to_array')) {
    /**
     * Generate an array of routes for a given namespace.
     *
     * @param string $namespace
     * @return array
     * @throws \Exception
     */
    function routes_to_array(string $namespace): array
    {
        if (! in_array($namespace, ['api', 'admin'])) {
            throw new Exception("routes_to_array expect a namespace argument that is either `api` or `admin`. `{$namespace}` provided");
        }

        $routes = [];

        foreach (Route::getRoutes() as $route) {
            if (starts_with($route->getName(), $namespace)) {
                $routes[$route->getName()] = config('app.url') . '/' . $route->uri();
            }
        }

        return $routes;
    }
}
