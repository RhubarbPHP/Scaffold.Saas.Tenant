<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Accounts;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class NewAccountModel extends LeafModel
{
    /**
     * @var Event
     */
    public $createAccountEvent;

    public $accountName = "";

    public function __construct()
    {
        parent::__construct();
        
        $this->createAccountEvent = new Event();
    }

}