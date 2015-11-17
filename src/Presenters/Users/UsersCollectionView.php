<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Leaf\Views\HtmlView;
use Rhubarb\Scaffolds\Saas\Tenant\Model\User;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class UsersCollectionView extends HtmlView
{
    protected function printViewContent()
    {
        parent::printViewContent();

        $this->printInviteButton();
        $this->printUsers();
    }

    protected function printInviteButton()
    {
        print "<a href=''>Invite a user</a>";
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

        foreach ($users->items as $user) {
            print $user->Email."</br>";
        }
    }

}