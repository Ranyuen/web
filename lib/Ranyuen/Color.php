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
 * RGB and HSV color.
 *
 * See http://c4se.hatenablog.com/entry/2013/08/04/190937
 */
class Color
{
    private $r;
    private $g;
    private $b;

    /**
     * Load from RGB.
     *
     * @param int $r 0..255
     * @param int $g 0..255
     * @param int $b 0..255
     *
     * @return Color
     */
    public function fromRgb($r, $g, $b)
    {
        list($this->r, $this->g, $this->b) = [$r, $g, $b];

        return $this;
    }

    /**
     * Load from HSV.
     *
     * @param int $h 0..360 degree
     * @param int $s 0..100 %
     * @param int $v 0..100 %
     *
     * @return Color
     */
    public function fromHsv($h, $s, $v)
    {
        list($this->r, $this->g, $this->b) = $this->hsvToRgb($h, $s, $v);

        return $this;
    }

    /**
     * Get RGB.
     *
     * @return int[] [red 0..255, green 0..255, blue 0..255]
     */
    public function rgb()
    {
        return [$this->r, $this->g, $this->b];
    }

    /**
     * Get HSV.
     *
     * @return int[] [hue 0..360, saturation 0..100, value 0..100]
     */
    public function hsv()
    {
        return $this->rgbToHsv($this->r, $this->g, $this->b);
    }

    private function rgbToHsv($red, $green, $blue)
    {
        $red /= 255;
        $green /= 255;
        $blue /= 255;
        $cmax = max($red, $green, $blue);
        $cmin = min($red, $green, $blue);
        $d = $cmax - $cmin;
        if ((float) $d === 0.0) {
            return [0, 0, floor($cmax * 100)];
        }
        switch ($cmax) {
            case $red:
                $hue = 60 * (($green - $blue) / $d % 6);
                break;
            case $green:
                $hue = 60 * (($blue - $red) / $d + 2);
                break;
            default:
                $hue = 60 * (($red - $green) / $d + 4);
        }
        $hue = ($hue + 360) % 360;

        return [floor($hue), floor($d / $cmax * 100), floor($cmax * 100)];
    }

    private function hsvToRgb($hue, $saturation, $value)
    {
        $saturation /= 100;
        $value /= 100;
        $chroma = $value * $saturation;
        $x = $chroma * (1 - abs(($hue / 60) % 2 - 1));
        $m = $value - $chroma;
        if ($hue < 60) {
            list($red, $green, $blue) = [$chroma, $x, 0];
        } elseif ($hue < 120) {
            list($red, $green, $blue) = [$x, $chroma, 0];
        } elseif ($hue < 180) {
            list($red, $green, $blue) = [0, $chroma, $x];
        } elseif ($hue < 240) {
            list($red, $green, $blue) = [0, $x, $chroma];
        } elseif ($hue < 300) {
            list($red, $green, $blue) = [$x, 0, $chroma];
        } else {
            list($red, $green, $blue) = [$chroma, 0, $x];
        }

        return array_map(
            function ($channel) use ($m) {
                return ceil(($channel + $m) * 255);
            },
            [$red, $green, $blue]
        );
    }
}
