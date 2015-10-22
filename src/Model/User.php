<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Model;

use Rhubarb\Crown\LoginProviders\Exceptions\NotLoggedInException;
use Rhubarb\Scaffolds\Authentication\LoginProvider;
use Rhubarb\Scaffolds\Saas\Tenant\RestClients\SaasGateway;
use Rhubarb\Stem\Filters\Equals;
use Rhubarb\Stem\Schema\Columns\UUID;
use Rhubarb\Stem\Schema\ModelSchema;

class User extends \Rhubarb\Scaffolds\AuthenticationWithRoles\User
{
    protected function getConsistencyValidationErrors()
    {
        // Tenant users are normally created as a placeholder to allow in-db joins for reporting etc.
        // As such usually they don't have a username or password and so we need to disable that
        // validation
        return [];
    }

    protected function extendSchema(ModelSchema $schema)
    {
        parent::extendSchema($schema);

        $schema->removeColumnByName("Token");
        $schema->removeColumnByName("Password");
        $schema->removeColumnByName("TokenExpiry");
        $schema->removeColumnByName("PasswordResetHash");
        $schema->removeColumnByName("PasswordResetDate");
        $schema->addColumn(new UUID());
    }

    /**
     * @param string $uuid
     * @return \Rhubarb\Stem\Models\Model|static
     * @throws \Rhubarb\Stem\Exceptions\RecordNotFoundException
     */
    public function findByUUID( $uuid )
    {
        return static::findFirst( new Equals( 'UUID', $uuid ) );
    }

    protected function beforeSave()
    {
        parent::beforeSave();

        // Sync changes to "me" with the landlord
        try {
            $loginProvider = LoginProvider::getDefaultLoginProvider();
            $currentUser = $loginProvider::getLoggedInUser();

            // if the current user is editing themselves
            if (!$this->isNewRecord() && $currentUser->UserID == $this->UserID) {
                $changes = $this->getModelChanges();
                $numChanges = count($changes);
                // So long as the only change ISN'T role (which should not be synced to the landlord)
                if ($numChanges > 0 && !($numChanges === 1 && isset($changes['RoleID']))) {
                    SaasGateway::updateMe($this);
                }
            }
        } catch (NotLoggedInException $ex) {
            // Probably a system change (triggered by landlord call), these won't get propagated to the landlord
        }
    }

    protected function getPublicPropertyList()
    {
        return [
            'Forename',
            'Surname',
            'Username',
            'Email',
            'RoleID'
        ];
    }

}