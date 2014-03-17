<?php
/**
 * Created by PhpStorm.
 * User: mathieu.savy
 * Date: 3/13/14
 * Time: 3:58 AM
 */

namespace Diapazon\Database;

use Diapazon\Generator\AbstractSchema;
use Diapazon\Generator\Table;

abstract class Driver implements IParamBindable, IDriveHasTypeString
{
    /** @var AbstractSchema */
    protected $abstractSchema;

    protected abstract function readDatabaseSchema();

    public abstract function writeFindProcedure(Table $table);

    public abstract function writeAllProcedure(Table $table);

    public abstract function writeCountProcedure(Table $table);

    public abstract function writeTakeProcedure(Table $table);

    public abstract function writeOneToManyProcedure($foreignTable, $foreignColumn, $foreignColumnClean, $tableName, $columnType);

    /**
     * @return AbstractSchema
     */
    public function getAbstractSchema()
    {
        $this->readDatabaseSchema();

        return $this->abstractSchema;
    }
} 