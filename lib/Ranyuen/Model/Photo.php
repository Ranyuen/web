<?php
namespace Ranyuen\Model;

use \Model;

class Photo extends Model
{
    public static $_table = 'photo';

    public $width;
    public $height;
    private $image;

    public function loadImageSize()
    {
        if (!$this->width || !$this->height) {
            $file = "Calanthe/gallery/$this->id.jpg";
            list($this->width, $this->height) = getimagesize($file);
        }

        return $this;
    }

    public function loadImage()
    {
        if (!$this->image) {
            $file = "Calanthe/gallery/$this->id.jpg";
            $this->image = imagecreatefromjpeg($file);
            $this->loadImageSize();
        }

        return $this;
    }

    public function renderResized($new_width, $new_height)
    {
        $cache_filename = "Calanthe/cache/{$this->id}_{$new_width}x$new_height.jpg";
        $this->deleteOldCache();
        if (!file_exists($cache_filename)) {
            $this->loadImage();
            $this->createCache($new_width, $new_height, $cache_filename);
        }
        touch($cache_filename);
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($cache_filename));
        ob_clean();
        flush();
        readfile($cache_filename);
    }

    private function createCache($new_width, $new_height, $cache_filename)
    {
        $orig_filename = "Calanthe/gallery/$this->id.jpg";
        $orig_image = imagecreatefromjpeg($orig_filename);
        $image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($image, $orig_image,
            0, 0, 0, 0,
            $new_width, $new_height, $this->width, $this->height);
        imagedestroy($orig_image);
        imageinterlace($image, 1);
        imagejpeg($image, $cache_filename, 95);
        imagedestroy($image);
    }

    private function deleteOldCache()
    {
        // try {
        //     $cache_dir = 'Calanthe/cache';
        //     $is_dir_too_large = function () use ($cache_dir) {
        //         return preg_match('/\A[0-9]+G/', exec("du -h $cache_dir"));
        //     };
        //     if ($dir = opendir($cache_dir)) {
        //         while (($file = readdir($dir)) !== false) {
        //             if ($file[0] !== '.' &&
        //                 filemtime($file) < time() - 60 * 60 * 24 * 7) {
        //                 unlink("$cache_dir/$file");
        //             }
        //         }
        //         closedir($dir);
        //     }
        //     if ($is_dir_too_large()) {
        //         $filenames = glob("$cache_dir/*.*");
        //         $filesizes = array_map(
        //             function ($filename) { return filesize($filename); },
        //             $filenames);
        //         array_multisort($filenames,
        //             $filesizes, SORT_ASC);
        //         while ($is_dir_too_large() && $filenames) {
        //             unlink(array_pop($filenames));
        //         }
        //     }
        // } catch (Exception $ex) {
        // }
    }
}
