<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen\Model;

use Illuminate\Database\Eloquent;

/**
 * Photo model.
 */
class Photo extends Eloquent\Model
{
    public static function getPhotosBySpeciesName($speciesName, $offset = 0, $limit = 150gsc)
    {
        switch ($speciesName) {
            case 'all':
                return self::skip($offset)->take($limit)->get();
            case 'others':
                return self::whereNull('species_name')
                    ->skip($offset)
                    ->orderByRaw('RAND()')
                    ->take($limit)
                    ->get();
            default:
                return self::whereRaw('LOWER(species_name) LIKE ?', ['%'.strtolower($speciesName).'%'])
                    ->skip($offset)
                    ->orderByRaw('RAND()')
                    ->take($limit)
                    ->get();
        }
    }

    public static function getRandomPhotos($offset = 0, $limit = 100)
    {
        switch ((new self())->getConnection()->getConfig('driver')) {
            case 'sqlite':
                return self::orderByRaw('RANDOM()')->take($limit)->get();
            case 'mysql':
                return self::orderByRaw('RAND()')->take($limit)->get();
            default:
                return self::skip($offset)->take($limit)->get();
        }
    }

    protected $table = 'photo';

    /**
     * Image file.
     *
     * @var resource
     */
    private $image;
    /**
     * Image file path.
     *
     * @var string
     */
    private $path;

    /**
     * Search image file.
     *
     * @return string When not found, returns empty string.
     */
    public function getPath()
    {
        if (!is_null($this->path)) {
            return $this->path;
        }
        $path = '';
        $dir = opendir('images/');
        while (false !== ($entry = readdir($dir))) {
            if (!is_dir("images/$entry") || '.' === $entry[0]) {
                continue;
            }
            foreach (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'] as $ext) {
                if (is_file("images/$entry/$this->id.$ext")) {
                    $path = "images/$entry/$this->id.$ext";
                    break;
                }
            }
            if ($path) {
                break;
            }
        }
        closedir($dir);
        $this->path = $path;

        return $path;
    }

    /**
     * Get width and height from the file.
     *
     * @return this
     */
    public function loadImageSize()
    {
        $path = $this->getPath();
        if ($path && !$this->width || !$this->height) {
            list($this->width, $this->height) = getimagesize($path);
        }

        return $this;
    }

    /**
     * Search and load image file.
     *
     * @return this
     */
    public function loadImage()
    {
        $path = $this->getPath();
        if ($path && !$this->image) {
            $this->image = imagecreatefromjpeg($this->getPath());
            $this->loadImageSize();
        }

        return $this;
    }

    /**
     * Echo resized image.
     *
     * @param int $newWidth  New image width px.
     * @param int $newHeight New image height px.
     *
     * @return void
     */
    public function renderResized($newWidth, $newHeight)
    {
        $cacheFilename = "images/.cache/{$this->id}_{$newWidth}x$newHeight.jpg";
        $this->deleteOldCache();
        if (!file_exists($cacheFilename)) {
            $this->loadImage();
            $this->createCache($newWidth, $newHeight, $cacheFilename);
        }
        touch($cacheFilename);
        header('Content-Type: image/jpeg');
        header('Content-Length: '.filesize($cacheFilename));
        // ob_clean();
        flush();
        readfile($cacheFilename);
    }

    /**
     * Create resized cache.
     *
     * @param int    $newWidth      New image width px.
     * @param int    $newHeight     New image height px.
     * @param string $cacheFilename Cache file name.
     *
     * @return void
     */
    private function createCache($newWidth, $newHeight, $cacheFilename)
    {
        $origImage = imagecreatefromjpeg($this->getPath());
        $image = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($image, $origImage, 0, 0, 0, 0, $newWidth, $newHeight, $this->width, $this->height);
        imagedestroy($origImage);
        imageinterlace($image, 1);
        imagejpeg($image, $cacheFilename, 95);
        imagedestroy($image);
    }

    private function deleteOldCache()
    {
        $cacheDir = 'images/.cache';
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
