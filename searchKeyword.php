<?php

/**
 * Search keyword in given array and keyword and return matched data.
 *
 * Use $keyword as a needle to search each key in $keys in array $data.
 * return a array that all rows of data that we find $keyword exist in $data[row][key].
 * return null if none of row in data match.
 *
 * @param array $data The table that we want to search.
 * @param string $keyword The needle we want to search.
 * @param array $keys each element in $keys is the key we want to search in data.
 * @return mixed return array if find matched data and null if none of data match.
 */
function searchKeyword(array &$data, string $keyword, ...$keys)
{
    $ret = null;
    foreach ($data as $row) {
        foreach ($keys as $key) {
            if (strpos($row[$key], $keyword) !== false) {
                $ret[] = $row;
                break;
            }
        }
    }
    return $ret;
}
