<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Leaves\Users;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\Model\User;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafModel;
use Rhubarb\Leaf\Crud\Leaves\CrudLeaf;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class UsersItem extends CrudLeaf
{
    /**
     * @var UsersItemModel
     */
    protected $model;

    /**
     * Returns the name of the standard view used for this leaf.
     *
     * @return string
     */
    protected function getViewClass()
    {
        return UsersItemView::class;
    }

    /**
     * Should return a class that derives from LeafModel
     *
     * @return LeafModel
     */
    protected function createModel()
    {
        $model = new UsersItemModel();
        $model->cancelPressedEvent->attachHandler(function(){
            throw new ForceResponseException(new RedirectResponse("../"));
        });

        $model->savePressedEvent->attachHandler(function(){
            $payloadUser = $this->model->restModel;
            
            $this->onUserSaving($payloadUser);
            $payloadUser->save();

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
