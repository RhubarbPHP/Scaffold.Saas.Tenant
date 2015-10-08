<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Model;

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
        $schema->removeColumnByName("Token");
        $schema->removeColumnByName("Password");
        $schema->removeColumnByName("TokenExpiry");
        $schema->removeColumnByName("PasswordResetHash");
        $schema->removeColumnByName("PasswordResetDate");

        parent::extendSchema($schema);
    }
}