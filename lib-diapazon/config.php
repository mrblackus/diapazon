<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/12/14
 * Time: 11:01 PM
 */

namespace Diapazon;

define('DIAPAZON_VERSION', '0.1');

Autoloader::addPath('app/');
Autoloader::addPath('app/controller/');
Autoloader::addPath('app/service/');
Autoloader::addPath('app/service/dao/');
Autoloader::addPath('app/service/entity/');
Autoloader::addPath('lib-diapazon/');
Autoloader::addPath('lib-diapazon/database/');
Autoloader::addPath('lib-diapazon/router/');
