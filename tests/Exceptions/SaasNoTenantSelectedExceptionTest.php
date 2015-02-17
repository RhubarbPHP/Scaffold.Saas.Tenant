<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\Exceptions;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\Exceptions\SaasNoTenantSelectedException;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;

class SaasNoTenantSelectedExceptionTest extends TenantTestCase
{
    public function testRedirectOccurs()
    {
        $this->loginWithMultipleAccounts();

        try
        {
            new SaasNoTenantSelectedException();

            $this->fail("Throwing a SaasNoTenantSelectedException should have failed causing a redirect");
        }
        catch( ForceResponseException $er )
        {
            /**
             * @var RedirectResponse $response
             */
            $response = $er->getResponse();
            $this->assertEquals("/accounts/", $response->getUrl());
        }

        $this->logout();
    }

    public function testRedirectsWhileSelectsSingleAccount()
    {
        $this->loginWithSingleAccounts();

        try
        {
            new SaasNoTenantSelectedException();
        }
        catch( ForceResponseException $er )
        {
            /**
             * @var RedirectResponse $response
             */
            $response = $er->getResponse();

            $this->assertEquals("/app/", $response->getUrl(), "If a user has just 1 account we should be taken to the" .
                " /app/ logged into that account.");

            $accountSession = new AccountSession();

            $this->assertEquals( $this->protonWelding->UniqueIdentifier, $accountSession->AccountID, "If a user has just".
                " 1 account we should be automatically connected to it" );
        }
    }
}
