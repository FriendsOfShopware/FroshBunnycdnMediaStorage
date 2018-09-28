<?php

namespace TinectMediaBunnycdn\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use TinectMediaBunnycdn\Components\BunnyCDNAdapter;

class AdapterCollectionSubscriber implements SubscriberInterface
{

    private $cache;

    public function __construct(\Zend_Cache_Core $cache)
    {
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
     */
    public function createBunnyCDNAdapter(Enlight_Event_EventArgs $args)
    {
        $defaultConfig = ['migration' => false];
        $config = $args->get('config');

        return new BunnyCDNAdapter(array_merge($config,$defaultConfig), $this->cache);
    }
}
