<?php


namespace DSRouter;

class Router
{
    private $routes = array();
    private $request;
    private $url_parser;

    private $possible_methods = ['POST', 'GET', 'DELETE', 'PUT', 'OPTIONS'];

    public function __construct()
    {
        $this->request = new Request();
        $this->url_parser = new URLParser();

    }

    public function addRoute($route, $method, $function)
    {
        $method = strtoupper($method);

        if (in_array($method, $this->possible_methods)) {
            $route_index = $this->searchForRoute($route);

            if (null !== $route_index) {
                $this->routes[$route_index]->addCallFunctionToMethod($method, $function);
            } else {
                $this->routes[] = new Route($route, $method, $function);
            }
        }else{
            echo "A METHOD nem támogatott: " . $route . ' ' . $method . ' '. $function;
            die();
            //TODO: Hibakezelés
        }
    }

    public function findRoute()
    {
        $index_for_route = $this->searchForRoute($this->url_parser->getUrlWithoutHost());

        if (null !== $index_for_route) {
            if ($this->routes[$index_for_route]->methodExist($this->request->getMethod())) {
                $this->routes[$index_for_route]->callFunction($this->request->getMethod(), $this->request);
            } else {
                echo "Nincs ilyen METHOD az útvonalhoz megadva";
                //TODO:Hibakezelés
            }
        } else {
            echo "Nincs ilyen útvonal!";
            //TODO: Hibakezelés
        }

    }

    public function printAllRoute()
    {

    }

    private function searchForRoute($route_name)
    {
        $index_for_route = null;

        foreach ($this->routes as $key => $route) {
            if ($route->getRouteName() === $route_name) {
                $index_for_route = $key;
            }
        }

        return $index_for_route;
    }

}