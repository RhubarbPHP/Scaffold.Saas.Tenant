<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Controls\Common\Buttons\Button;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Scaffolds\Saas\Tenant\Model\User;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class UsersCollectionView extends View
{
    /**
     * @var UsersCollectionModel
     */
    protected $model;

    protected function printViewContent()
    {
        parent::printViewContent();

        $this->printInviteButton();
        $this->printUsers();
    }

    /**
     * Called to allow a view to instantiate any sub Leaves that may be needed.
     *
     * Called by the presenter when it is ready to receive any corresponding events.
     */
    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf(
            new Button("ResendInvite", "Resend", function($email){
                $this->model->resentInviteEvent->raise(base64_decode($email));
            })
        );
    }


    protected function printInviteButton()
    {
        print "<a href='add/'>Invite a user</a>";
    }

    protected function printUsers()
    {
        // Get the list of users and show them.
        $users = SaasGateway::getUsers();

        ?>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php

            foreach ($users->items as $user) {
                $localUser = User::findByUUID($user->UUID);

                ?><tr><td><?=$user->Username;?></td><td><?=($localUser->Role) ? $localUser->Role->RoleName : "";?></td><td></td></tr><?php
            }

            ?>
            </tbody>
        </table>
        <?php

        // Get the list of users and show them.
        $users = SaasGateway::getOutstandingInvites();

        ?>
        <table>
            <thead>
            <tr>
                <th>Email</th>
                <th>Role</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($users->items as $invite) {
                $localUser = false;
                try {
                    $localUser = User::findByUUID($invite->UserUUID);
                } catch( RecordNotFoundException $er ){}

                ?>
                <tr>
                    <td><?=$invite->Email;?></td>
                    <td><?=($localUser && $localUser->Role) ? $localUser->Role->RoleName : "";?></td>
                    <td>Pending <?php $this->leaves["ResendInvite"]->displayWithIndex(base64_encode($invite->Email));?></td>
                </tr><?php
            }

            ?>
            </tbody>
        </table>
        <?php
    }

}