<?php
function sortRule($r0, $r1)
{
    global $sortKeys;
    global $sortInIncrease;
    foreach ($sortKeys as $sortKey) {
        if ($r0[$sortKey] != $r1[$sortKey]) {
            return $sortInIncrease ? $r0[$sortKey] - $r1[$sortKey] : $r0[$sortKey] - $r1[$sortKey];
        }
    }
    return false;
}
$sortKeys = ['a', 'b', 'c'];
$sortInIncrease = false;
//echo sortRule( ['a' => 2, 'b' => 4, 'c' => 6],  ['a' => 2, 'b' => 4, 'c' => 5]);

$input = [
    ['a' => 2, 'b' => 4, 'c' => 6],
    ['a' => 1, 'b' => 4, 'c' => 6],
    ['a' => 1, 'b' => 4, 'c' => 5],
    ['a' => 2, 'b' => 3, 'c' => 6],
    ['a' => 1, 'b' => 3, 'c' => 6],
    ['a' => 1, 'b' => 3, 'c' => 5],
    ['a' => 2, 'b' => 3, 'c' => 5],
    ['a' => 2, 'b' => 4, 'c' => 5],
];
$expect = [
    ['a' => 1, 'b' => 3, 'c' => 5],
    ['a' => 1, 'b' => 3, 'c' => 6],
    ['a' => 1, 'b' => 4, 'c' => 5],
    ['a' => 1, 'b' => 4, 'c' => 6],
    ['a' => 2, 'b' => 3, 'c' => 5],
    ['a' => 2, 'b' => 3, 'c' => 6],
    ['a' => 2, 'b' => 4, 'c' => 5],
    ['a' => 2, 'b' => 4, 'c' => 6],
];
usort($input, "sortRule");
print_r($input);