<?php
require 'vendor/autoload.php';

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
    private const enableString = ['adult', 'child', 'sum', 'addr', 'init'];
    public function __construct($argv)
    {
        $this->argv = $argv;
        $this->specs = new OptionCollection;
        $this->specs->add('sort+', '排序方法'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortDecrease+', '排序方法'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortIncrease+', '排序方法'.join(' ', self::enableString))
            ->isa('string');

        $this->specs->add('d|address+', '篩選地址')
            ->isa('string');
        $this->specs->add('i|institution+', '篩選機構名')
            ->isa('string');
        $this->specs->add('a|adult+', '用成人口罩數量篩選')
            ->isa('Number');
        $this->specs->add('c|child+', '用小孩口罩數量篩選')
            ->isa('Number');
        $this->specs->add('s|sum+', '用總數篩選')
            ->isa('Number');
        $this->specs->add('returnLimit+', '最大回傳數量')
            ->isa('Number');
        $this->specs->add('sendToTeams', '發送到teams');

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
                if ($key == 'help') {
                    $this->getHelp();
                    return [];
                }
                $re[$key] = $spec->value;
            }
            uksort($re, 'argvParser::keyCmp');
            return($re);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }


    public static function keyCmp($a, $b)
    {
        $order = ['address', 'institution', 'adult', 'child', 'sum',
                  'sort', 'sortDecrease', 'sortIncrease',
                  'returnLimit',
                  'sendToTeams',
                  'setTeams',
                  'help'];
        return array_search($a, $order) - array_search($b, $order);
    }

    private function optionType($str)
    {
        switch ($str) {
            case 'address':
            case 'institution':
            case 'adult':
            case 'child':
            case 'sum':
                return 'filter';
                break;
            case 'sortDecrease':
            case 'sortIncrease':
                return 'sort';
                break;
            case 'returnLimit':
                return 'preReturn';
                break;
            case 'sendToTeams':
                return 'return';
                break;
            case 'setTeams':
            case 'help':
                return 'alone';
                break;
            default:
                break;
        }
    }

    private function FilterValueError($str)
    {
        return false;
    }

    private function sortValueError()
    {
    }

    private function preReturnValueError()
    {
    }

    private function returnValueError()
    {
    }

    private function aloneValueError()
    {
    }
}
