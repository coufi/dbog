<?php
/**
 * dbog .../src/core/View.php
 */

namespace Src\Core;

use Src\Database\AdapterInterface;
use Src\Syncer\Runner;

abstract class View extends Entity
{
    /** @var string */
    protected $viewName;

    /**
     * @param Schema $schema
     */
    public function __construct($schema)
    {
        $this->viewName = self::getLabel();
        parent::__construct($schema);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->viewName;
    }

    /**
     * Get information schema from database.
     * @param AdapterInterface $db
     * @param string $dbSchemaName
     * @return bool|string View definition
     */
    protected function getInformationSchema($db, $dbSchemaName)
    {
        $query = "
SELECT `V`.`VIEW_DEFINITION` AS definition
FROM `information_schema`.`VIEWS` AS `V`
WHERE `V`.`TABLE_SCHEMA` = '{$dbSchemaName}' AND `V`.`TABLE_NAME` = '{$this->getName()}'";

        return $db->fetchColumn($query);
    }

    /**
     * Sync database view.
     * @param Runner $runner
     */
    public function sync($runner)
    {
        $definition = $this->getInformationSchema($runner->getDb(), $runner->getDbSchemaName());

        // found definition in information schema, check for changes
        if ($definition)
        {
            //Definition from information schema has unusable format (includes db name)
            $r = $runner->getDb()->fetch("SHOW CREATE VIEW `{$this->getName()}`");
            $dbDefinition = $r['Create View'];
            $parts = explode("VIEW `{$this->getName()}` AS ", $dbDefinition, 2);
            $dbDefinition = $parts[1];

            // definition has been changed, sync in db
            $viewQuery = $this->getConfiguration()->getQuery();
            if ($dbDefinition != $viewQuery)
            {
                $runner->log("SYNC: Changing view {$this->getName()} query.");
                $runner->processQuery("ALTER VIEW {$this->getName()} AS {$viewQuery}");
            }
        }
        // definition not found, create new view
        else
        {
            $runner->log("SYNC: Creating view {$this->getName()}.");
            $runner->processQuery("CREATE VIEW {$this->getName()} AS {$this->getConfiguration()->getQuery()}");
        }
    }
}
