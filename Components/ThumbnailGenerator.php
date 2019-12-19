<?php

namespace FroshBunnycdnMediaStorage\Components;

use Shopware\Bundle\MediaBundle\MediaServiceInterface;
use Shopware\Components\Thumbnail\Generator\GeneratorInterface;

class ThumbnailGenerator implements GeneratorInterface
{
    private $parentGenerator;
    private $mediaService;
    private $shouldRun = true;

    public function __construct(array $config, MediaServiceInterface $mediaService, GeneratorInterface $parentGenerator)
    {
        $this->parentGenerator = $parentGenerator;
        $this->mediaService = $mediaService;
        if (!$config['ManipulationEngine']) {
            $this->shouldRun = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createThumbnail($image, $destination, $maxWidth, $maxHeight, $keepProportions = false, $quality = 90)
    {
        if (!$this->shouldRun) {
            $this->parentGenerator->createThumbnail($image, $destination, $maxWidth, $maxHeight, $keepProportions, $quality);

            return;
        }

        /*
        * We'll need just 140x140 thumbs for preview in mediamanager.
        * Waiting for https://github.com/shopware/shopware/pull/2267 to get merged!
        */
        if ((int) $maxWidth === 140) {
            $this->parentGenerator->createThumbnail($image, $destination, $maxWidth, $maxHeight, $keepProportions, $quality);
        } else {
            //remove old thumbnails from bunnyCDN
            if (strpos($destination, '?') !== false) {
                return;
            }
            if ($this->mediaService->has($destination)) {
                $this->mediaService->delete($destination);
            }
        }
    }
}
