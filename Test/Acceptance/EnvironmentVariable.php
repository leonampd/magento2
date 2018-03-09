<?php

trait EnvironmentVariable
{
    /**
     * @var string $name
     * @return mixed
     */
    public function getEnvFromName($name)
    {
        return getenv($name);
    }
}
