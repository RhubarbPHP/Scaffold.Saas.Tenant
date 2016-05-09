<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class UsersCollection extends Leaf
{
    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return UsersCollectionView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new UsersCollectionModel();
        $model->resentInviteEvent->attachHandler(function($email){
            SaasGateway::inviteUser($email);
        });

        return $model;
    }
}