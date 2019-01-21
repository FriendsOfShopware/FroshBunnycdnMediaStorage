<?php

namespace FroshBunnycdnMediaStorage\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Doctrine\Common\Cache\FilesystemCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use FroshBunnycdnMediaStorage\Components\BunnyCDNAdapter;

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
            'Shopware_Collect_MediaAdapter_bunnycdn' => 'createBunnyCDNAdapter'
        ];
    }

    /**
     * Creates adapter instance
     *
     * @param Enlight_Event_EventArgs $args
     * @return BunnyCDNAdapter
     * @throws \Zend_Cache_Exception
     */
    public function createBunnyCDNAdapter(Enlight_Event_EventArgs $args)
    {
        $defaultConfig = ['migration' => false];
        $config = $args->get('config');

        return new BunnyCDNAdapter(array_merge($config,$defaultConfig), $this->cache, $this->container);
    }
}
