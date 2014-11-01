<?php
/**
 * Backgroud image selector.
 */
namespace Ranyuen;

/**
 * Backgroud image selector.
 */
class BgImage
{
    private $resources = [];

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function __construct()
    {
        if (isset($_SESSION['bgimage'])) {
            $bgimage = $_SESSION['bgimage'];
            if ($bgimage['expiration'] >= time()) {
                $this->resources = [$bgimage['img']];

                return $this;
            }
            unset($_SESSION['bgimage']);
        }
        $this->resources = $this->getAvailableImages();
    }

    /**
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getRandom()
    {
        $image = $this->resources[array_rand($this->resources)];
        $_SESSION['bgimage'] = [
            'img' => $image,
            'expiration' => isset($_SESSION['bgimage']['expiration']) ?
                $_SESSION['bgimage']['expiration'] :
                time() + 3600,
        ];

        return $image;
    }

    /**
     * @return string[]
     */
    private function getAvailableImages()
    {
        $files = scandir('assets/images/backgrounds');
        $files = array_filter(
            $files,
            function ($file) {
                return preg_match('/\.(?:jpe?g)|png$/i', $file) > 0;
            }
        );
        $files = array_map(
            function ($file) {
                return "/assets/images/backgrounds/$file";
            },
            $files
        );

        return $files;
    }
}
