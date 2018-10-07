<?php
/**
 * dbog .../src/database/AdapterPDO.php
 */

namespace Src\Database;


class AdapterPDO implements AdapterInterface
{
    const CONNECTION_ATTEMPTS = 5;
    const CONNECTION_TIMEOUT_SECONDS = 2;

    /** @var \PDO */
    protected $pdo;

    /**
     * @param Instance $instanceConfig
     * @throws \PDOException
     */
    public function connect($instanceConfig)
    {
        $pdo = null;
        $exception = null;

        // try to repeat connection on error
        for ($i = self::CONNECTION_ATTEMPTS; $i; --$i)
        {
            $next = microtime(true) + self::CONNECTION_TIMEOUT_SECONDS / self::CONNECTION_ATTEMPTS;
            try
            {
                $pdo = $this->create($instanceConfig);
                $exception = null;
                break;
            }
            catch (\PDOException $exception)
            {
                // wait for next attempt
                usleep(max(($next - microtime(true)) * 1000 * 1000, 0));
            }
        }

        if ($exception !== null)
        {
            throw $exception;
        }

        $this->pdo = $pdo;
    }

    /**
     * Create new PDO instantion.
     * @param Instance $instanceConfig
     * @return \PDO
     */
    private function create($instanceConfig)
    {
        $serverConfig = $instanceConfig->getDbServer();

        $dsn = sprintf(
            '%s::dbname=%s;host=%s',
            $instanceConfig->getDbServer()->getDriver(),
            $instanceConfig->getDbSchemaName(),
            $serverConfig->getDbHost() . ':' . $serverConfig->getDbPort()
        );

        $pdo = new \PDO($dsn, $instanceConfig->getUser(), $instanceConfig->getPassword());
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function quote($val)
    {
        if (is_null($val))
        {
            return "'NULL'";
        }

        if (is_bool($val))
        {
            return $val ? 1 : 0;
        }

        return $this->pdo->quote($val);
    }

    /**
     * {@inheritdoc}
     */
    public function query($sql, $parameters = [])
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($parameters);
        return $sth;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($sql, $parameters = [])
    {
        $sth = $this->query($sql, $parameters);
        return $sth->fetch();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($sql, $parameters = [])
    {
        $sth = $this->query($sql, $parameters);
        return $sth->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($sql, $parameters = [])
    {
        $sth = $this->query($sql, $parameters);
        return $sth->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumnAll($sql, $parameters = [])
    {
        $sth = $this->query($sql, $parameters);
        return $sth->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchKeyValue($sql, $parameters = [])
    {
        $sth = $this->query($sql, $parameters);
        return $sth->fetchAll(\PDO::FETCH_KEY_PAIR);
    }
}
