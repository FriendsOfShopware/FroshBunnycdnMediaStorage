<?php

namespace FroshBunnycdnMediaStorage\Components\Thumbnail;

use Shopware\Bundle\MediaBundle\MediaServiceInterface;
use Shopware\Components\Thumbnail\Generator\GeneratorInterface;

class Manager extends \Shopware\Components\Thumbnail\Manager
{
    /**
     * This generator will be used for the thumbnail creation itself
     *
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var \Enlight_Event_EventManager
     */
    protected $eventManager;

    /**
     * @var MediaServiceInterface
     */
    private $mediaService;

    private $shouldRun = true;

    /**
     * {@inheritdoc}
     */
    public function __construct(GeneratorInterface $generator, $rootDir, \Enlight_Event_EventManager $eventManager, MediaServiceInterface $mediaService)
    {
        $this->generator = $generator;
        $this->rootDir = $rootDir;
        $this->eventManager = $eventManager;
        $this->mediaService = $mediaService;

        if (!Shopware()->Container()->get('frosh_bunnycdn_media_storage.config')['ManipulationEngine']) {
            $this->shouldRun = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaThumbnails($name, $type, $extension, array $sizes)
    {
        if (!$this->shouldRun) {
            return parent::getMediaThumbnails($name, $type, $extension, $sizes);
        }

        $sizes = $this->uniformThumbnailSizes($sizes);
        $path = 'media/' . strtolower($type) . '/';

        $thumbnails = [];
        foreach ($sizes as $size) {
            $thumbnails[] = [
                'maxWidth' => $size['width'],
                'maxHeight' => $size['height'],
                'source' => $this->mediaService->encode($path . $name . '.' . $extension) . '?width=' . $size['width'] . '&height=' . $size['height'],
                'retinaSource' => $this->mediaService->encode($path . $name . '.' . $extension) . '?width=' . ($size['width'] * 2) . '&height=' . ($size['height'] * 2),
            ];
        }

        return $thumbnails;
    }
}
