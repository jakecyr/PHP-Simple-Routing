<?php
class Response
{
    public function json($jsonData, $allStrings = false)
    {
        if (is_array($jsonData)) {
            if ($allStrings) {
                $jsonData = json_encode($jsonData);
            } else {
                $jsonData = json_encode($jsonData, JSON_NUMERIC_CHECK);
            }
        }

        header('Content-Type: application/json');
        print $jsonData;
        $this->close();
    }
    public function send($data)
    {
        print $data;
    }
    public function end($data = null)
    {
        if (!is_null($data)) print $data;
        $this->close();
    }
    public function close()
    {
        exit();
    }
}
