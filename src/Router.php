<?php

namespace Himeji\Routing;

class Router
{
    private $routes;

    public function __construct() {
        $this->routes = [];
    }

    public function addGet(string $path, mixed $callback) : void {
        $this->add(['GET'], $path, $callback);
    }

    public function addPost(string $path, mixed $callback) : void {
        $this->add(['POST'], $path, $callback);
    }

    public function addPut(string $path, mixed $callback) : void {
        $this->add(['PUT'], $path, $callback);
    }

    public function addPatch(string $path, mixed $callback) : void {
        $this->add(['PATCH'], $path, $callback);
    }

    public function addDelete(string $path, mixed $callback) : void {
        $this->add(['DELETE'], $path, $callback);
    }

    public function add(array $methods, string $path, mixed $callback) : void {
        $this->routes[] = [
            'path' => $path,
            'methods' => $methods,
            'callback' => $callback,
        ];
    }

    public function findRouteForPath($path) : ?array {
        $matchingRoute = null;

        foreach ($this->routes as $route) {
            if (array_key_exists('path', $route) && $route['path'] == $path) {
                $matchingRoute = $route;
            }
        }

        return $matchingRoute;
    }

    // If you want to override the default behaviour for resolve, just call findRouteForPath method.
    // This will give you the route along with the array of information.

    // Array keys: path (string), methods (array), callback (mixed)

    public function resolve(string $path, DependencyContainer $dependencyContainer) : void {
        $matchingRoute = $this->findRouteForPath($path);

        if ($matchingRoute == null) {
            echo '404'; return;
        }

        $callback = $matchingRoute['callback'];

        if (is_callable($callback)) {
            echo $callback();
        }
        else {
            if (is_string($callback)) {
                $controllerObject = $dependencyContainer->fetch($callback);

                if ($controllerObject != null && method_exists($controllerObject, '__invoke')) {
                    echo call_user_func(array($controllerObject, '__invoke'));
                }
                else {
                    echo 'cant do it';
                }
            }
            else {
                $controller = $callback[0];
                $method = $callback[1];

                $controllerObject = $dependencyContainer->fetch($controller);
                echo $controllerObject->$method();
            }
        }
    }
}