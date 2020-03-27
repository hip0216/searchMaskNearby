<?php
require dirname(__DIR__) . '/vendor/autoload.php';

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
 * 
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

        # filter part
        case 'a':
        case 'adult':
        case 'c':
        case 'child':
        case 's':
        case 'sum':
        case 'd':
        case 'address':
        case 'i':
        case 'institution':
            $headersAsFilters[] = mapToTableHeaders($key);
            $min = $vals[0];
            $max = $vals[1] ?? '99999';
            foreach ($table as $row) {
                foreach ($headersAsFilters as $header) {
                    if (!($min <= $row[$header] and $row[$header] <= $max)) break;
                }
                $ret[] = $row[$header];
            }
            break;
        case 'returnLimit':
        case 'setTeams':
            break;  //// todo to set token of teams or others
        case 'sendToTeams':
            break;
        }
    }
}
