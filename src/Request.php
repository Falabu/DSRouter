<?php


namespace DSRouter;


class Request
{
    private $method;

    private $headers;

    private $request_data;

    public function __construct()
    {
        $this->setMethod();
        $this->setHeaders();
        $this->setData();
    }

    public function getRequestContent()
    {
        return $this->request_data['body'];
    }

    public function getAllGet()
    {
        return $this->request_data['get'];
    }

    public function getAllPost()
    {
        return $this->request_data['post'];
    }

    public function findInGet($key)
    {
        return $this->findRequestData('get', $key);
    }

    public function findInPost($key)
    {
        return $this->findRequestData('post', $key);
    }

    public function get($key)
    {
        $value = null;

        foreach ($this->request_data as $method => $data) {
            if ($method !== 'body' && $value === null) {
                $value = $this->findRequestData($method, $key);
            }
        }

        return $value;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getJSONContent($to_array = false)
    {
        $json_data = null;

        if (!empty($this->request_data['body'])) {
            if ($json = json_decode($this->request_data['body'], $to_array)) {
                $json_data = $json;
            }
        } else {
            //TODO: Hibakezelés
        }

        return $json_data;
    }

    public function getHeader($header){
        return $this->headers[$header];
    }

    private function findRequestData($method, $key)
    {
        $value = null;

        foreach ($this->request_data[$method] as $get_key => $get_value) {
            if ($get_key === $key) {
                $value = $get_value;
            }
        }

        return $value;
    }

    private function setMethod()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    private function setHeaders()
    {
        $this->headers = getallheaders();
    }

    private function setData()
    {
        if (isset($_GET)) {
            $this->request_data['get'] = $_GET;
        }

        if (isset($_POST)) {
            $this->request_data['post'] = $_POST;
        }

        $this->request_data['body'] = file_get_contents('php://input');
    }

}