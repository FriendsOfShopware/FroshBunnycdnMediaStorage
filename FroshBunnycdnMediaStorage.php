<?php

namespace FroshBunnycdnMediaStorage;

use Exception;
use FroshBunnycdnMediaStorage\Components\CompilerPass\SetCustomThumbnailManager;
use Shopware\Components\Plugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class FroshBunnycdnMediaStorage extends Plugin
{
    /**
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new SetCustomThumbnailManager());
    }
}
