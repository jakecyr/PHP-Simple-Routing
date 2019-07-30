<?php
class RouteManager
{
    private $databaseObject;
    private $routes;
    private $requestType;
    private $params;
    private $currentRoute;
    private $currentPath;

    private $payload;

    function __construct($databaseObject)
    {
        $this->databaseObject = $databaseObject;
        $this->routes = [];
    }
    public function handleRoute()
    {
        if (!isset($_GET['route'])) new JsonError('Route not set');

        $paths = explode('/', $_GET['route']);

        if (sizeof($paths) > 1) {
            $this->currentRoute = array_shift($paths);
            $this->currentPath = implode('/', $paths);
        } else if (isset($_GET["path"])) {
            $this->currentRoute = $_GET['route'];
            $this->currentPath = $_GET['path'];
        } else {
            new JsonError('Path not set');
        }

        unset($_GET["route"]);
        unset($_GET["path"]);

        $this->requestType = $_SERVER['REQUEST_METHOD'];
        $this->params = null;

        //check request type and get params accordingly
        if ($this->requestType == 'GET') {
            $this->params = $_GET;
        } else if ($this->requestType == 'POST') {
            $postData = file_get_contents("php://input");
            $this->payload = null;

            if (!empty($postData)) {
                $payload = json_decode($postData);
                $this->params = json_decode(json_encode($payload), true);
            } else {
                $this->params = $_POST;
            }
        } else if ($this->requestType == 'PUT') {
            parse_str(file_get_contents('php://input'), $_PUT);
            $this->params = $_PUT;
        } else if ($this->requestType == 'DELETE') {
            parse_str(file_get_contents('php://input'), $_DELETE);
            $this->params = $_DELETE;
        } else {
            die('Request type not recognized');
        }

        if (isset($this->routes[$this->currentRoute])) {
            $route = $this->routes[$this->currentRoute]->getEndpoint($this->requestType, $this->currentPath);
            $route->callback->__invoke($this->params, $this->databaseObject);
        } else {
            new JsonError('Specified route is not handled');
        }

        $this->close();
        return $this;
    }
    public function addRoute($routeName, $routeObject)
    {
        $this->routes[$routeName] = $routeObject;
        return $this;
    }
    public function getType()
    {
        return $this->requestType;
    }
    public function close()
    {
        $this->databaseObject->close();
        $this->databaseObject = null;
        exit();
    }
    public function getRoutes()
    {
        return $this->routes;
    }
    public function getDbConnection()
    {
        return $this->databaseObject;
    }
}
