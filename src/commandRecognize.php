<?php

use Sebbmyr\Teams\TeamsConnector;

// You should copy tokenEample.php to token.php
// Then add your own token in token.php,
// or run mask_data.php by --setTeamsToken first
require 'token.php';
require 'setToken.php';
require 'teamsMessage/messageCard.php';
require 'teamsMessage/parseArrayToString.php';
use SmallFreshMeat\Teams\MessageCard;
use function SmallFreshMeat\parseArrayToString;

class CommandRecognize
{
    private $table;
    private $sortInIncrease = false;
    private $sortHeaders = [];
    private $filterHeaders = [];
    private $returnLimit = 30;

    /**
     * construct of CommandRecognize
     */
    public function __construct($table = [])
    {
        $this->table = $table;
    }

    /**
     * setter function of sortInIncrease
     */
    public function setSortInIncrease(bool $b): CommandRecognize
    {
        $this->sortInIncrease = $b;
        return $this;
    }

    /**
     * setter function of sortHeaders
     */
    public function setSortHeaders(array $a): CommandRecognize
    {
        $this->sortHeaders = $a;
        return $this;
    }

    /**
     * setter function of filterHeaders
     */
    public function setFilterHeaders(array $a): CommandRecognize
    {
        $this->filterHeaders = $a;
        return $this;
    }

    /**
     * getter function of table
     */
    public function getTable(): array
    {
        return array_slice($this->table, 0, $this->returnLimit);
    }

    /**
     * Return map of command values
     *
     * Since command value is not equal to table keys in $taable
     * Use mapKey to map those argument values to corresponded table keys
     *
     * Usage:
     *     mapToTableHeaders('a') => '成人口罩'
     *                        ＾
     *  a | c | s | i | d | adult | child | sum | institution | address
     * 
     * @param array $vals input argument values array for one command
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
     * Sorting rule for sort genre command to call
     * 
     * THis function can sort by multiple header
     * Use this with usort to sort array
     * Since we need to support string sorting, we can't use '-' as $diff in program
     * 
     * Usage:
     *     $this->setSortInIncrease = true; // or false
     *     usort($this->table, sortRule);
     *     print_r($this->getTable());
     * 
     * @parma array $r0 a row of data
     * @parma array $r1 a row of data
     * @var array $this->sortKeys An array contains headers in table we want to sort depends on them
     * @var bool $this->sortInIncrease Sort result in increase or decrease will depends on this
     * @return bool if a ceil is higher than another?
     */
    public function sortRule(array $r0, array $r1): bool
    {
        foreach ($this->sortHeaders as $sortKey) {
            $diff = $r0[$sortKey] > $r1[$sortKey];
            if ($r0[$sortKey] != $r1[$sortKey]) {
                return $this->sortInIncrease ? $diff : !$diff;
            }
        }
        return 0;
    }

    /**
     * Filtering rule for filterNumeric genre command to call
     * 
     * This function can found all row with specific range mask
     * We need to put that key in $this->filterHeaders[0] first (IMPORTANT)
     * Header accept only one at a time
     * 
     * Usage:           a | c | s | adult | child | sum
     *                               v
     *     $this->setFilterHeaders(['a']);  // search adult mask (accept only one)
     *     $this->numericFilter(0, 100);  // count between 0~100
     *     print_r($this->getTable());
     * 
     * @parma int $min a min mask count allowed
     * @parma int $max a row mask count allowed
     * @var array $this->filterHeaders First element contains filter header
     */
    public function numericFilter(int $min, int $max): void
    {
        foreach ($this->table as $row) {
            $header = $this->filterHeaders[0];
            if ($min <= $row[$header] and $row[$header] <= $max) {
                $ret[] = $row;
            }
        }
        $this->table = $ret ?? [];
    }

    /**
     * Filtering rule for filterString genre command to call
     * 
     * This function can found all row with specific keywords
     * We need to put that key in $this->filterHeaders[0] first (IMPORTANT)
     * Header accept only one at a time
     * Keyword accept multiple at a time, which will be "and" for all of them
     * 
     * Usage:
     *                   i | d | institution | address
     *                               v
     *     $this->setFilterHeaders(['d']);  // search adult mask (accept only one)
     *     $this->stringFilter(['keyword1' , 'keyword2']);  // keyword to search
     *     print_r($this->getTable());
     * 
     * @parma array $keywords array contain we want to search
     * @var array $this->filterHeaders First element contains filter header
     */
    public function stringFilter(array $keywords): void
    {
        $header = $this->filterHeaders[0];
        foreach ($this->table as $row) {
            $success = true;
            foreach ($keywords as $keyword) {
                if (strpos($row[$header], $keyword) === false) {
                     $success = false;
                     break;
                }
            }
            if ($success)
                $ret[] = $row;
        }
        $this->table = $ret ?? [];
    }

    /**
     * Run the function to reconize to command
     * 
     * The passing rule are as follow:
     * 
     * Usage:
     *     $this->run([
     *         'a' => [100], # Adult mask equal or more than 100
     *         'c' => [0, 999], # Child mask between 0 - 99
     *         's' => [100, 500] # Sum of mask between 100 - 500
     *         'i' => ['衛生所'] # Institution name contain '衛生所'
     *         'd' => ['臺北', '中山'] # Address contain '台北' and ‘中山’
     *         'sort' => ['a', 'c', 'i'] # Sort follow as:
     *                # adult mask count, child mask count and institution name
     *         'returnLimit' => [15] # Return restrict 15 rows of data
     *     ]);
     * 
     * @example Header accept only one at a time
     * 
     * @parma array $cmdPairs Contain [cmdkey -> [*cmd values*]]
     * @var $this->talbe
     */
    public function run(array $cmdPairs)
    {
        foreach ($cmdPairs as $cmd => $vals) {
            switch ($cmd) {
            # sort part
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
                $this->returnLimit = min($vals[0], 30);
                break;
            case 'setTeamsToken':
                setToken($vals[0],'TEAMS_WEBHOOK_TOKEN');
                break;
            case 'sendToTeams':
                // Require the webhook token
                $webhook = TEAMS_WEBHOOK_TOKEN;

                // Type your message title here
                $messageTitle = "查詢結果";
                // Transform the datas into string and make it formatted
                $messageContent = parseArrayToString($this->table);

                // You can see the result in the command line if you want
                // echo $messageContent;

                // Set a Teams connect by webhook
                $connector = new TeamsConnector($webhook);
                // Create a Teams message card
                $card = new MessageCard($messageTitle, $messageContent);
                // Send the card to Teams' channel
                $connector->send($card);
                break;
            }
        }
    }
}
