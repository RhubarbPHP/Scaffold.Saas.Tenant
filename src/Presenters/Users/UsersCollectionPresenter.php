<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Presenters\Users;

use Rhubarb\Leaf\Presenters\HtmlPresenter;

class UsersCollectionPresenter extends HtmlPresenter
{
    protected function createView()
    {
        return new UsersCollectionView();
    }
}