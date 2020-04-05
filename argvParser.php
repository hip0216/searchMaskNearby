<?php

use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\OptionPrinter\ConsoleOptionPrinter;

/**
 *
 */
class argvParser
{
    private $specs;
    private $argv;
    private const enableString = ['adult', 'child', 'sum', 'addr', 'init', 'a', 'c', 's', 'd', 'i'];
    public function __construct($argv)
    {
        $this->argv = $argv;
        $this->specs = new OptionCollection;
        $this->specs->add('sort+', '可用參數'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortDecrease+', '可用參數'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortIncrease+', '可用參數'.join(' ', self::enableString))
            ->isa('string');

        $this->specs->add('d|address+', '篩選地址')
            ->isa('string');
        $this->specs->add('i|institution+', '篩選機構名')
            ->isa('string');
        $this->specs->add('a|adult+', '用成人口罩數量篩選-a=m,n');
        // ->isa('Number');
        $this->specs->add('c|child+', '用小孩口罩數量篩選-c=m,n');
        // ->isa('Number');
        $this->specs->add('s|sum+', '用總數篩選-s=m,n');
        // ->isa('Number');

        $this->specs->add('returnLimit+', '最大回傳數量')
            ->isa('Number');

        $this->specs->add('sendToTeams', '發送到teams');
        $this->specs->add('appendGoogleApi', '');

        $this->specs->add('setTeamsToken+', '')
            ->isa('string');
        $this->specs->add('setGoogleApiKey+', '')
            ->isa('string');
        $this->specs->add('h|help', '列出可用選項');
    }

    public function getHelp()
    {
        $printer = new ConsoleOptionPrinter;
        echo $printer->render($this->specs);
    }

    public function getOption()
    {
        $parser = new OptionParser($this->specs);

        try {
            $result = $parser->parse($this->argv);
            $re = [];
            foreach ($result->keys as $key => $spec) {
                $re[$key] = $this->splitOptionString($spec->value);
                self::checkOptionRule($key, $re[$key]);
                if ($key == 'help') {
                    $this->getHelp();
                    return ['help' => []];
                } elseif ($key == 'setTeamsToken') {
                    return ['setTeamsToken' => $spec->value];
                } elseif ($key == 'setGoogleApiKey') {
                    return ['setGoogleApiKey' => $spec->value];
                }
            }
            uksort($re, 'argvParser::keyCmp');
            return($re);
        } catch (Exception $e) {
            echo $e->getMessage();
            echo "\n";
            return [];
        }
    }


    public static function keyCmp($a, $b)
    {
        $order = ['address', 'institution', 'adult', 'child', 'sum',
                  'sort', 'sortDecrease', 'sortIncrease',
                  'returnLimit',
                  'sendToTeams', 'appendGoogleApi',
                  'setTeamsToken', 'setGoogleApiKey', 'help',
                 ];
        return array_search($a, $order) - array_search($b, $order);
    }

    private function splitOptionString($arr)
    {
        $re = [];
        if (!is_array($arr)) {
            return [];
        }
        foreach ($arr as $i) {
            $re = array_merge($re, explode(',', $i));
        }
        return $re;
    }

    private function checkOptionRule($key, $value)
    {
        switch ($key) {
            case 'address':
            case 'institution':
                if (count($value) > 5 || count($value) < 1) {
                    throw new InvalidArgumentException($key.'參數數量錯誤，允許1~5個字串');
                }
                break;
            case 'adult':
            case 'child':
            case 'sum':
                if (count($value) > 2 || count($value) < 1) {
                    throw new InvalidArgumentException($key.'參數數量錯誤，允許1~2個數字');
                } elseif (is_numeric($value[0]) && is_numeric($value[1])) {
                    throw new InvalidArgumentException($key.'參數錯誤，允許數字');
                }
                break;
            case 'sortDecrease':
            case 'sortIncrease':
                if (count($value) > 5 || count($value) < 0) {
                    throw new InvalidArgumentException($key.'參數數量錯誤，允許0~5個字串');
                } else {
                    foreach ($value as $v) {
                        print_r($value);
                        if (!in_array($v, self::enableString)) {
                            throw new InvalidArgumentException($key.'錯誤的參數'.$v.'允許以下值'.join(' ', self::enableString));
                        }
                    }
                }
                break;
            case 'returnLimit':
                if (count($value) != 1) {
                    throw new InvalidArgumentException($key.'參數數量錯誤，允許1個數字');
                } elseif ($value[0] > 30 || $value[0] < 1) {
                    throw new InvalidArgumentException($key.'參數數值，允許1~30的數字');
                }
                break;

            default:
                return;
                break;
        }
    }
}
