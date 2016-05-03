<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Crud\Leaves\CrudModel;

class UsersAddModel extends CrudModel
{
    public $email;

    public $roleId;
}