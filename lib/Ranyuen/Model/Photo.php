<?php
/**
 * Ranyuen web site
 */
namespace Ranyuen\Model;

use \Illuminate\Database\Eloquent;

/**
 * Photo model.
 */
class Photo extends Eloquent\Model
{
    protected $table = 'photo';

    /** @var resource */
    private $image;

    /**
     * @return Photo
     */
    public function loadImageSize()
    {
        if (!$this->width || !$this->height) {
            $file = "images/gallery/$this->id.jpg";
            list($this->width, $this->height) = getimagesize($file);
        }

        return $this;
    }

    /**
     * @return Photo
     */
    public function loadImage()
    {
        if (!$this->image) {
            $file = "images/gallery/$this->id.jpg";
            $this->image = imagecreatefromjpeg($file);
            $this->loadImageSize();
        }

        return $this;
    }

    /**
     * @param integer $newWidth  New image width px
     * @param integer $newHeight New image height px
     *
     * @return void
     */
    public function renderResized($newWidth, $newHeight)
    {
        $cacheFilename = "images/cache/{$this->id}_{$newWidth}x$newHeight.jpg";
        $this->deleteOldCache();
        if (!file_exists($cacheFilename)) {
            $this->loadImage();
            $this->createCache($newWidth, $newHeight, $cacheFilename);
        }
        touch($cacheFilename);
        header('Content-Type: image/jpeg');
        header('Content-Length: '.filesize($cacheFilename));
        ob_clean();
        flush();
        readfile($cacheFilename);
    }

    /**
     * @param integer $newWidth      New image width px
     * @param integer $newHeight     New image height px
     * @param string  $cacheFilename Cache file name
     *
     * @return void
     */
    private function createCache($newWidth, $newHeight, $cacheFilename)
    {
        $origFilename = "images/gallery/$this->id.jpg";
        $origImage = imagecreatefromjpeg($origFilename);
        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($image, $origImage, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        imagedestroy($origImage);
        imageinterlace($image, 1);
        imagejpeg($image, $cacheFilename, 95);
        imagedestroy($image);
    }

    private function deleteOldCache()
    {
        $cacheDir = 'images/cache';
        // $isDirTooLarge = function () use ($cacheDir) {
        //     return preg_match('/\A[0-9]+G/', exec("du -h $cacheDir"));
        // };
        if ($dir = opendir($cacheDir)) {
            while (($file = readdir($dir)) !== false) {
                if (is_file($file)
                    && $file[0] !== '.'
                    && filemtime($file) < time() - 60 * 60 * 24 * 7
                ) {
                    unlink("$cacheDir/$file");
                }
            }
            closedir($dir);
        }
        // if ($isDirTooLarge()) {
        //     $filenames = glob("$cacheDir/*.*");
        //     $filesizes = array_map(
        //         function ($filename) { return filesize($filename); },
        //         $filenames);
        //     array_multisort($filenames,
        //         $filesizes, SORT_ASC);
        //     while ($isDirTooLarge() && $filenames) {
        //         unlink(array_pop($filenames));
        //     }
        // }
    }
}
