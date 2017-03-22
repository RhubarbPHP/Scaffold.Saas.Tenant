<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users\Edit;

use Rhubarb\Leaf\Crud\Leaves\CrudLeaf;

class UserEdit extends CrudLeaf
{
    /**
     * @var UserEditModel
     */
    protected $model;

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return UserEditView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new UsersEditModel();
        $model->cancelPressedEvent->attachHandler(function(){
            throw new ForceResponseException(new RedirectResponse("../"));
        });

        $model->savePressedEvent->attachHandler(function(){
            $payloadUser = $this->model;

            try {
                $user = User::findByUUID($payloadUser->UserUUID);
            } catch (RecordNotFoundException $er){
                $user = new User();
                $user->UUID = $payloadUser->UserUUID;
            }

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