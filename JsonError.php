<?php
class JsonError
{
    private $error;

    function __construct($error, $die = true)
    {
        $this->error = $error;
        print json_encode((object) array('error' => $error));
        if ($die) exit();
        return $this;
    }
}
