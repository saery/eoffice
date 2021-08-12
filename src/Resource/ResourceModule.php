<?php

namespace EOffice\Resource;

use EOffice\Contracts\Support\ModuleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourceModule implements ModuleInterface
{
    public function build(ContainerBuilder $builder): void
    {
        // TODO: Implement build() method.
    }

    public function getName(): string
    {
        return "resource";
    }

    public function getBaseDir(): string
    {
        return realpath(__DIR__);
    }
}
