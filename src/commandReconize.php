<?php

// hadn't finish, hadn't validate
//function
function sortRule($r0, $r1)
{
    global $sortKeys, $sortInIncrease;
    foreach ($sortKeys as $sortKey) {
        if ($r0[$sortKey] != $r1[$sortKey]) {
            return $sortInIncrease ? $r0[$sortKey] - $r1[$sortKey] : $r1[$sortKey] - $r0[$sortKey];
        }
    }
    return false;
}

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
function mapKey(array $vals): array
{
    $mapping = [
        'a' => "成人口罩", 'adult' => "成人口罩", 'default' => "成人口罩",
        'c' => "孩童口罩", 'child' => "孩童口罩",
        's' => "口罩總數", 'sum' => "口罩總數",
        'i' => "機構名稱", 'init' => "機構名稱",
        'ad' => "機構地址", 'addr' => "機構地址",
    ];
    foreach ($vals as $val) {
        $sortKeys[] = $mapping[$val];
    }
    return $sortKeys ?? [];
}

/**
 * 
 */
function commandReconize(array $table, array $parameterPairs)
{
    foreach ($ParameterPairs as $key => $vals) {
        switch ($key) {
        case 's':
        case 'sort':
        case 'sortDecrease':
        case 'sortIncrease':

            $sortKeys = $sortKeys ?? $mapping['default'];
            usort($table, sortRule);

            break;
        case 'd':
        case 'address':
        case 'i':
        case 'institution':
        case 'a':
        case 'adult':
        case 'c':
        case 'child':
        case 'sum':
            $filterKeys[] = $mapping[$keys];
            if (count($vals) === 1) {
                $vals[] = '99999';
            }
                $table = array_values(array_filter($table, function ($r0, $r1) use ($vals) {
                    return ;
                }));

            break;
        case 'returnLimit':
        case 'setTeams':
            break;  //// todo to set token of teams or others
        case 'sendToTeams':
            break;
        }
    }
}
