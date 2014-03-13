<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/12/14
 * Time: 11:31 PM
 */

$router = new \Diapazon\Router\Router();

$router->add(array("url" => "/", "controller" => "index", "action" => "index"));