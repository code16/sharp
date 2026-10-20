<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DecoderInterface;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Thin wrapper around Intervention's ImageManager, compatible with both v3 and v4
 * (which renamed read() to decode() and create() to createImage()).
 *
 * @internal
 */
class SharpImageManager
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(sharp()->config()->get('uploads.image_driver'));
    }

    public function create(int $width, int $height): ImageInterface
    {
        return method_exists($this->manager, 'createImage')
            ? $this->manager->createImage($width, $height)
            : $this->manager->create($width, $height);
    }

    public function read(mixed $input, array|string|DecoderInterface|null $decoders = null): ImageInterface
    {
        return method_exists($this->manager, 'decode')
            ? $this->manager->decode($input, $decoders)
            : $this->manager->read($input, $decoders ?? []);
    }

    public function driver(): DriverInterface
    {
        return method_exists($this->manager, 'driver')
            ? $this->manager->driver()
            : $this->manager->driver;
    }
}
