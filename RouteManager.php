<?php
class RouteManager{
	private $databaseObject;
	private $routes;
	private $requestType;
	private $params;
	private $currentRoute;
	private $currentPath;

	function __construct($databaseObject){
		if(!isset($_GET['route'])) die('Route not set');
		if(!isset($_GET['path'])) die('Path not set');

		$this->currentRoute = $_GET['route'];
		$this->currentPath = $_GET['path'];

		unset($_GET["route"]);
		unset($_GET["path"]);

		$this->requestType = $_SERVER['REQUEST_METHOD'];
		$this->databaseObject = $databaseObject;
		$this->routes = array();
		$this->params = $this->requestType == 'GET' ? $_GET : $_POST;
	}
	function handleRoute(){
		if(isset($this->routes[$this->currentRoute])){
			$route = $this->routes[$this->currentRoute]->getEndpoint($this->requestType, $this->currentPath);
			$route->callback->__invoke($this->params);
		} else{
			die('Error: Route not handled');
		}
	}
	function addRoute($routeName, $routeObject){
		$this->routes[$routeName] = $routeObject;
	}
	function getType(){
		return $this->requestType;
	}
}
?>
