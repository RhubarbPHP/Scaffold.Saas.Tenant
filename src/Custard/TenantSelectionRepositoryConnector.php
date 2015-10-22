<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Custard;

use Rhubarb\Crown\Context;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Stem\Custard\RepositoryConnectorInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class TenantSelectionRepositoryConnector implements RepositoryConnectorInterface
{
    const SETTINGS_PATH = "tenant-context.json";

    function interact(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $settings = false;

        if (file_exists(self::SETTINGS_PATH)){
            $settings = json_decode(file_get_contents(self::SETTINGS_PATH), true);
        }

        if ( !$settings ){
            $settings = [ "username" => "", "password" => "", "account" => "" ];
        }

        $default = ($settings["username"]) ? '('.$settings["username"].') ' : '';
        $question = new Question("<question>No tenant is selected. Enter a username:</question> ".$default, $settings[ "username"] );

        $username = $helper->ask($input, $output, $question);

        $default = ($settings["password"]) ? '('.$settings["password"].') ' : '';
        $question = new Question("<question>Enter a password:</question> ".$default, $settings[ "password"] );

        $password = $helper->ask($input, $output, $question);

        $tenantLoginProvider = new TenantLoginProvider();

        if ( !$tenantLoginProvider->login($username, $password) ) {
            $output->writeln("<error>The login credentials were rejected.</error>");
            throw new \Exception();
        }

        $accounts = Me::getAccounts();

        $accountsStrings = [];

        foreach($accounts as $account){
            $accountsStrings[] = $account->_id;
        }

        $default = ($settings["account"]) ? '('.$settings["account"].') ' : '';
        $question = new ChoiceQuestion( "<question>Select an account:</question> ".$default, $accountsStrings, $settings[ "account"] );

        $account = $helper->ask($input, $output, $question);

        if ( !$account ){
            $output->writeln("<error>No account? Really? You expect me to just do this by magic?</error>");
            throw new \Exception();
        }

        $session = new AccountSession();
        $session->connectToAccount($account);

        $context = new Context();

        if ( $context->DeveloperMode ) {
            $output->writeln( "Storing settings in ".realpath(self::SETTINGS_PATH) );

            // If we get this far we should update our default values to make it faster next time.
            $settings = ["username" => $username, "password" => $password, "account" => $account];

            file_put_contents(self::SETTINGS_PATH, json_encode($settings, JSON_PRETTY_PRINT));
        }
    }
}