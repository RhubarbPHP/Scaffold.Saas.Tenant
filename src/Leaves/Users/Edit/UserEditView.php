<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users\Edit;

use Rhubarb\Leaf\Crud\Leaves\CrudView;

class UserEditView extends CrudView
{
    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            $role = new DropDown("roleId")
        );

        $role->setSelectedItem($this->model->roleId);

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
                "roleId",
                "" => "{Save} {Cancel}"
            ]
        );

        parent::printViewContent();
    }
}