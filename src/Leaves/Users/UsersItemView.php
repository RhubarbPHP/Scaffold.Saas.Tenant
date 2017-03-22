<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Crud\Leaves\CrudView;
use Rhubarb\Scaffolds\AuthenticationWithRoles\Role;

class UsersItemView extends CrudView
{
    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf( "Username");
        $this->registerSubLeaf( "RoleID" );
    }

    protected function printViewContent()
    {
        $this->layoutItemsWithContainer(
            "",
            [
                "Username",
                "RoleID",
                "" => "{Save} {Cancel}"
            ]
        );

        parent::printViewContent();
    }
}
