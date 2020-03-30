<?php

namespace SmallFreshMeat;

/**
 * Parse an array datas into a formatted string data
 * 
 * @param array $datas an array which contains some masks information datas
 * 
 * @return string $string return a formatted string data
 */
function parseArrayToString(array $datas)
{
    $keys = array_keys($datas[0]);
    $string = "";

    $maxLength = [];
    foreach ($keys as $key) {
        $maxLength[] = multiStringLength($key);
    }
    $index = 0;
    foreach ($datas as $data) {
        foreach ($keys as $key) {
            $maxLength[$index] = $maxLength[$index] < multiStringLength($data["$key"]) ?
                                 multiStringLength($data["$key"]) : $maxLength[$index];
            $index++;
        }
        $index = 0;
    }

    foreach ($keys as $key) {
        $string .= $key;
        if ($key != $keys[count($keys) - 1]) {
            if (($maxLength[$index] - multiStringLength($key)) % 2 == 0) {
                $string .= str_repeat("　", floor(($maxLength[$index] + 6 - multiStringLength($key)) / 2));
            } else {
                $string .= " ";
                $string .= str_repeat("　", floor(($maxLength[$index] + 6 - multiStringLength($key)) / 2));
            }
            $index++;
        }
    }
    $index = 0;
    $string .= "\n\r";

    foreach ($datas as $data) {
        foreach ($keys as $key) {
            $string .= $data["$key"];
            if ($key != $keys[count($keys) - 1]) {
                if (($maxLength[$index] - multiStringLength($data["$key"])) % 2 == 0) {
                    $string .= str_repeat("　", floor(($maxLength[$index] + 6 - multiStringLength($data["$key"])) / 2));
                } else {
                    $string .= " ";
                    $string .= str_repeat("　", floor(($maxLength[$index] + 6 - multiStringLength($data["$key"])) / 2));
                }
            }
            $index++;
        }
        $string .= "\n\r";
        $index = 0;
    }

    return $string;
}

/**
 * Return the length of a string.
 * 
 * The rule is: one English char equal one,
 * in other hand, one Chinese char equal two.
 * 
 * @param string $string a string which you want to count the length of it
 * 
 * @return int $multiStringLength return the length of a string
 */
function multiStringLength($string)
{
    $mb_strlen = mb_strlen($string, 'UTF-8');
    $multiStringLength = 0;
    for ($i = 0; $i < $mb_strlen; $i++) {
        $s = mb_substr($string, $i, 1, 'UTF-8');
        if (strlen($s) == 1) {
            $multiStringLength++;
        } else {
            $multiStringLength += 2;
        }
    }
    return $multiStringLength;
}
