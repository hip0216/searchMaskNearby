<?php
/**
 * Return map of command values
 *
 * Since command value is not equal to table keys in $taable
 * Use mapKey to map those argument values to corresponded table keys
 *
 * @param array $vals input rgument values array for one command
 *
 * @return array return array where map each values to corresponded attribute of table
 */
function mapToTableHeaders(array $vals): array
{
    $mapping = [
        'a' => "成人口罩", 'adult' => "成人口罩", 'default' => "成人口罩",
        'c' => "孩童口罩", 'child' => "孩童口罩",
        's' => "口罩總數", 'sum' => "口罩總數",
        'i' => "機構名稱", 'institution' => "機構名稱",
        'd' => "機構地址", 'address' => "機構地址",
    ];
    foreach ($vals as $val) {
        $sortKeys[] = $mapping[$val];
    }
    return $sortKeys ?? [];
}
