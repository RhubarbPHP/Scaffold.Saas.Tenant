<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\RestModels;

use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class MeTest extends TenantTestCase
{
    public function testAccurateListOfAccounts()
    {
        $this->loginWithMultipleAccounts();
        $accounts = Me::getAccounts();

        $this->assertCount( 2, $accounts );

        $this->logout();
        $this->loginWithSingleAccounts();
        $accounts = Me::getAccounts();

        $this->assertCount( 1, $accounts );

        $this->logout();
    }

}
