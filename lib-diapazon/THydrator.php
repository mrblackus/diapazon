<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/18/14
 * Time: 5:53 PM
 */

namespace Diapazon;

trait THydrator
{
    /**
     * @param IHydratable $entity Object to hydrate
     * @param array       $data Associative array representing object value
     */
    protected static function hydrate(IHydratable &$entity, Array $data)
    {
        $r = new \ReflectionClass($entity);
        foreach ($data as $k => $v)
        {
            $methodName = "set" . Tools::capitalize($k);
            if ($r->hasMethod($methodName))
            {
                $method = $r->getMethod($methodName);
                $method->setAccessible(true);
                $method->invoke($entity, $v);
                $method->setAccessible(false);
            }
        }

        $entity->_setDFInserted(true);
        $entity->_setDFEdited(false);
    }
} 