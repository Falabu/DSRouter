<?php


namespace DSRouter;

use ReflectionMethod;

class Route
{
    private $route_name;

    private $function_per_method;

    private $parameter_name;

    private $parameterizable = false;

    private $parameter_value;

    public function __construct($route_name, $method, $function, $parameter_name)
    {
        $this->route_name = $route_name;
        $this->function_per_method[$method] = $function;

        if (null !== $parameter_name) {
            $this->parameter_name = $parameter_name;
            $this->parameterizable = true;
        }

    }

    public function addCallFunctionToMethod($method, $function)
    {
        $this->function_per_method[$method] = $function;
    }

    public function getRouteName()
    {
        return $this->route_name;
    }

    public function methodExist($method)
    {
        if (isset($this->function_per_method[$method])) {
            return true;
        }

        return false;
    }

    public function callFunction($method, Request $request)
    {
        list($class, $function) = explode('@', $this->function_per_method[$method]);

        if (class_exists($class)) {
            $called_class = new $class();
            $class_method_reflection = null;
            $parameter_array = array();

            try {
                $class_method_reflection = new ReflectionMethod($called_class, $function);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
                //TODO:Hibakezelés
            }

            if ($class_method_reflection) {
                $class_method_parameters = $class_method_reflection->getParameters();

                foreach ($class_method_parameters as $method_parameter) {
                    if ($method_parameter->getClass()) {
                        if ($method_parameter->getClass()->name === 'DSRouter\Request') {
                            $parameter_array[] = $request;
                        }
                    }elseif ($method_parameter->name === $this->parameter_name){
                        $parameter_array[] = $this->parameter_value;
                    }else{
                        $parameter_array[] = "Nincs paraméter";
                        //TODO: Hibakezelés
                    }

                }

                $class_method_reflection->invokeArgs($called_class, $parameter_array);
            }

        } else {
            echo "Nincs ilyen hívható osztály ami a útvonalhoz van kapcsolva";
            //TODO: Hibakezelés
        }
    }

    public function isParamSet()
    {
        return $this->parameterizable;
    }

    public function setParamValue($param)
    {
        $this->parameter_value = $param;
    }

    public function issetParamValue()
    {
        $isset = true;

        if (!isset($this->parameter_value)) {
            $isset = false;
        }

        if (strlen($this->parameter_value) === 0) {
            $isset = false;
        }

        return $isset;

    }
}