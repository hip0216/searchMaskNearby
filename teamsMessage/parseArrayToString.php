<?php

namespace SmallFreshMeat;

function parseArrayToString(array $datas)
{
    $keys = array_keys($datas[0]);
    $string = "";

    $maxLength = [];
    foreach ($keys as $key) {
        $maxLength[] = mb_strlen($key, "utf-8");
    }
    $index = 0;
    foreach ($datas as $data) {
        foreach ($keys as $key) {
            $maxLength[$index] = $maxLength[$index] < mb_strlen($data["$key"], "utf-8") ?
                                 mb_strlen($data["$key"], "utf-8") : $maxLength[$index];
            $index++;
        }
        $index = 0;
    }

    foreach ($keys as $key) {
        $string .= $key;
        if ($key != $keys[count($keys) - 1]) {
            $string .= str_repeat("・", $maxLength[$index] + 3 - mb_strlen($key, "utf-8"));
            $index++;
        }
    }
    $index = 0;
    $string .= "\n\r";

    foreach ($datas as $data) {
        foreach ($keys as $key) {
            $string .= $data["$key"];
            if ($key != $keys[count($keys) - 1]) {
                $string .= str_repeat("・", $maxLength[$index] + 3 - mb_strlen($data["$key"], "utf-8"));
            }
            $index++;
        }
        $string .= "\n\r";
        $index = 0;
    }

    return $string;
}
