<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Model;

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
}