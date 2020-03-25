<?php
// hadn't finish, hadn't validate
//function 

function commandReconize (array $table, array $parameterPairs) {
    $sortInIncrease = false;
    foreach ($ParameterPairs as $key => $vals) {
        $vals = explode(',', $vals);
        switch ($key) {
            case 'sortDecrease': case 'sort': $sortInIncrease = false; // current logic error
            case 'sortIncrease': $sortInIncrease = true;
                foreach ($vals as $val) {
                    if ($val === 'adult' or $val === 'a') $sortKeys[] = "成人口罩";  //// todo
                    elseif ($val === 'child' or $val === 'c') $sortKeys[] = "孩童口罩";  //// todo
                    elseif ($val === 'sum' or $val == 's') $sortKeys[] = "口罩總數";  //// todo
                    elseif ($val === 'addr' or $val == 'ad') $sortKeys[] = "地址";  //// todo
                    elseif ($val === 'inst' or $val == 'i') $sortKeys[] = "口罩總數";  //// todo
                } $sortKeys = $sortKeys ?? ["成人口罩"];  //// default
                usort($table, function($r0, $r1)use($sortKeys, $sortInIncrease){
                    foreach ($sortKeys as $sortKey)
                        if ($r0[$sortKey] != $r1[$sortKey])
                            return $sortInIncrease ? $r0[$sortKey] - $r1[$sortKey] : $r1[$sortKey] - $r0[$sortKey];
                    return false;
                });
                break;
            case 'adultCnt': $filterKeys[] = "成人口罩"; // current logic error
            case 'childCnt': $filterKeys[] = "孩童口罩";
            case 'sumCnt': $filterKeys[] = "口罩總數";
                if (count($vals) === 1) $vals[] = '99999';
                $table = array_values(array_filter($table, function($r0, $r1)use($vals){return ;}));
                break;
            case 'set': break;  //// todo to set token of teams or others
            case '': break;
            case '': break;
            case '': break;
            case 'push': break;  //// todo call API
            case 'man': break;  //// show all command
        }
    }
}