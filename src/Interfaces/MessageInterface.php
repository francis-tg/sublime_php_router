<?php

namespace Francis\SublimePhp\Interfaces;

interface MessageInterface
{
    function success(string $msg);
    function error(string $msg);
}
