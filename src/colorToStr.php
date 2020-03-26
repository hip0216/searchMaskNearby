<?php

/**
 * Add color tag to string $str
 *
 * Decorate $str with a color tag
 * Use climate to show the return value and it will have color on console
 *
 * @param string $color Color tag string. This will be fill in the tag.
 * @param string $str String we want to add a decoration of tag.
 * @return string the $str that after add a color tag.
 */
function colorToStr(string $color, string $str): string
{
    return "<$color>$str</$color>";
}
