<?php
class Route
{
    private $endpoints;
    private $middle;

    public function __construct()
    {
        $this->endpoints = [
            'GET' => [],
            'POST' => [],
            'DELETE' => [],
            'PUT' => [],
        ];

        $this->middle = [];

        return $this;
    }
    private function addPath($type, $path, $callback)
    {
        $this->endpoints[$type][$path] = (object) ['callback' => $callback];
        return $this;
    }
    public function use($callback)
    {
        array_push($this->middle, $callback);
        return $this;
    }
    public function get($path, $callback)
    {
        $this->addPath('GET', $path, $callback);
        return $this;
    }
    public function post($path, $callback)
    {
        $this->addPath('POST', $path, $callback);
        return $this;
    }
    public function put($path, $callback)
    {
        $this->addPath('PUT', $path, $callback);
        return $this;
    }
    public function delete($path, $callback)
    {
        $this->addPath('DELETE', $path, $callback);
        return $this;
    }
    public function getEndpoint($type, $path)
    {
        if (isset($this->endpoints[$type][$path])) {
            return $this->endpoints[$type][$path];
        } else {
            new JsonError('Endpoint does not exist');
        }
    }
    public function executeMiddle(&$req, $res, $db)
    {
        foreach ($this->middle as $middleFunction) {
            $middleFunction($req, $res, $db);
        }
    }
    public function getPaths()
    {
        return $this->endpoints;
    }
}
