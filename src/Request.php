<?php
/**
 * dbog .../src/Request.php
 */

namespace Src;

class Request
{
    const CLI = 'cli';

    protected $requestUri;

    protected $args;


    public function __construct($args)
    {
        //Use first cli argument as uri
        if (isset ($args[1]))
        {
            $server['REQUEST_URI'] = '/' . $args[1] . '/';
            $this->args = $this->parseArguments(array_slice($args, 1));
        }
        else
        {
            $this->args = [];
        }
    }

    /**
     * Parse CLI command arguments
     * @param array $arguments
     * @return array Parsed arguments
     */
    protected function parseArguments($arguments)
    {
        $res = [];
        foreach ($arguments as $arg)
        {
            if (strpos($arg, '--') !== 0)
            {
                $res[$arg] = true;
                continue;
            }

            $parts = explode('=', $arg, 2);
            if (count($parts) != 2)
            {
                $res[substr($parts[0], 2)] = true;
            }
            else
            {
                $res[substr($parts[0], 2)] = $parts[1];
            }
        }

        return $res;
    }

    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param $name string
     * @param $default mixed|null
     * @return null
     */
    public function getArgument($name, $default = null)
    {
        return isset ($this->args[$name]) ? $this->args[$name] : $default;
    }

    /**
     * @param $index int
     * @return mixed
     */
    public function getArgumentName($index)
    {
        $keys = array_keys($this->args);
        return isset ($keys[$index]) ? $keys[$index] : null;
    }
}
