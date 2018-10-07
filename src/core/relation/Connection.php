<?php
/**
 * dbog .../src/core/relation/Connection.php
 */

namespace Src\Core\Relation;


class Connection extends \Src\Core\Relation
{
    /**
     * Connecting table name
     * @var string
     */
    protected $connecting;

    /**
     * {@inheritdoc}
     * @param string $connecting Connecting table name
     */
    public function __construct($tableName, $reference, $connecting)
    {
        $this->connecting = $connecting;
        parent::__construct($tableName, $reference);
    }

    /**
     * {@inheritdoc}
     */
    protected function setRelationName()
    {
        $this->relationName = $this->connecting;
    }

    /**
     * Get connecting
     * @return string
     */
    public function getConnecting()
    {
        return $this->connecting;
    }
}
