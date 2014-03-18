<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/18/14
 * Time: 5:59 PM
 */

namespace Diapazon;

interface IHydratable
{
    /**
     * @param boolean $DFEdited
     */
    public function _setDFEdited($DFEdited);

    /**
     * @param boolean $DFInserted
     */
    public function _setDFInserted($DFInserted);

} 