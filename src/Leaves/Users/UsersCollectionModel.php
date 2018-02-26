<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Crown\Events\Event;
use Rhubarb\Leaf\Crud\Leaves\ModelBoundModel;
use Rhubarb\Leaf\Leaves\LeafModel;

class UsersCollectionModel extends ModelBoundModel
{
    /**
     * @var Event
     */
    public $resentInviteEvent;

    /** @var Event $revokeInviteEvent */
    public $revokeInviteEvent;

    /** @var Event $disableUserEvent */
    public $disableUserEvent;

    public function __construct()
    {
        parent::__construct();
        
        $this->resentInviteEvent = new Event();
        $this->revokeInviteEvent = new Event();
        $this->disableUserEvent = new Event();
    }
}