<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Leaves\LeafModel;

class UsersCollectionModel extends LeafModel
{
    /**
     * @var Event
     */
    public $resentInviteEvent;

    public function __construct()
    {
        parent::__construct();
        
        $this->resentInviteEvent = new Event();
    }
}