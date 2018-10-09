<?php

namespace TinectMediaBunnycdn\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TinectMediaBunnycdn\Components\BunnyCDNAdapter;

class AdapterCollectionSubscriber implements SubscriberInterface
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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

        $cacheDir = $this->container->getParameter('kernel.cache_dir') . '/bunnycdn/';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir);
        }

        $cacheOptions = ['cache_dir' => $cacheDir, 'automatic_serialization' => true, 'lifetime' => null];

        $cache = $this->container->get('cache_factory')->factory(
            'file', $cacheOptions, $cacheOptions, $this->container->get('shopware.release')
        );


        return new BunnyCDNAdapter(array_merge($config,$defaultConfig), $cache, $this->container);
    }
}
