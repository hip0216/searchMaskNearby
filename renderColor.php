<?php

require_once('colorToStr.php');

/**
 * Add color tag to element in array
 *
 * Decorate values who with a specific key a color tag in array.
 * Color tag will be add according to the value of $key in each row of $arr.
 * After use this function, use $climate->table($arr) can show value with color.
 *
 * @param array $arr array that contains many data and each have a value with key $key.
 * @param string $key the key of value that we want to add color tag.
 */
function renderColor(array &$arr, string $key)
{
    foreach ($arr as &$row) {
        if ($row[$key] == 0) {
            $row[$key] = colorToStr("red", $row[$key]);
        } elseif ($row[$key] <= 20) {
            $row[$key] = colorToStr("light_red", $row[$key]);
        } elseif ($row[$key] <= 100) {
            $row[$key] = colorToStr("yellow", $row[$key]);
        } else {
            $row[$key] = colorToStr("green", $row[$key]);
        }
    }
}
