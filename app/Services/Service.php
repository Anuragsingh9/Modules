<?php
namespace App\Services;
/**
 *
 *
 * Class Service
 * @package App\Services
 */
class Service {
    static $instances = [];

    /**
     * @return static|null
     */
    public static function getInstance() {
        $class = get_called_class();
        if (isset(Service::$instances[$class]) && Service::$instances[$class]) {
            return Service::$instances[$class];
        }
        return Service::$instances[$class] = new static();
    }
}
