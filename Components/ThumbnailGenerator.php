<?php

namespace FroshBunnycdnMediaStorage\Components;

use Shopware\Components\Thumbnail\Generator\GeneratorInterface;

class ThumbnailGenerator implements GeneratorInterface
{
    private $parentGenerator;
    private $shouldRun = true;

    public function __construct(array $config, GeneratorInterface $parentGenerator)
    {
        $this->parentGenerator = $parentGenerator;
        if (!$config['ManipulationEngine']) {
            $this->shouldRun = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnail($image, $destination, $maxWidth, $maxHeight, $keepProportions = false, $quality = 90)
    {
        /*
        * We'll need just 140x140 thumbs for preview in mediamanager.
        * Waiting for https://github.com/shopware/shopware/pull/2267 to get merged!
        *
        * There is a bug, which results in long delay by deleting media,
        * cause initializing MediaModel will result in creating thumbnails while they aren't on space!
        */
        if (!$this->shouldRun || (int) $maxWidth === 140) {
            $this->parentGenerator->createThumbnail($image, $destination, $maxWidth, $maxHeight, $keepProportions, $quality);
        }
    }
}
