<?php

namespace Route;

use Controller\EmailController;

class EmailRoute extends BaseRoute {
    function routing($url): bool
    {
        $EmailController = new EmailController();

        if($this->routeCheck($url, "email/send", "POST")) {
            $EmailController->sendEmailController();
            return true;
        }  else {
            return false;
        }
    }
}