<?php

namespace App\Service;

use Exception;
use Slim\Http\UploadedFile;

class Uploader
{
    /**
     * @throws Exception
     */
    public function upload(string $directory, UploadedFile $file): string
    {
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $file->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}