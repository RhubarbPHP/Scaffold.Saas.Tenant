<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Custard;

use Rhubarb\Crown\Context;
use Rhubarb\Scaffolds\Saas\Tenant\LoginProviders\TenantLoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestModels\Me;
use Rhubarb\Scaffolds\Saas\Tenant\Sessions\AccountSession;
use Rhubarb\Stem\Custard\RepositoryConnectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class TenantSelectionRepositoryConnector implements RepositoryConnectorInterface
{
    const SETTINGS_PATH = "tenant-context.json";

    private $username;
    private $password;
    private $selectedAccount;

    function interact(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $settings = false;

        if (file_exists(self::SETTINGS_PATH)){
            $settings = json_decode(file_get_contents(self::SETTINGS_PATH), true);
        }

        if ( !$settings ){
            $settings = [ "username" => "", "password" => "", "account" => "" ];
        }

        $this->username = $input->getOption("username");
        $this->password = $input->getOption("password");
        $this->selectedAccount = $input->getOption("account");

        if (!$this->username) {
            $default = ($settings["username"]) ? '(' . $settings["username"] . ') ' : '';
            $question = new Question("<question>No tenant is selected. Enter a username:</question> " . $default, $settings["username"]);
            $this->username = $helper->ask($input, $output, $question);
        }

        if (!$this->password) {
            $default = ($settings["password"]) ? '(' . $settings["password"] . ') ' : '';
            $question = new Question("<question>Enter a password:</question> " . $default, $settings["password"]);
            $this->password = $helper->ask($input, $output, $question);
        }

        $tenantLoginProvider = new TenantLoginProvider();

        if ( !$tenantLoginProvider->login($this->username, $this->password) ) {
            $output->writeln("<error>The login credentials were rejected.</error>");
            throw new \Exception();
        }

        $accounts = Me::getAccounts();

        $accountsStrings = [];

        foreach($accounts as $account){
            $accountsStrings[] = $account->_id;
        }

        if (!$this->selectedAccount) {
            $default = ($settings["account"]) ? '(' . $settings["account"] . ') ' : '';
            $question = new ChoiceQuestion("<question>Select an account:</question> " . $default, $accountsStrings, $settings["account"]);

            $this->selectedAccount = $helper->ask($input, $output, $question);
        }

        if ( !$this->selectedAccount ){
            $output->writeln("<error>No account? Really? You expect me to just do this by magic?</error>");
            throw new \Exception();
        }
    }

    public function configure(Command $command)
    {
        $command->addOption("username", "u", InputOption::VALUE_OPTIONAL, "The username for the tenant login");
        $command->addOption("password", "p", InputOption::VALUE_OPTIONAL, "The password for the tenant login");
        $command->addOption("account", "a", InputOption::VALUE_OPTIONAL, "The account id from the list of available accounts");
    }

    /**
     * Called when all params should be on the command line for scenarios where there is no command line.
     * @param InputInterface $input
     * @return mixed
     * @throws \Exception
     * @throws \Rhubarb\Crown\LoginProviders\Exceptions\LoginFailedException
     */
    public function connect(InputInterface $input)
    {
        $usernameSwitch = $input->getOption("username");
        $passwordSwitch = $input->getOption("password");
        $selectedAccountSwitch = $input->getOption("account");

        $this->username = ($usernameSwitch) ? $usernameSwitch : $this->username;
        $this->password = ($passwordSwitch) ? $passwordSwitch : $this->password;
        $this->selectedAccount = ($selectedAccountSwitch) ? $selectedAccountSwitch : $this->selectedAccount;

        $tenantLoginProvider = new TenantLoginProvider();

        if ( !$tenantLoginProvider->login($this->username, $this->password) ) {
            throw new \Exception();
        }

        $session = new AccountSession();
        $session->connectToAccount($this->selectedAccount);

        $context = new Context();

        if ( $context->DeveloperMode ) {
            // If we get this far we should update our default values to make it faster next time.
            $settings = ["username" => $this->username, "password" => $this->password, "account" => $this->selectedAccount];

            file_put_contents(self::SETTINGS_PATH, json_encode($settings, JSON_PRETTY_PRINT));
        }
    }
}