<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Leaf\Crud\Leaves\ModelBoundLeaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class UsersCollection extends ModelBoundLeaf
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
        $model->revokeInviteEvent->attachHandler(function($inviteID){
            SaasGateway::revokeUserInvite($inviteID);
        });
        $model->disableUserEvent->attachHandler(function($userUuid){
            SaasGateway::disableUser($userUuid);
        });

        return $model;
    }
}