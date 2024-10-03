<?php

namespace App\Image\Application\Port;

interface ImageStoragePort
{
    public function upload($file): string;
}
