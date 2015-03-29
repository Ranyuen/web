<?php

class TemplateHelper
{
    public function dokutsurutake($str)
    {
        if (is_array($str)) {
            $str = implode('', $str);
        }

        return "死の天使 $str";
    }

    public function kaentake($arr)
    {
        if (!is_array($arr)) {
            $arr = [$arr];
        }

        return '症状: '.implode('・', $arr);
    }
}
