<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Crud\Leaves\CrudLeaf;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Scaffolds\Saas\Tenant\Model\User;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class UsersAdd extends CrudLeaf
{
    /**
     * @var UsersAddModel
     */
    protected $model;
    
    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return UsersAddView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new UsersAddModel();
        $model->cancelPressedEvent->attachHandler(function(){
            throw new ForceResponseException(new RedirectResponse("../"));
        });

        $model->savePressedEvent->attachHandler(function(){
            $payloadUser = SaasGateway::inviteUser($this->model->email);

            try {
                $user = User::findByUUID($payloadUser->UserUUID);
            } catch (RecordNotFoundException $er){
                $user = new User();
                $user->UUID = $payloadUser->UserUUID;
            }

            $user->Email = $this->model->email;
            $user->RoleID = $this->model->roleId;

            $this->onUserSaving($user);
            $user->save();

            throw new ForceResponseException(new RedirectResponse("../"));
        });

        return $model;
    }

    /**
     * An opportunity for extenders to augment the model with custom attributes taken in the real UI
     *
     * @param User $user
     */
    protected function onUserSaving(User $user)
    {

    }
}
