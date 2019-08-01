<?php
class RouteManager
{
    private $databaseObject;
    private $routes;

    function __construct($databaseObject)
    {
        $this->databaseObject = $databaseObject;
        $this->routes = [];
    }
    public function handleRoute()
    {
        $params = null;
        $requestType = $_SERVER['REQUEST_METHOD'];

        $params = $this->getRouteParams($requestType);
        $routeConfig = $this->parseRoute($_GET);

        $routeName = $routeConfig->route;
        $pathName = $routeConfig->path;

        if (isset($this->routes[$routeName])) {
            $route = $this->routes[$routeName];
            $endpoint = $route->getEndpoint($requestType, $pathName);

            //create new request object
            $request = new Request($requestType, $params, $routeName, $pathName);

            $response = new Response();

            //execute middleware functions if specified
            $route->executeMiddle($request,  $response, $this->databaseObject);

            //call the endpoint function
            $endpoint->callback->__invoke($request, $response, $this->databaseObject);
        } else {
            new JsonError('Specified route is not handled');
        }

        $this->close();
    }
    public function use($routeName, $routeObject)
    {
        $this->routes[$routeName] = $routeObject;
        return $this;
    }
    public function close()
    {
        $this->databaseObject->close();
        $this->databaseObject = null;
        exit();
    }
    private function parseRoute(&$queryString = null)
    {
        if (is_null($queryString)) return [];
        if (!isset($queryString['route'])) new JsonError('Route not set');

        $paths = explode('/', $queryString['route']);

        if (count($paths) >= 1) {
            $routeName = array_shift($paths);
            $pathName = implode('/', $paths);

            if (empty($pathName)) $pathName = null;

            unset($queryString['route']);
            unset($queryString['path']);

            return (object) [
                'route' => $routeName,
                'path' => $pathName,
            ];
        } else {
            return new JsonError('Path not set');
        }
    }
    private function getRouteParams($requestType = null)
    {
        if ($requestType == 'GET' || $requestType == 'DELETE') {
            return $_GET;
        } else if ($requestType == 'POST' || $requestType == 'PUT') {
            $postData = file_get_contents('php://input');

            if (!empty($postData)) {
                return json_decode($postData, true);
            } else {
                return $_POST;
            }
        } else {
            return [];
        }
    }
}
