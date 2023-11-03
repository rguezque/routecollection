# RouteCollection

**RouteCollection** es un router extensible que permite agregar rutas y grupos de rutas. No ejecuta acciones sobre los controladores, solamente devuelve parámetros de la ruta solicitada si es hallada, para implementar acciones posteriores.

```php
use rguezque\RouteCollection\Dispatcher;
use rguezque\RouteCollection\RouteCollection;

// Crea una nueva instanca de RouteCollection
$router = new RouteCollection;

// Agrega algunas rutas
$router->route('GET', '/', function() {
    return 'Home';
});

$router->route('GET', '/about', function() {
    return 'About';
}); 

$router->route('POST', '/submit', function() {
    return 'Form submitted';
});

$router->route('GET', '/posts/{id}', function(array $params) {
    return 'ID received: '.$params['id'];
});

// Agrega grupo y grupos anidados
$router->routeGroup('/foo', function(RouteCollection $route) {
    $route->route('GET', '/', function() {
        return 'Foot root';
    });

    $route->route('GET', '/bar', function() {
        return 'Bar page';
    });
});

$dispatcher = new Dispatcher($router);

// Despacha el router para la actual petición
$router_params = $dispatcher->match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

// Implementar acciones con el resultado...
```

## Agregar rutas

Agrega rutas con el método `RouteCollection::route`, el cual recibe cuatro parámetros; el método de petición HTTP, la definición de la ruta la cual soporta *wildcards* en forma de parámetros nombrados y el controlador de la ruta. 

Los *wildcards* que coincidan con la URI solicitada serán enviados al controlador, como argumento dentro de un array lineal. El controlador puede ser una función, un método de un objeto o un métoco estático.

Agrega grupos de rutas bajo un mismo prefijo de ruta con el método `RouteCollection::routeGroup`. Esté método recibe dos parámetros, el prefijo del grupo y una función que a su vez recibe un argumento de tipo `RouteCollection`.

Dentro de esta función se pueden definir no solo rutas sino grupos de rutas anidadas que a su vez heredarán el prefijo del grupo padre.

```php
$router->routeGroup('/foo', function(RouteCollection $route) {
    $route->route('GET', '/', function() {
        return 'Foo';
    });

    $route->route('GET', '/bar', function() {
        return 'Bar';
    });

    $route->routeGroup('/control', function(RouteCollection $subruta) {
        $subruta->route('GET', '/', function() {
            return 'Superadmin control';
        });

        $subruta->route('GET', '/admin', function() {
            return 'Admin control';
        });

        $subruta->route('GET', '/user', function() {
            return 'User control';
        });
    });
});

// Este grupo genera las rutas:
// /foo
// /foo/bar
// /foo/control
// /foo/control/admin
// /foo/control/user
```

### Prefijo global

Se puede definir un prefijo global para todas las rutas al crear la instancia principal de `RouteCollection`, esto es útil si el router esta anidado en un subdirectorio del servidor.

```php
use rguezque\RouteCollection\RouteCollection;

$router = new RouteCollection('/mi_router');
```

## Ejecutar el router

Para ejecutar el router se debe crear una instancia de `Dispatcher` el cual recibe como argumento un objeto `RouteCollection`.

El método `Dispatcher::match` permite correr el router y recibe los parámetros `$_SERVER['REQUEST_URI']` y `$_SERVER['REQUEST_METHOD']`. Buscará una ruta que corresponda o se empareje con la URI solicitada; si halla alguna coincidencia devolvera un *array* con los datos de la ruta.

- `status_code`: Con valor `1` que significa que fue hallada una coincidencia.
- `route_path`: La definicion de la URI de la ruta.
- `route_method`: El método HTTP de petición definido para la ruta.
- `route_controller`: El controlador de la ruta.
- `route_params`: Los parámetros de la ruta si es que se definieron *wildcards* en la ruta.

Si  `Dispatcher::match` no encuentra ninguna ruta devolverá lo siguiente:

- `status_code`: Con valor `0` que significa que la ruta solicitada no fue hallada.
- `request_uri`: La URI solicitada.
- `request_method`: El método de petición de la URI solicitada.

## Extensible

La versatilidad de `Dispatcher::match` es que permite decidir como implementar las acciones del controlador después de ejecutar el enrutamiento. Por ejemplo de la siguiente manera:

```php
require __DIR__.'/vendor/autoload.php';

use rguezque\RouteCollection\Dispatcher;
use rguezque\RouteCollection\RouteCollection;

// Create a new RouteCollection instance
$router = new RouteCollection;

// Add some routes
$router->route('GET', '/', function(): string {
    return 'Home';
});

$router->route('GET', '/posts/{id}', function(array $params): string {
    return 'ID received: '.$params['id'];
}); 

$dispatcher = new Dispatcher($router);

// Dispatch the router for the current request
$router_params = $dispatcher->match($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

switch($router_params['status_code']) {
    case Dispatcher::FOUND: 
        $result = call_user_func($router_params['route_controller'], $router_params['route_params']);
        
        if(is_null($result)) {
            ob_get_clean();
            http_response_code(406);
            printf('The route "%s" with %s method must return a result.', $router_params['route_path'], $router_params['route_method']);
        }

        http_response_code(200);
        echo $result;
        break;

    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        printf('The request URI "%s" with %s method do not match any route.', $router_params['request_uri'], $router_params['request_method']);
        break;
}
```
