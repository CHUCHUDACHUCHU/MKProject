<?php

namespace Route;

use Utils\RouteUtils;

abstract class BaseRoute
{
    use RouteUtils;

    abstract function routing($url): bool;
}

?>