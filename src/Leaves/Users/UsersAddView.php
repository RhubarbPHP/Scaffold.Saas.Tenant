<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Controls\Common\Text\TextBox;
use Rhubarb\Patterns\Mvp\Crud\CrudView;
use Rhubarb\Scaffolds\AuthenticationWithRoles\Role;

class UsersAddView extends CrudView
{
    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            new TextBox("email",100),
            $role = new DropDown("roleId")
        );

        $role->setSelectionItems([
            [ "", "Please Select" ],
            Role::find()
        ]);
    }


    protected function printViewContent()
    {
        $this->layoutItemsWithContainer(
            "",
            [
                "email",
                "roleId",
                "" => "{Save} {Cancel}"
            ]
        );

        parent::printViewContent();
    }

}