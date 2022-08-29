<?php

declare(strict_types=1);

namespace App\Model;

use Nette;
use Nette\DI\Definitions\Definition;
use Nette\PhpGenerator\ClassType;

class AuthExtension extends Nette\DI\CompilerExtension
{
    /** @var Definition */
    private $definition;


    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();
        $acl = $builder->parameters['acl'];
        $this->definition = $builder->addDefinition($this->prefix('authorizatorFactory'))
            ->setType(AuthorizatorFactory::class)
            ->addSetup('setupRules', [$acl]);
    }


    public function afterCompile(ClassType $class)
    {
        parent::afterCompile($class);
        $class->getMethod('initialize')->addBody('$this->getService(?);', [$this->definition->getName()]);
    }
}
