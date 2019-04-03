<?php


namespace DSRouter;


class Route
{
    private $route_name;

    private $function_per_method;

    public function __construct($route_name, $method, $function)
    {
        $this->route_name = $route_name;
        $this->function_per_method[$method] = $function;
    }

    public function addCallFunctionToMethod($method, $function){
        $this->function_per_method[$method] = $function;
    }

    public function getRouteName(){
        return $this->route_name;
    }

    public function methodExist($method){
        if(isset($this->function_per_method[$method])){
            return true;
        }

        return false;
    }

    public function callFunction($method, Request $request){
        list($class, $function) = explode('@', $this->function_per_method[$method]);
        if(class_exists($class)){
            $called_class = new $class();

            if(method_exists($called_class, $function)){
                $called_class->$function($request);
            }else{
                echo "Nincs ilyen hívható metódus az osztályban";
                //TODO: Hibakezelés
            }
        }else{
            echo "Nincs ilyen hívható osztály ami a útvonalhoz van kapcsolva";
            //TODO: Hibakezelés
        }
    }
}