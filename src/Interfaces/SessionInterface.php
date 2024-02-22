<?php

namespace Francis\SublimePhp\Interfaces;

interface SessionInterface
{
    function addSession(string $key,mixed $value);
    function removeSession(string $key);
}
