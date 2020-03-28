<?php
class CommandReconize
{
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
    public static function mapToTableHeaders(array $vals): array
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
    
    /**
     * Sorting rule for commandReconize sort genre command to call
     * 
     * THis function can sort by multiple header
     * Use this with usort to sort array
     * 
     * @parma array $r0 a row of data
     * @parma array $r1 a row of data
     * @global array $sortKeys An array contains headers in table we want to sort depends on them
     * @global bool $sortInIncrease Sort result in increase or decrease will depends on this
     */
    public static function sortRule(array $r0, array $r1)
    {
        global $sortHeaders;
        global $sortInIncrease;
        foreach ($sortHeaders as $sortKey) {
            $diff = $r0[$sortKey] - $r1[$sortKey];
            if ($diff) {
                return $sortInIncrease ? $diff : -$diff;
            }
        }
        return false;
    }

    /**
     * 
     * 
     */
    function run(array $table, array $parameterPairs)
    {
        foreach ($ParameterPairs as $key => $vals) {
            switch ($key) {
            case 's':
            case 'sort':
            case 'sortDecrease':
                $sortInIncrease = false;
                $sortHeaders[] = mapToTableHeaders($vals);
            case 'sortIncrease':
                $sortInIncrease = true;

                usort($table, sortRule);
                print_r($table);
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

}


