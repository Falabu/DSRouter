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
        $parameter_name = null;

        if (preg_match_all('/{([^#]+?)}/', $route, $parameter) !== 0) {
            $parameter_name = $parameter[1][0];
            $start_index = strpos($route, $parameter[0][0]);
            $route = substr($route, 0, $start_index - 1);
        }

        if (in_array($method, $this->possible_methods)) {
            $route_index = $this->searchForRoute($route);

            if (null !== $route_index) {
                $this->routes[$route_index]->addCallFunctionToMethod($method, $function);
                if (null !== $parameter_name) {
                    $this->routes[$route_index]->setParamName($parameter_name);
                }
            } else {
                $this->routes[] = new Route($route, $method, $function, $parameter_name);
            }
        } else {
            echo "A METHOD nem támogatott: " . $route . ' ' . $method . ' ' . $function;
            //TODO: Hibakezelés
        }
    }

    public function findRoute()
    {
        $index_for_route = $this->searchForRoute($this->url_parser->getUrlWithoutHost());

        if ($index_for_route === null) {
            $index_for_route = $this->searchForRoute($this->url_parser->getURL());

            if ($index_for_route !== null) {
                $this->routes[$index_for_route]->setParamValue($this->url_parser->getLastParam());
            }
        }

        if ($this->routes[$index_for_route]->isParamSet() && $this->routes[$index_for_route]->issetParamValue() === false) {
            echo "Nincs paraméter a paraméteres utvonalhoz";
            //TODO: Hibakezelés
        }

        if (null !== $index_for_route) {
            if ($this->routes[$index_for_route]->methodExist($this->request->getMethod())) {
                $this->routes[$index_for_route]->callFunction($this->request->getMethod(), $this->request);
            } else {
                echo "Nincs METHOD az útvonalhoz megadva";
                //TODO:Hibakezelés
            }
        } else {
            echo "Nincs ilyen útvonal!";
            //TODO: Hibakezelés
        }

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