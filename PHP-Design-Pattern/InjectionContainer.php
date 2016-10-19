<?php

class InjectionContainer {

    /**
     * @var array $instances
     */
    private $instances = array();

    /**
     * @var array $factories
     */
    private $factories = array();

    public function set($key, Callable $resolver) {
        $this->instances[$key] = $resolver();
    }

    public function setFactory($key, Callable $resolver) {
        $this->factories[$key] = $resolver;
    }

    public function setInstance($instance) {
        $reflector = new \ReflectionClass($instance);
        $this->instances[$reflector->getName()] = $instance;
    }

    public function get($key) {
        return (isset($this->factories[$key])) ? $this->getFactory($key) : $this->getInstance($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    private function getFactory($key) {
        return $this->factories[$key]();
    }

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    private function getInstance($key) {
        if (!isset($this->instances[$key])) {
            $reflectedClass = new \ReflectionClass($key);
            if (!$reflectedClass->isInstantiable())
                throw new \Exception($key . ' is not instantiable');
            $this->instances[$key] = $this->createObjectInstance($reflectedClass);
        }
        return $this->instances[$key];
    }

    /**
     * @param \ReflectionClass $reflectedClass
     * @return object
     */
    private function createObjectInstance(\ReflectionClass $reflectedClass) {
        $classConstructor = $reflectedClass->getConstructor();
        if ($classConstructor) {
            $parametersToInject = $this->buildParameterList($classConstructor);
            return $reflectedClass->newInstanceArgs($parametersToInject);
        } else {
            return $reflectedClass->newInstance();
        }
    }

    /**
     * @param \ReflectionMethod $classConstructor
     * @return array
     */
    private function buildParameterList(\ReflectionMethod $classConstructor) {
        $constructorParameters = $classConstructor->getParameters();
        $parametersToInject = array();
        foreach ($constructorParameters as $parameter)
            $parametersToInject[] = $this->buildParameter($parameter);
        return $parametersToInject;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed|null
     */
    private function buildParameter(\ReflectionParameter $parameter) {
        return ($parameter->getClass()) ?
            $this->get($parameter->getClass()->getName()) :
            $parameter->getDefaultValue();
    }

}
