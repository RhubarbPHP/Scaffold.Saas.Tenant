<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Presenters\Forms\Form;
use Rhubarb\Scaffolds\Saas\Tenant\Model\User;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Stem\Exceptions\RecordNotFoundException;

class UsersAddPresenter extends Form
{
    /**
     * Called to create and register the view.
     *
     * The view should be created and registered using RegisterView()
     * Note that this will not be called if a previous view has been registered.
     *
     * @see Presenter::registerView()
     */
    protected function createView()
    {
        return new UsersAddView();
    }

    /**
     * Called to initialise the view.
     *
     * This method should be used to attach any event handlers. The view must first be created
     * using CreateView
     *
     * Do not apply any settings that might be overriden with default values in ApplyModelToView() or that
     * need to use the model. The reason for this is that after the view is initialised events are
     * processed that might change the model or view directly. Just before presenting the view we
     * call ApplyModelToView() to apply any remaining settings to the view (usually model data). If we apply the
     * model data too early it will be re-applied just before presentation with results that can sometimes
     * be hard to predict.
     *
     * @see Presenter::createView()
     * @see Presenter::UpdateView()
     */
    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("CancelPressed", function(){
            throw new ForceResponseException(new RedirectResponse("../"));
        });

        $this->view->attachEventHandler("SavePressed", function(){
            $payloadUser = SaasGateway::inviteUser($this->Email);

            try {
                $user = User::findByUUID($payloadUser->UserUUID);
            } catch (RecordNotFoundException $er){
                $user = new User();
                $user->UUID = $payloadUser->UserUUID;
            }

            $user->RoleID = $this->RoleID;
            $user->save();

            throw new ForceResponseException(new RedirectResponse("../"));
        });
    }
}