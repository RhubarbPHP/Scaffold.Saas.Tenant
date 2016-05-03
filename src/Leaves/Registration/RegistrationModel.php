<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Registration;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class RegistrationModel extends LeafModel
{
    public $inviteId;

    public $forename = "";

    public $surname = "";

    public $email = "";

    public $username = "";

    public $newPassword = "";

    public $newPasswordConfirm = "";

    /**
     * @var Event
     */
    public $createUserEvent;

    public function __construct()
    {
        $this->createUserEvent = new Event();
    }

}