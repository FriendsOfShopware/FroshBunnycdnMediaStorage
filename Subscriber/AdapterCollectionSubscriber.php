<?php

namespace TinectMediaBunnycdn\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TinectMediaBunnycdn\Components\BunnyCDNAdapter;

class AdapterCollectionSubscriber implements SubscriberInterface
{

    private $cache;
    private $container;

    public function __construct(\Zend_Cache_Core $cache, ContainerInterface $container)
    {
        $this->cache = $cache;
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
     */
    public function createBunnyCDNAdapter(Enlight_Event_EventArgs $args)
    {
        $defaultConfig = ['migration' => false];
        $config = $args->get('config');

        return new BunnyCDNAdapter(array_merge($config,$defaultConfig), $this->cache, $this->container);
    }
}
