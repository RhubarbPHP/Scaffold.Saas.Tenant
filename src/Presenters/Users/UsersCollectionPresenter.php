<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Leaf\Presenters\HtmlPresenter;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;

class UsersCollectionPresenter extends HtmlPresenter
{
    protected function createView()
    {
        return new UsersCollectionView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler("ResendInvite", function($email){
            SaasGateway::inviteUser($email);
        });
    }
}