<?php
class CommandReconize
{
    private $table;
    private $sortInIncrease = false;
    private $sortHeaders = [];
    private $filterHeaders = [];

    /**
     * construct of CommandReconize
     */
    public function __construct($table = [])
    {
        $this->table = $table;
    }

    /**
     * setter function of sortInIncrease
     */
    public function setSortInIncrease(bool $b): CommandReconize
    {
        $this->sortInIncrease = $b;
        return $this;
    }

    /**
     * setter function of sortHeaders
     */
    public function setSortHeaders(array $a): CommandReconize
    {
        $this->sortHeaders = $a;
        return $this;
    }

    /**
     * setter function of filterHeaders
     */
    public function setFilterHeaders(array $a): CommandReconize
    {
        $this->filterHeaders = $a;
        return $this;
    }

    /**
     * getter function of table
     */
    public function getTable(): array
    {
        return $this->table;
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
    public static function mapToTableHeaders(array $vals): array
    {
        $mapping = [
            'a' => ADULT, 'adult' => ADULT, 'default' => ADULT,
            'c' => CHILD, 'child' => CHILD,
            's' => SUM, 'sum' => SUM,
            'i' => INSTITUTION, 'institution' => INSTITUTION,
            'd' => ADDRESS, 'address' => ADDRESS,
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
     * Since we need to support string sorting, we can't use '-' as $diff in program
     * 
     * @parma array $r0 a row of data
     * @parma array $r1 a row of data
     * @var array $this->sortKeys An array contains headers in table we want to sort depends on them
     * @var bool $this->sortInIncrease Sort result in increase or decrease will depends on this
     */
    public function sortRule(array $r0, array $r1): int
    {
        foreach ($this->sortHeaders as $sortKey) {
            $diff = $r0[$sortKey] > $r1[$sortKey];
            if ($r0[$sortKey] != $r1[$sortKey]) {
                return $this->sortInIncrease ? $diff : !$diff;
            }
        }
        return 0;
    }

    public function numericFilter(int $min, int $max): void
    {
        $ret = [];
        foreach ($this->table as $row) {
            $success = true;

            foreach ($this->filterHeaders as $header) {
                echo $header."\n";
                //$header = $this->filterHeaders[0];
                if (!($min <= $row[$header] and $row[$header] <= $max)) {
                    $success = false;
                    break;
                }
            }
            if ($success) {
                $ret[] = $row;
            }
        }
        $this->table = $ret;
    }

    public function stringFilter(array $needles): void
    {
        // $ret = [];
        // foreach ($this->table as $row) {
        //     $success = true;
        //     foreach ($this->filterHeaders as $header) {
        //         if (!($min <= $row[$header] and $row[$header] <= $max)) {
        //             $success = false;
        //             break;
        //         }
        //     }
        //     if ($success) {
        //         $ret[] = $row;
        //     }
        // }
        // $this->table = $ret;
    }

    /**
     * @parma array $table
     * @parma array $cmdPairs
     * 
     */
    public function run(array $cmdPairs)
    {
        foreach ($cmdPairs as $cmd => $vals) {
            switch ($cmd) {
            # sort part
            case 's':
            case 'sort':
            case 'sortDecrease':
                $this->sortInIncrease = false;
                $this->sortHeaders = self::mapToTableHeaders($vals);
                usort($this->table, 'self::sortRule');
                break;
            case 'sortIncrease':
                $this->sortInIncrease = true;
                $this->sortHeaders = self::mapToTableHeaders($vals);
                usort($this->table, 'self::sortRule');
                break;

            # filter-numeric part
            case 'a':
            case 'adult':
            case 'c':
            case 'child':
            case 's':
            case 'sum':
                $this->filterHeaders = self::mapToTableHeaders([$cmd]);
                $min = $vals[0];
                $max = $vals[1] ?? '99999';
                $this->numericFilter($min, $max);
                break;

            # filter-string part
            case 'd':
            case 'address':
            case 'i':
            case 'institution':
                $this->filterHeaders = self::mapToTableHeaders([$cmd]);
                $this->stringFilter($vals);
                break;
            case 'returnLimit':
                break;
            case 'setTeams':
                break;
            case 'sendToTeams':
                break;
            }
        }
    }

}


