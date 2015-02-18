<?php


namespace Rhubarb\Scaffolds\Saas\Tenant\Tests\UrlHandlers;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Scaffolds\Saas\Tenant\Tests\Fixtures\TenantTestCase;
use Rhubarb\Scaffolds\Saas\Tenant\UrlHandlers\ValidateTenantConnectedUrlHandler;

class ValidateTenantConnectedUrlHandlerTest extends TenantTestCase
{
    public function testRedirectOccurs()
    {
        $this->loginWithMultipleAccounts();

        $handler = new ValidateTenantConnectedUrlHandler();

        try {
            $handler->generateResponse(new WebRequest());

            $this->fail("Not being connected to a tenant should have failed causing a redirect");
        } catch (ForceResponseException $er) {
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

        $handler = new ValidateTenantConnectedUrlHandler();

        try {
            $handler->generateResponse(new WebRequest());
        } catch (ForceResponseException $er) {
            /**
             * @var RedirectResponse $response
             */
            $response = $er->getResponse();

            $this->assertEquals("/app/", $response->getUrl(), "If a user has just 1 account we should be taken to the" .
                " /app/ logged into that account.");

            $accountSession = new AccountSession();

            $this->assertEquals($this->protonWelding->UniqueIdentifier, $accountSession->AccountID, "If a user has just" .
                " 1 account we should be automatically connected to it");
        }
    }

    public function testConnectedUserDoesntRedirect()
    {
        $this->loginWithSingleAccounts();

        $session = new AccountSession();
        $session->connectToAccount( $this->protonWelding->UniqueIdentifier );

        $handler = new ValidateTenantConnectedUrlHandler();

        try {
            $handler->generateResponse(new WebRequest());
        } catch (ForceResponseException $er) {
            $this->fail("A user connected to a tenant should not be redirected.");
        }
    }
}
