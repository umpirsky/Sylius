<?php

namespace Sylius\Bundle\CoreBundle\Uploader;

use PunkAve\FileUploaderBundle\Services\FileManager as BaseFileManager;
use Sylius\Bundle\CoreBundle\Model\VariantImage;
use Symfony\Component\HttpFoundation\File\File;

class FileManager extends BaseFileManager
{
    public function syncFiles($options = array())
    {
        $options = array_merge($this->options, $options);
        $options['from_folder'] = $options['from_folder'].'/originals';

        // We're syncing and potentially deleting folders, so make sure
        // we were passed something - make it a little harder to accidentally
        // trash your site
        if (!strlen(trim($options['file_base_path'])))
        {
            throw \Exception("file_base_path option looks empty, bailing out");
        }
        if (!strlen(trim($options['from_folder'])))
        {
            throw \Exception("from_folder option looks empty, bailing out");
        }

        $from = $options['file_base_path'] . '/' . $options['from_folder'];
        if (file_exists($from))
        {
            $this->collectImages($from, $options['resource']);

            if (isset($options['remove_from_folder']) && $options['remove_from_folder'])
            {
                $this->cleanupFiles($options['from_folder']);
            }
        }
        else
        {
            // A missing from_folder is not an error. This is commonly the case
            // when syncing from something that has nothing attached to it yet, etc.
        }
    }

    public function cleanupFiles($path)
    {
        system("rm -rf " . escapeshellarg($this->options['file_base_path'].'/'.$path));
    }

    private function collectImages($path, $resource)
    {
        foreach (scandir($path) as $filename) {
            $filepath = $path.'/'.$filename;
            if (!is_file($filepath)) {
                continue;
            }

            $image = new VariantImage();
            $image->setFile(new File($filepath));
            $resource->addImage($image);
        }
    }
}
