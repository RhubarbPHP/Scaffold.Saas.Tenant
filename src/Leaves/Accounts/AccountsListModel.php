<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Accounts;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class AccountsListModel extends LeafModel
{
    /**
     * @var Event
     */
    public $acceptInviteEvent;

    /**
     * @var Event
     */
    public $selectAccountEvent;

    /**
     * @var array
     */
    public $accounts = [];

    /**
     * @var array
     */
    public $invites = [];

    public function __construct()
    {
        parent::__construct();
        
        $this->acceptInviteEvent = new Event();
        $this->selectAccountEvent = new Event();
    }
}