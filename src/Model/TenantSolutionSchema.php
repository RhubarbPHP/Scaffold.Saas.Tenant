<?php

namespace Rhubarb\Scaffolds\Saas\Tenant\Model;

use Rhubarb\Stem\Schema\SolutionSchema;

class TenantSolutionSchema extends SolutionSchema
{
    public function __construct($version = 0.1)
    {
        parent::__construct($version);

        $this->addModel(
            "User", User::class
        );
    }
}