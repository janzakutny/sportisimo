<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Security\Permission;

class AuthorizatorFactory
{
    /** @var array of entity autohrization asserts */
    private $assertions = [];

    /** @var Authorizator */
    private $auth;


    public function __construct(
        Authorizator $auth,
    ) {
        $this->auth = $auth;
    }


    public function setupRules(array $aclConfig)
    {
        foreach ($aclConfig['roles'] as $role => $parentRoles) {
            $this->auth->addRole($role, !empty($parentRoles) ? $parentRoles : null);
        }

        foreach ($aclConfig['resources'] as $resource) {
            $this->auth->addResource($resource, null);
        }

        foreach ($aclConfig['rules'] as $rule) {
            $this->auth->allow($rule['role'], $rule['resource'], $rule['privilege']);
        }
    }


    /**
     * Get callback for entity autohorization
     * @param string|Permission::ALL $resource string pattern EntityName:Entity
     * @param string|Permission::ALL $role
     */
    private function getAssertion(?string $resource, ?string $role)
    {
        $parts = explode(':', $resource);
        if (
            count($parts) !== 2 || strtolower($parts[1]) !== 'entity' ||
            !isset($this->assertions[strtolower($parts[0])])
        ) {
            return null;
        }

        return [$this->assertions[strtolower($parts[0])], 'assert'];
    }
}
