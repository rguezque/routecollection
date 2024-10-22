# RouteCollection

**RouteCollection** es un router extensible que permite agregar rutas y grupos de rutas. No ejecuta acciones sobre los controladores, solamente devuelve parámetros de la ruta solicitada si es hallada, para implementar acciones posteriores.

- [Agregar rutas](#agregar-rutas)
  - [Prefijo global](#prefijo-global)
- [Ejecutar el router](#ejecutar-el-router)
  - [Implementación predeterminada](#implementación-predeterminada)
- [Configurar CORS](#configurar-cors)
- [HTTP](#http)
  - [Petición (Request)](#petición-(request))
  - [Respuesta (Response)](#respuesta-(response))
  - [Emitir la respuesta](#emitir-la-respuesta)
- [Plantillas](#plantillas)
- [Base de datos](#base-de-datos)
- [Sesiones](#sesiones)
- [Globales](#globales)

## Ejemplo

```php
<?php declare(strict_type = 1);

require __DIR__.'/vendor/autoload.php';

use rguezque\RouteCollection\{
    Dispatcher;
	RouteCollection
};

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
$request = new ServerRequest;

// Despacha el router para la actual petición
$router_params = $dispatcher->match($request);

// Implementar acciones con el resultado...
```

## Agregar rutas

Agrega rutas con el método `RouteCollection::route`, el cual recibe tres parámetros; el método de petición HTTP, la definición de la ruta la cual soporta *wildcards* en forma de parámetros nombrados y el controlador de la ruta. 

Los *wildcards* que coincidan con la URI solicitada serán enviados al controlador como argumentos dentro de un array lineal. El controlador puede ser una función, un método de un objeto o un método estático.

Agrega grupos de rutas bajo un mismo prefijo de ruta con el método `RouteCollection::routeGroup`. Esté método recibe dos parámetros, el prefijo del grupo y una función que a su vez recibe un argumento de tipo `RouteCollection`.

Dentro de esta función se pueden definir no solo rutas sino grupos de rutas anidadas que a su vez heredarán el prefijo del grupo padre.

```php
$router->routeGroup('/foo', function(RouteCollection $route) {
    $route->route('GET', '/', function() {
        return 'Foo';
    });

    $route->route('GET', '/bar/{name}', function() {
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

Para ejecutar el router se debe crear una instancia de `Dispatcher` el cual recibe como argumento un objeto `RouteCollection` y opcionalmante un objeto `CorsConfig` (Ver [Configurar CORS](#configurar-cors)).

El método `Dispatcher::match` permite correr el router y recibe un objeto `ServerRequest`. Buscará una ruta que corresponda o se empareje con la URI solicitada; si halla alguna coincidencia devolvera un *array* con los datos de la ruta.

- `status_code`: Con valor `1` que significa que fue hallada una coincidencia.
- `route_path`: La definicion de la URI de la ruta.
- `route_method`: El método HTTP de petición definido para la ruta.
- `route_controller`: El controlador de la ruta.
- `route_params`: Un *array* asociativo con los parámetros de la ruta si es que se definieron *wildcards* en la ruta.

Si  `Dispatcher::match` no encuentra ninguna ruta devolverá lo siguiente:

- `status_code`: ConsendHeaders(): Envía los encabezados HTTP al cliente. valor `0` que significa que la ruta solicitada no fue hallada.
- `request_uri`: La URI solicitada.
- `request_method`: El método de petición de la URI solicitada.

De esta forma se puede decidir como implementar las acciones del controlador después de ejecutar el enrutamiento.

### Implementación predeterminada

El método `Dispatcher::dispatch` proporciona un motor de funcionamiento default para el router. Recibe un argumento de tipo `ServerRequest` y devuelve un objeto `HttpResponse`, por lo cual, todos los controladores deben retornar un objeto `HttpResponse` de lo contrario lanzara un `UnexpectedValueException`. 

A cada controlador se le inyecta un argumento de tipo `ServerRequest` el cual contiene toda la información sobre la petición actual así como los parámetros de ruta que hayan sido definidos. Si una ruta no existe el router lanzará un `Runtime Exception`.

Para detener el router invoca el método `Dispatcher::halt` el cual recibe un objeto `HttpResponse` y se encarga de enviar la respuesta e inmediatamente detiene el *script*.

## Configurar CORS

Configura CORS a través de `CorConfig`, el cual recibe un array con los origenes aceptados y sus opciones.

```php
$cors = new CorsConfig([
    'http://localhost:3000' => [
        'methods' => ['GET', 'PATCH'],
        'headers' => ['Authorization']
    ],
    'http://foo.net' => [
        'methods' => ['GET', 'POST'. 'DELETE'],
        'headers' => ['Authorization', 'Accept']
    ]
]);
```

Donde cada clave es la URL del origen; `methods` son los métodos de petición HTTP aceptados desde ese origen y `headers` son los encabezados aceptados desde ese origen.

Alternativamente se pueden agragar origenes con `CorsConfig::addOrigin`.

```php
$cors->addOrigin('http://localhost:3000', ['GET', 'PATCH'], ['Authorization', 'Accept']);
```

Si no se especifican los métodos y encabezados de un origen por default se asignaran los métodos `GET` y `POST` y los encabezados `Content-Type`, `Authorization` y `Accept`.

Para implementarlo se envía como segundo argumento en `Dispatcher` (Ver [Ejecutar el router](#ejecutar-el-router)).

```php
$api = new RouteCollection;
$api->route('GET', '/', function() {
    return new HttpJsonResponse(['hola' => 'mundo']);
});

$cors = new CorsConfig([
    'http://localhost:3000' => {
        'methods' => ['GET', 'POST'],
        'headers' => ['Accept']
    }
]);

$dispatcher = new Dispatcher($api, $cors);
```

## HTTP

### Petición (Request)

Un objeto ` ServerRequest` contiene información sobre la petición actual. Esta información se puede acceder a través de atributos públicos:

- `query`: Equivale a `$_GET`

- `body`: Equivale a `$_POST`

- `server`: Equivale a `$_SERVER`

- `cookie`: Equivale a `$_COOKIE`

- `files`: Equivale a `$_FILES`

- `params`: Equivle a loa parámetros de una ruta

- `input`: Contiene los datos del stream `php://input` en on ubjeto `PhpInputStream`

Cada atributo es una instancia de `Collection` excepto `input` que contiene una instancia de `PhpInputStream`. La clase  `Collection` tiene los siguientes métodos:

- `get(string $name, mixed $default = null)`: Devulve un parámetro por nombre o devuelve un valor default especificado si el parámetro no existe.
- `set(string $name, mixed $value)`:  Crea o asigna un valor a un parámetro.
- `all()`: Devuelve el array completo de parámetros.
- `has(string $name)`: Devuelve `true` si un parámetro existe y no es `null`.
- `remove(striong $name)`: Elimina un parámetro por nombre.
- `clear()`: Elimina todos los parámetros.

Para la clase `PhpInputStream` se tienen los métodos:

- `getParsedStr`: Parsea el stream cuando llega como un query del tipo `name=John&lastname=Doe` y lo devuelve en un objeto `Collection`.
- `getDecodedJson`: Decodifica el stream cuando llega en formato JSON y lo devuelve en un objeto `Collection`.
- `getRawData`: Devuelve el stream tal cual llega en la petición.

Los siguientes métodos también son parte de `ServerRequest`:

- `withParams(array $params)`: Permite asignar los parámetros de una ruta a la actual petición.
- `getRequestHeaders()`: Devuelve todos los encabezados HTTP de la actual petición.
- `buildQuery(string $uri, array $params)`: Genera una cadena de petición del tipo `https://fake.com?name=John&lastname=Doe`.

### Respuesta (Response)

El objeto `HttpResponse` contiene los métodos y atributos necesarios para generar una respuesta a partir de una petición de cliente.

- `clear()`: Limpia los valores actuales de `Httpresponse`.
- `setstatusCode(int $code)`: Asigna un código de estatus HTTP del response (Ver [HTTP Status Code](http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml)).
- `getStatusCode()`: Devuelve el código HTTP actual del response.
- `setContent(string $body)`: Asigna el contenido del response.
- `getContent()`: Devuelve el actual cuerpo del response.

Para definir el cuerpo del response se hace a través del atributo `body` que contiene una instancia de `Stream`con los métodos:

- `write(mixed $string)`: Escribe contenido en el cuerpo del stream del response, es el método .
- `read(int $length)`: Lee una longitud especificada de *bytes* del cuerpo del response.
- `getContents()`: Devuelve el contenido del response. Primero asegurate de aplicar `Stream::rewind` para mover el puntero al inicio del stream, o `Stream::seek` para mover el puntro a una posición especificada del *stream*.
- `rewind()`: Mueve el puntero de lectura al inicio del *stream*.
- `seek($offset, $whence = SEEK_SET)`: Mueve el puntero a un límite específico a partir de una posición inicial y le suma el *offset* para dar como resultado la posición final del puntero. La constante `SEEK_SET` establece la posición igual a los *bytes* de desplazamiento.
- `getSize()`: Devuelve la longitud de *bytes* del stream.
- `detach()`: Devuelve el *stream* y establece en `null` el atributo que lo contenía.
- `tell`: Devuelve la posición actual del puntero en el stream.
- `eof`: Devuelve `true` si el puntero apunta al final del stream, `false` en caso contrario.
- `close()`: Cierra la escritura del *stream*. Se ejecuta automáticamente con el evento `__destruct()` de la clase `Stream` contenida en el atributo `body`.

Internamente el atributo `body` implementa un stream `php://memory`:

```php
$stream = new Stream(fopen('php://memory'));
```

Para definr *headers* se hace através del atributo `headers` que contiene una instancia de `HttpHeaders` con los siguientes métodos:

- `setHeader(string $name, $value)`: Asigna un encabezado HTTP.
- `getHeader(string $name)`: Devuelve el valor actual de un encabezado HTTP específico.
- `hasHeader(string $name)`: Devuelve `true` si un encabezado existe.
- `removeHeader(string $name)`: Elimina un encabezado HTTP del *response*.
- `clearAllHeaders()`: Elimina todos los encabezados HTTP del *response*.
- `getAllHeaders()`: Devuelve el array de los encabezados HTTP del *response*.

Como usar `HttpResponse`:

```php
use rguezque\RouteCollection\{
    HttpResponse,
    SapiEmitter
};

$response = new HttpResponse('Hello world'); // Escribe contenido inicial (opcional)
$response->body->write(' Good morning!'); // Agrega contenido al cuerpo del response
$response->headers->setHeader('Authorization', 'Bearer 932ie3849ea43ur7');

SapiEmitter::emit($response); // Devuelve el response
```

Método abreviado para enviar un `HttpJsonResponse`, recibe un array y automaticamente lo codifica a formato JSON y agrega el encabezado necesario:

```php
use rguezque\RouteCollection\{
    HttpJsonResponse,
    SapiEmitter
};

$response = new HttpJsonResponse(['Hello' => 'world']);
SapiEmitter::emit($response);
```

### Emitir la respuesta

El método estático `SapiEmitter::emit` recibe un objeto `HttpResponse` y se encarga de enviar los encabezados HTTP y el cuerpo del response.

## Plantillas

La clase `TemplateEngine` permite hacer *fetch* de una plantilla PHP. Recibe como argumentos iniciales un directorio predeterminado donde se buscarán las plantillas y un directorio *cache*.

El método `TemplateEngine::fetch` recibe dos argumentos, un archivo de plantilla PHP y opcionalmente un *array* de parámetros que son enviados a la plantilla; y la devuelve como un string para ser renderizado posteriormente por ejemplo en un *response*.

```php
$view = new TemplateEngine(
    __DIR__.'/templates/views',
    __DIR__.'/templates/cache'
);

$template = $view->fetch('home.php', ['Hola' => 'mundo']);
```

## Base de datos

La clase `PdoSingleton` como su nombre lo indica devuelve una instancia singleton de una conexión PDO, para lo cual es necesario definir variables de entorno en un archivo `.env` con las siguientes variables (por ejemplo):

```
DB_DSN="mysql:dbname=my_base_de_datos;host=localhost;port=3306;charset=utf8"
DB_USERNAME="root"
DB_PASSWORD="3kues3eeu974ke"
```

Para cargar estas variables es necesario alguna dependencia como `vlucas/phpdotenv`, después delo cual solo se debe llamar estáticamente a `PdoSingleton` el cual devueve una instancia de PDO:

```php
$pdo = PdoSingleton::getInstance();
```

## Sesiones

La clase `SessionManager` permite manipular las variabes de `$_SESSION` a través de una instancia singleton.

- `getInstance(string $session_name = SessionManager::DEFAULT_SESSION_NAME)`: Devuelve una instancia singleton de `Sessionmanager`. Recibe como argumento un nombre de sesión, aunque es opcional es recomendable para evitar y reducir posibles colisiones de variables de sesión con otras aplicaciones web. 
  >[!TIP]
  >Utiliza variables de entorno (`.env`) para declarar un nombre de sesión a través de toda la aplicación.
- `start()`: Inicia o retoma una sesión activa. Siempre debe invocarse antes de los demás métodos (Permite encadenamiento `$session->start()->get('foo')`).
- `isStarted()`: Devuelve `true` si una sesión está activa, `false` en caso contrario.
- `set(string $key, mixed $value)`: Crea o sobrescribe una variable de sesión.
- `get(string $key, mixed $default)`: Devuelve una variable por nombre; si no existe devuelve el valor default especificado (`null` asignado por default).
- `remove(string $key)`: Elimina una variable por nombre.
- `destroy()`: Destruye la sesión activa.
- `exists(string $name)`: Devuelve `true` si una variable existe y no es nula.
- `getAll()`: Devuelve todo el *array* de variables.

```php
$session = SessionManager::getInstance();

$session->start()->set('foo', 'bar'); // Crea una variable
$session->start()->get('foo'); // Recupera una variable
```

## Globales

La clase `Globals` permite manipular las variables de ` $GLOBALS` a través de sus métodos estáticos:

- `set(string $name, mixed $value)`: Crea una variable global.
- `get(string $name, mixed $default = null)`: Devuelve una variable global por nombre o el valor default que se especifique si no existe.
- `has(string $name)`: Devuelve `true` si una variable existe y no es nula, `false` en caso contrario
- `all()`: Devuelve todo el *array* de variables.
- `remove(string $name)`: Elimina una variable por nombre.
- `clear()`: Elimina todas las variables.
