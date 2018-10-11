<?php
/**
 * dbog .../src/core/Column.php
 */

namespace Src\Core;

use Src\Core\Datatype\DtEnum;
use Src\Core\Datatype\DtSet;
use Src\Core\Table\Config;
use Src\Database\AdapterInterface;
use Src\Syncer\Runner;

class Column
{
    /** @var string */
    protected $columnName;

    /**  @var Config */
    protected $table;

    /**
     * Whether can be null.
     * @var bool
     */
    protected $null;

    /**
     * Default value.
     * @var string
     */
    protected $default;

    /**
     * Original table name before renaming.
     * @var string
     */
    protected $renamedFrom;

    /**
     * @var Datatype
     */
    protected $datatype;


    /**
     * @param $columnName string
     * @param $table Config
     */
    public function __construct($columnName, $table)
    {
        $this->columnName = $columnName;
        $this->table = $table;
        $this->null = false;
        $this->default = null;
        $this->renamedFrom = null;
        $this->datatype = null;
    }

    /**
     * Set whether column can be null.
     * @param boolean $null
     * @return Column
     */
    public function setNull($null = true)
    {
        $this->null = $null;
        return $this;
    }

    /**
     * Set column default value.
     * @param $default string
     * @return Column
     */
    public function setDefault($default)
    {
        if (is_bool($default))
        {
            $default = $default ? 1 : 0;
        }

        $this->default = $default;
        return $this;
    }

    /**
     * Set renamed from.
     * @param $renamedFrom string
     * @return Column
     */
    public function setRenamedFrom($renamedFrom)
    {
        $this->renamedFrom = $renamedFrom;
        return $this;
    }

    /**
     * Set foreign key.
     * @param null $tableName Referenced table name
     * @return Column
     */
    public function setFK($tableName = null)
    {
        // autogenerate if null
        if (is_null($tableName))
        {
            $tableName = substr($this->columnName, 3);      //remove 'id_'
        }

        $targetColumnName = Config::ID_PREFIX . $tableName;
        $this->table->addRelationMapping($tableName, [$this->columnName], [$targetColumnName]);

        if ($this->table->getSchema()->hasTable($tableName))
        {
            $targetColumn = $this->table->getSchema()->getTable($tableName)->getConfiguration()->getColumn($targetColumnName);
            $this->setDatatype($targetColumn->getDatatype());
        }
        else
        {
            // set default datatype if target cannot be found
            $this->setIntUnsigned();
        }

        return $this;
    }

    /**
     * Set datatype tiny int signed.
     * @return Column
     */
    public function setTinyIntSigned()
    {
        $this->datatype = new Datatype\DtTinyint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype tiny int unsigned.
     * @return Column
     */
    public function setTinyIntUnsigned()
    {
        $this->datatype = new Datatype\DtTinyint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype small int signed.
     * @return Column
     */
    public function setSmallIntSigned()
    {
        $this->datatype = new Datatype\DtSmallint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype small int unsigned.
     * @return Column
     */
    public function setSmallIntUnsigned()
    {
        $this->datatype = new Datatype\DtSmallint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype medium int signed.
     * @return Column
     */
    public function setMediumIntSigned()
    {
        $this->datatype = new Datatype\DtMediumint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype medium int unsigned.
     * @return Column
     */
    public function setMediumIntUnsigned()
    {
        $this->datatype = new Datatype\DtMediumint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype int signed.
     * @return Column
     */
    public function setIntSigned()
    {
        $this->datatype = new Datatype\DtInt();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype int unsigned.
     * @return Column
     */
    public function setIntUnsigned()
    {
        $this->datatype = new Datatype\DtInt();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype big int signed.
     * @return Column
     */
    public function setBigIntSigned()
    {
        $this->datatype = new Datatype\DtBigint();
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype big int unsigned.
     * @return Column
     */
    public function setBigIntUnsigned()
    {
        $this->datatype = new Datatype\DtBigint();
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype decimal signed.
     * @param int $precision Total decimal precision
     * @param int $fraction Num of fraction digits in decimal
     * @return Column
     */
    public function setDecimalSigned($precision = 10, $fraction = 0)
    {
        $this->datatype = new Datatype\DtDecimal();
        $this->datatype->setPrecision($precision);
        $this->datatype->setFraction($fraction);
        $this->datatype->setUnsigned(false);
        return $this;
    }

    /**
     * Set datatype decimal unsigned.
     * @param int $precision Total decimal precision
     * @param int $fraction Num of fraction digits in decimal
     * @return Column
     */
    public function setDecimalUnsigned($precision = 10, $fraction = 0)
    {
        $this->datatype = new Datatype\DtDecimal();
        $this->datatype->setPrecision($precision);
        $this->datatype->setFraction($fraction);
        $this->datatype->setUnsigned(true);
        return $this;
    }

    /**
     * Set datatype string.
     * @param int $length Total string length
     * @return Column
     */
    public function setString($length = null)
    {
        $this->datatype = new Datatype\DtString();
        $this->datatype->setLength($length);
        return $this;
    }

    /**
     * Set datatype char.
     * @param int $length Total string length
     * @return Column
     */
    public function setChar($length = null)
    {
        $this->datatype = new Datatype\DtChar();
        $this->datatype->setLength($length);
        return $this;
    }

    /**
     * Set datatype bool.
     * @return Column
     */
    public function setBool()
    {
        $this->datatype = new Datatype\DtBool();
        return $this;
    }

    /**
     * Set datatype year.
     * @return Column
     */
    public function setYear()
    {
        $this->datatype = new Datatype\DtYear();
        return $this;
    }

    /**
     * Set datatype date.
     * @return Column
     */
    public function setDate()
    {
        $this->datatype = new Datatype\DtDate();
        return $this;
    }

    /**
     * Set datatype datetime.
     * @return Column
     */
    public function setDatetime()
    {
        $this->datatype = new Datatype\DtDatetime();
        return $this;
    }

    /**
     * Set datatype time.
     * @return Column
     */
    public function setTime()
    {
        $this->datatype = new Datatype\DtTime();
        return $this;
    }

    /**
     * Set datatype text.
     * @return Column
     */
    public function setText()
    {
        $this->datatype = new Datatype\DtText();
        return $this;
    }

    /**
     * Set datatype text medium.
     * @return Column
     */
    public function setTextMedium()
    {
        $this->datatype = new Datatype\DtTextMedium();
        return $this;
    }

    /**
     * Set datatype enum.
     * @param $allowedValues array
     * @return Column
     */
    public function setEnum($allowedValues = [])
    {
        $this->datatype = new Datatype\DtEnum($allowedValues);
        return $this;
    }

    /**
     * Set datatype set.
     * @param array $allowedValues
     * @return Column
     */
    public function setSet($allowedValues = [])
    {
        $this->datatype = new Datatype\DtSet($allowedValues);
        return $this;
    }

    /**
     * Set datatype directly.
     * @param Datatype $datatype
     * @return Column
     */
    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;
        return $this;
    }

    /**
     * Get column name.
     * @return string
     */
    public function getName()
    {
        return $this->columnName;
    }

    /**
     * Whether is null.
     * @return boolean
     */
    public function isNull()
    {
        return $this->null;
    }

    /**
     * Get default value.
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get datatype.
     * @return Datatype
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * Get renamed from name.
     * @return string
     */
    public function getRenamedFrom()
    {
        return $this->renamedFrom;
    }

    /**
     * Get table config.
     * @return Config
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Whether is primary key column.
     * @return bool
     */
    public function isPrimaryKey()
    {
        return in_array($this->columnName, $this->table->getKeyPrimary()->getColumns());
    }


    /**
     * Whether is foreign key column.
     * @return bool
     */
    public function isForeignKey()
    {
        foreach ($this->table->getRelationsMapping() as $mapping)
        {
            if (in_array($this->columnName, $mapping->getColumns()))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [
     *                  'column_name' => (string),
     *                  'data_type' => (string),
     *                  'character_maximum_length' => (int),
     *                  'numeric_scale' => (int),
     *                  'is_nullable' => (string),
     *                  'column_default' => (string),
     *                  'collation_name' => (string),
     *                  'ordinal_position' => (int),
     *                  'column_type' => (string),
     *                  'is_unsigned' => (int)]
     */
    protected function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT
  `C`.`COLUMN_NAME` AS column_name,
  `C`.`DATA_TYPE` AS data_type,
  `C`.`CHARACTER_MAXIMUM_LENGTH` AS character_maximum_length,
  `C`.`NUMERIC_PRECISION` AS numeric_precision,
  `C`.`NUMERIC_SCALE` AS numeric_scale,
  `C`.`IS_NULLABLE` AS is_nullable,
  `C`.`COLUMN_DEFAULT` AS column_default,
  `C`.`COLLATION_NAME` AS collation_name,
  `C`.`ORDINAL_POSITION` AS ordinal_position,
  `C`.`COLUMN_TYPE` AS column_type,
  IF(`C`.`COLUMN_TYPE` LIKE '%unsigned', 1, 0) as `is_unsigned`
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `C`.`TABLE_NAME` = :table  AND `C`.`COLUMN_NAME` = :column";

        // definition found in information schema
        if ($result = $db->fetch($query, [':table' => $this->getTable()->getName(), ':column' => $this->getName()]))
        {
            return $result;
        }

        // definition not found in information schema, check whether table has been renamed
        $result = false;
        if (!is_null($this->getRenamedFrom()))
        {
            $result = $db->fetch($query, [':table' => $this->table->getDbTableName(), ':column' => $this->getRenamedFrom()]);
        }

        return $result;
    }

    /**
     *  Sync column with database.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $columnsList = $this->getTable()->getColumnNames();
        $ordinalPositions = $this->getTable()->getColumnOrdinalPositions();
        $ordinalPosition = $ordinalPositions[$this->getName()];

        $r = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // found definition in information schema, check for changes
        if ($r)
        {
            list ($dbName, $dbDatatype, $length, $precision, $scale, $nullable, $default, $collation, $position, $type, $unsigned) = $r;

            $recreate = false;
            $datatype = $this->getDatatype();
            $renameColumn = !is_null($this->getRenamedFrom()) && $this->getRenamedFrom() != $dbName;

            // column has been renamed
            if ($renameColumn)
            {
                $runner->log("SYNC: Changing column {$this->getRenamedFrom()} name to {$this->getName()}.");
                $recreate = true;
            }

            // changed datatype definition
            if ($dbDatatype != $datatype->getSqlDatatype())
            {
                $runner->log("SYNC: Changing column {$this->getName()} data type to {$datatype->getSqlDefinition()}");
                $recreate = true;
            }

            // changed datatype maximal length
            if ($length !== null && $datatype->getSqlMaxLength() !== null && $length != $datatype->getSqlMaxLength())
            {
                $runner->log("SYNC: Changing column {$this->getName()} length to {$datatype->getSqlMaxLength()}");
                $recreate = true;
            }

            // changed datatype precision
            if ($precision !== null && $precision != $datatype->getSqlPrecision())
            {
                $runner->log("SYNC: Changing column {$this->getName()} precision to {$datatype->getSqlPrecision()}");
                $recreate = true;
            }

            // changed datatype scale
            if ($scale !== null && $scale != $datatype->getSqlScale())
            {
                $runner->log("SYNC: Changing column {$this->getName()} scale to {$datatype->getSqlScale()}");
                $recreate = true;
            }

            // changed nullable option
            if ($nullable == 'YES' && !$this->isNull() || $nullable == 'NO' && $this->isNull())
            {
                $this->log("SYNC: Changing column {$this->getName()} nullability to " . ($this->isNull() ? 'YES' : 'NO') . '.');
                $recreate = true;
            }

            // changed default value
            $actual = $runner->getDb()->quote($default);
            $required = $runner->getDb()->quote($this->getDefault());
            if ($actual !== $required)
            {
                $runner->log("SYNC: Changing column {$this->getName()} default to " . $required);
                $recreate = true;
            }

            // changed collation
            if ($collation !== null && $collation != $this->getTable()->getSchema()->getDbCollation())
            {
                $runner->log("SYNC: Changing column {$this->getName()} collation to {$this->getTable()->getSchema()->getDbCollation()}");
                $recreate = true;
            }

            // changed ordinal position
            if ($position !== null && $position != ($ordinalPosition + 1))
            {
                $runner->log("SYNC: Changing column {$this->getName()} ordinal position.");
                $recreate = true;
            }

            // changed definition for ENUM or SET
            // todo Refactor hardcoded instaceof
            if ($datatype instanceof DtEnum || $datatype instanceof DtSet)
            {
                if ($type != $datatype->getSqlDefinition())
                {
                    $runner->log("SYNC: Changing column {$this->getName()} data type to {$datatype->getSqlDefinition()}");
                    $recreate = true;
                }
            }

            // changed unsigned option
            if (!$recreate && ((boolean) $unsigned != $datatype->isUnsigned()))
            {
                $runner->log("SYNC: Changing column {$this->getName()} data type to {$datatype->getSqlDefinition()}");
                $recreate = true;
            }

            // definition has been changed, sync in db
            if ($recreate)
            {
                // must drop all constraints in other tables first
                if ($this->isPrimaryKey())
                {
                    $this->dropConstraints($runner);
                }

                // must drop mapping constraint first
                if ($this->isForeignKey())
                {
                    $this->dropMapping($runner);
                }

                // drop old primary key if changing to autoincremental
                $primary = $this->getTable()->getKeyPrimary();
                $allowPrimaryKey = false;
                if ($primary->isAutoincremental() && $primary->getColumns() == [$this->getName()])
                {
                    $aiInDb = $this->isAutoincrementalOnSync($runner->getDb(), $runner->getDbSchemaName(),  true);
                    if (!$aiInDb)
                    {
                        $primary->dropCurrentPrimaryKeyConstraints();
                        $runner->processQuery("ALTER TABLE `{$this->table->getnName()}` DROP PRIMARY KEY");
                        $allowPrimaryKey = true;
                    }
                }

                $columnPosition = $ordinalPosition == 0 ? ' FIRST' : " AFTER `" . $columnsList[$ordinalPosition - 1] . '`';
                $changedColumn = $renameColumn ? " `{$this->getRenamedFrom()}` " : "`{$this->getName()}`";

                $sql = $this->getSQLCreate($runner->getDb(), $allowPrimaryKey);
                $this->processQuery("ALTER TABLE `{$this->table->getname()}` CHANGE $changedColumn $sql $columnPosition");
            }
        }
        else
        {
            // drop old primary key if adding new autoincremental
            $primary = $this->getTable()->getKeyPrimary();
            if ($primary->isAutoincremental() && $primary->getColumns() == [$this->getName()])
            {
                $primary->dropCurrentPrimaryKeyConstraints();
                $runner->processQuery("ALTER TABLE `{$this->getTable()->getName()}` DROP PRIMARY KEY");
            }

            $columnPosition = $ordinalPosition == 0 ? ' FIRST' : " AFTER `" . $columnsList[$ordinalPosition - 1] . "`";
            $runner->log("SYNC: Creating column {$this->getName()}.");
            $sql = $this->getSQLCreate($runner->getDb());
            $this->processQuery("ALTER TABLE `{$this->getTable()->getName()}` ADD $sql $columnPosition");
        }
    }


    /**
     * Whether is incrementable in database
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @param bool $initPhase Whether has been udpdated in db already
     * @return bool
     */
    public function isAutoincrementalOnSync($db, $dbSchemaName, $initPhase)
    {
        $table = $this->getTable()->getName();
        // identify real name in db
        $columnName = $initPhase && $this->getRenamedFrom() ? $this->getRenamedFrom() : $this->getName();

        $query = "
SELECT `C`.`EXTRA` = 'auto_increment' AS `autoincremental`
FROM `information_schema`.`COLUMNS` AS `C`
WHERE `C`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `C`.`TABLE_NAME` = '{$table}' AND `C`.`COLUMN_NAME` = '{$columnName}'";

        return (boolean) $db->fetchColumn($query);
    }

    /**
     * Drop all constraints for this column.
     * @param Runner $runner
     */
    public function dropConstraints($runner)
    {
        // run all mappings in schema
        foreach ($this->table->getSchema()->getAllTables() as $table)
        {
            $tableConfig = $table->getConfiguration();
            $mappings = $tableConfig->getRelationsMapping();

            foreach ($mappings as $mapping)
            {
                // find specified target column & drop its mapping
                $targets = array_combine($mapping->getTargets() ? $mapping->getTargets() : $mapping->getColumns(), $mapping->getColumns());
                if (isset ($targets[$this->getName()]))
                {
                    $tableColumn = $tableConfig->getColumn($targets[$this->getName()]);
                    $tableColumn->dropMapping($runner);
                }
            }
        }
    }

    /***
     * Drop mapping relation for this column.
     * @param Runner $runner
     */
    public function dropMapping($runner)
    {
        if ($constraints = $this->getConstraints($runner->getDb(), $runner->getDbSchemaName()))
        {
            foreach ($constraints as $name)
            {
                $this->log("SYNC: Dropping foreign key {$name}.");
                $this->processQuery("ALTER TABLE `{$this->table->getName()}` DROP FOREIGN KEY {$name}");
            }
        }
    }

    /**
     * Get db column constraints.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return array [(string) $constraintName,...]
     */
    protected function getConstraints($db, $dbSchemaName)
    {
        $query = "
SELECT `T`.`CONSTRAINT_NAME`
FROM `information_schema`.`TABLE_CONSTRAINTS` AS `T`

INNER JOIN
(
SELECT `KU`.`CONSTRAINT_NAME`
FROM `information_schema`.`KEY_COLUMN_USAGE` AS `KU`
WHERE `KU`.`TABLE_SCHEMA` = '{$dbSchemaName}'
AND `KU`.`TABLE_NAME` = '{$this->table->getName()}'
AND `KU`.`COLUMN_NAME` = '{$this->getName()}'
) AS `KU` ON `T`.`CONSTRAINT_NAME` = `KU`.`CONSTRAINT_NAME`
WHERE `T`.`CONSTRAINT_TYPE` = 'FOREIGN KEY'
AND `T`.`TABLE_SCHEMA` = '{$dbSchemaName}'
AND `T`.`TABLE_NAME` = '{$this->table->getName()}'
";

        return $db->fetchColumnAll($query);
    }

    /**
     * Get SQL create statement for column.
     * @param AdapterInterface $db
     * @param bool $allowDirectPrimaryKey
     * @return string
     */
    public function getSQLCreate($db, $allowDirectPrimaryKey = true)
    {
        $sql = "`{$this->getName()}` {$this->getDatatype()->getSqlDefinition()} ";
        $sql .= $this->isNull() ? 'NULL' : 'NOT NULL';

        if ($this->getDefault() !== null)
        {
            $sql .= ' DEFAULT ' . $db->quote($this->getDefault());
        }

        $primary = $this->getTable()->getKeyPrimary();
        if ($primary->isAutoincremental() && $primary->getColumns() == [$this->getName()])
        {
            // defined in constraints later
            if ($allowDirectPrimaryKey)
            {
                $sql .= ' PRIMARY KEY';
            }

            $sql .= ' AUTO_INCREMENT';
        }

        return $sql;
    }
}
