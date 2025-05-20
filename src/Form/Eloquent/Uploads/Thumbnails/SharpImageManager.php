<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\DecoderInterface;
use Intervention\Image\Interfaces\DriverInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;

class SharpImageManager implements ImageManagerInterface
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(sharp()->config()->get('uploads.image_driver'));
    }

    public function create(int $width, int $height): ImageInterface
    {
        return $this->manager->create($width, $height);
    }

    public function read(mixed $input, array|string|DecoderInterface $decoders = []): ImageInterface
    {
        return $this->manager->read($input, $decoders);
    }

    public function animate(callable $init): ImageInterface
    {
        return $this->manager->animate($init);
    }

    public function driver(): DriverInterface
    {
        return $this->manager->driver();
    }
}
