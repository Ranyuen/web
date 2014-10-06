<?php
namespace Ranyuen\Model;

use \Illuminate\Database\Eloquent;

class Photo extends Eloquent\Model
{
    protected $table = 'photo';

    /** @type resource */
    private $_image;

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
        if (!$this->_image) {
            $file = "images/gallery/$this->id.jpg";
            $this->_image = imagecreatefromjpeg($file);
            $this->loadImageSize();
        }

        return $this;
    }

    /**
     * @param integer $new_width
     * @param integer $new_height
     */
    public function renderResized($new_width, $new_height)
    {
        $cache_filename = "images/cache/{$this->id}_{$new_width}x$new_height.jpg";
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

    /**
     * @param integer $new_width
     * @param integer $new_height
     * @param string  $cache_filename
     */
    private function createCache($new_width, $new_height, $cache_filename)
    {
        $orig_filename = "images/gallery/$this->id.jpg";
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
        $cache_dir = 'images/cache';
        // $is_dir_too_large = function () use ($cache_dir) {
        //     return preg_match('/\A[0-9]+G/', exec("du -h $cache_dir"));
        // };
        if ($dir = opendir($cache_dir)) {
            while (($file = readdir($dir)) !== false) {
                if (is_file($file) &&
                    $file[0] !== '.' &&
                    filemtime($file) < time() - 60 * 60 * 24 * 7) {
                    unlink("$cache_dir/$file");
                }
            }
            closedir($dir);
        }
        // if ($is_dir_too_large()) {
        //     $filenames = glob("$cache_dir/*.*");
        //     $filesizes = array_map(
        //         function ($filename) { return filesize($filename); },
        //         $filenames);
        //     array_multisort($filenames,
        //         $filesizes, SORT_ASC);
        //     while ($is_dir_too_large() && $filenames) {
        //         unlink(array_pop($filenames));
        //     }
        // }
    }
}
