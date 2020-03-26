<?php

/**
 * Replace word which have the same meaning
 *
 * Original maskdata.csv is not in a good formatted, each institution might have different format of same word.
 * adjustText will adjust these word and that word in $text in same format.
 *
 * @param string $text string to adjust
 * @return string string after adjust
 */
function adjustText(string $text): string
{
    $text = str_replace(["Ｏ", "0", "˙", "．", "，", "-", "－"], ["零", "零", "、", "、", "、", "之", "之"], $text);
    $text = str_replace(["０", "１", "２", "３", "４", "５", "６", "７", "８", "９"], ["零", "一", "二", "三", "四", "五", "六", "七", "八", "九"], $text);
    $text = str_replace(["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"], ["零", "一", "二", "三", "四", "五", "六", "七", "八", "九"], $text);
    $text = str_replace(["台", "F", "f", "Ｆ", "ｆ"], ["臺", "樓", "樓", "樓", "樓"], $text);
    return $text;
}
