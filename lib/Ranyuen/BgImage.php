<?php

/**
 * Ranyuen web site.
 *
 * @author  Ranyuen <cal_pone@ranyuen.com>
 * @license http://www.gnu.org/copyleft/gpl.html GPL-3.0+
 * @link    http://ranyuen.com/
 */

namespace Ranyuen;

/**
 * Backgroud image selector.
 */
class BgImage
{
    private $resources = [];
    /**
     * Request session.
     *
     * @var Ranyuen\Session
     *
     * @Inject
     */
    private $session;

    public function __construct()
    {
        if (isset($this->session['bgimage'])) {
            $bgimage = $this->session['bgimage'];
            if ($bgimage['expiration'] >= time()) {
                $this->resources = [$bgimage['img']];

                return $this;
            }
            unset($this->session['bgimage']);
        }
        $this->resources = $this->getAvailableImages();
    }

    /**
     * Random background image.
     *
     * @return string
     */
    public function getRandom()
    {
        $image = $this->resources[array_rand($this->resources)];
        $this->session['bgimage'] = [
            'img' => $image,
            'expiration' => isset($this->session['bgimage']['expiration']) ?
                $this->session['bgimage']['expiration'] :
                time() + 3600,
        ];

        return $image;
    }

    /**
     * All available background images.
     *
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
