<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Leaf\Presenters\Controls\Selection\DropDown\DropDown;
use Rhubarb\Leaf\Presenters\Controls\Text\TextBox\TextBox;
use Rhubarb\Patterns\Mvp\Crud\CrudView;
use Rhubarb\Scaffolds\AuthenticationWithRoles\Role;

class UsersAddView extends CrudView
{
    public function createPresenters()
    {
        parent::createPresenters();

        $this->addPresenters(
            new TextBox("Email",100),
            $role = new DropDown("RoleID")
        );

        $role->setSelectionItems([
            [ "", "Please Select" ],
            Role::find()
        ]);
    }


    protected function printViewContent()
    {
        $this->printFieldset(
            "",
            [
                "Email",
                "RoleID",
                "" => "{Save} {Cancel}"
            ]
        );

        parent::printViewContent();
    }

}