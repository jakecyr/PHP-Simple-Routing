# PHP Simple Routing
Simple PHP routing library similar to JavaScript Express.

## Setup
Clone or download this repo and add the folder to your project. Require the "init.php" file to include all dependencies.

Allows for either request payload or parameters. 

Use Database class from the repo below if required and pass reference to the RouteManager instance upon creation:
[https://github.com/jakecyr/PhpDatabase](https://github.com/jakecyr/PhpDatabase)

Create a route for each type of endpoint:
```
$route = new Route();

$route
	->use(function($req, $db){
		//middleware
	})
	->get('all', function($req){
		print "All employees here";
	})

	->get('one', function($req){
		if(!isset($params->query["employee_id"])) new JsonError('Employee ID required');
		print "Employee ID: " . $params->query["employee_id"];
	})
	->post('add', function($req){
		print_r($req->body);
	});
```

Create a route manager and add the routes:
```
$routeManager = new RouteManager($dbConnection);

$routeManager
	->use('employees', require('routes/employees.php')
	->use('schedule', require('routes/schedule.php')
	->handleRoute();
```
Example endpoint URL:
...ajax.php?route=employees&path=one&employee_id=######
