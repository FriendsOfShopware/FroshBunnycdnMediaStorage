<?php

namespace TinectMediaBunnycdn\Subscriber;

use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;
use Shopware\Components\HttpClient\GuzzleFactory;
use TinectMediaBunnycdn\Components\BunnyCDNAdapter;
use League\Flysystem\AdapterInterface;

class AdapterCollectionSubscriber implements SubscriberInterface
{

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

        return new BunnyCDNAdapter(array_merge($config,$defaultConfig));
    }
}
