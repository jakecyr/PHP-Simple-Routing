<?php
class Route{
	private $endpoints;

	public function __construct(){
		$this->endpoints = array(
			'GET' => array(),
			'POST' => array()
		);
	}
	public function get($path, $callback){
		$this->endpoints['GET'][$path] = (object) array(
			'callback' => $callback
		);

		return $this;
	}
	public function post($path, $callback){
		$this->endpoints['POST'][$path] = (object) array(
			'callback' => $callback
		);

		return $this;
	}
	public function getEndpoint($type, $path){
		if(isset($this->endpoints[$type][$path])){
			return $this->endpoints[$type][$path];
		} else{
			die('Endpoint does not exist');
		}
	}
}
?>
