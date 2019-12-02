<?php

namespace FroshBunnycdnMediaStorage\Components\CompilerPass;

use FroshBunnycdnMediaStorage\Components\Thumbnail\Manager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetCustomThumbnailManager implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('thumbnail_manager')->setClass(Manager::class);
    }
}
