<?php

namespace FroshBunnycdnMediaStorage\Subscriber;

use Doctrine\Common\Cache\FilesystemCache;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use FroshBunnycdnMediaStorage\Components\BunnyCDNAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Adapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use League\Flysystem\Filesystem;

class AdapterCollectionSubscriber implements SubscriberInterface
{
    private $container;
    private $cache;

    public function __construct(ContainerInterface $container, FilesystemCache $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Collect_MediaAdapter_bunnycdn' => 'createBunnyCDNAdapter',
        ];
    }

    /**
     * Creates adapter instance
     *
     * @param Enlight_Event_EventArgs $args
     *
     * @return CachedAdapter
     */
    public function createBunnyCDNAdapter(Enlight_Event_EventArgs $args)
    {
        $defaultConfig = ['migration' => false];
        $config = $args->get('config');

        // Create the adapter
        $mainAdapter = new BunnyCDNAdapter(array_merge($config, $defaultConfig), $this->cache, $this->container);

        $local = new Local($this->container->getParameter('frosh_bunnycdn_media_storage.cache_dir'));

        /*
         * REALLY?! one file?! 😡
         */
        $cacheStore = new Adapter($local, 'file', null);

        return new CachedAdapter($mainAdapter, $cacheStore);
    }
}
