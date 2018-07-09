# PHP Simple Routing
Simple PHP routing library similar to JavaScript Express.

## Setup
Clone or download this repo and add the folder to your project.
Create a route for each type of endpoint:
```
$route = new Route();

$route
	->get('all', function($params){
		print "All employees here";
	})

	->get('one', function($params){
		if(!isset($params["employee_id"])) die('Employee ID required');
		print "Employee ID: " . $params["employee_id"];
	});
```

Create a route manager and add the routes:
```
$routeManager = new RouteManager(null);
$routeManager->addRoute('employees', $route);
$routeManager->handleRoute();
```
