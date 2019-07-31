<?php
class RouteManager
{
    private $databaseObject;
    private $routes;
    private $requestType;

    function __construct($databaseObject)
    {
        $this->databaseObject = $databaseObject;
        $this->routes = [];
    }
    public function handleRoute()
    {
        if (!isset($_GET['route'])) new JsonError('Route not set');

        $paths = explode('/', $_GET['route']);
        $params = null;
        $requestType = $_SERVER['REQUEST_METHOD'];
        $routeName = null;
        $pathName = null;

        if (count($paths) > 1) {
            $routeName = array_shift($paths);
            $pathName = implode('/', $paths);
        } else if (isset($_GET["path"])) {
            $routeName = $_GET['route'];
            $pathName = $_GET['path'];
        } else {
            new JsonError('Path not set');
        }

        unset($_GET["route"]);
        unset($_GET["path"]);

        //check request type and get params accordingly
        if ($requestType == 'GET' || $requestType == 'DELETE') {
            $params = $_GET;
        } else if ($requestType == 'POST' || $requestType == 'PUT') {
            $postData = file_get_contents('php://input');

            if (!empty($postData)) {
                $params = json_decode($postData, true);
            } else {
                $params = $_POST;
            }
        } else {
            new JsonError('Request type not recognized');
        }

        if (isset($this->routes[$routeName])) {
            $route = $this->routes[$routeName];
            $endpoint = $route->getEndpoint($requestType, $pathName);

            //create new request object
            $request = new Request($requestType, $params, $routeName, $pathName);

            //execute middleware functions if specified
            $route->executeMiddle($request, $this->databaseObject);

            //call the endpoint function
            $endpoint->callback->__invoke($request, $this->databaseObject);
        } else {
            new JsonError('Specified route is not handled');
        }

        $this->close();
        return $this;
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
}
