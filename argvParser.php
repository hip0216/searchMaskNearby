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
        $this->specs->add('sort:', '排序方法'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortDecrease:', '排序方法'.join(' ', self::enableString))
            ->isa('string');
        $this->specs->add('sortIncrease:', '排序方法'.join(' ', self::enableString))
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
        $this->specs->add('returnLimit:', '最大回傳數量')
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
            $temp = ['filter' => [], 'sort' =>[], 'preReturn' => [], 'return' => [], 'alone' => []];
            foreach ($result->keys as $key => $spec) {
                // print_r($spec);
                switch (self::optionType($key)) {
                    case 'filter':
                        if (self::FilterValueError($spec)) {
                            throw new InvalidArgumentException($key.'參數錯誤');
                        }
                        break;
                    case 'sort':
                        break;
                    case 'preReturn':
                        break;
                    case 'return':
                        break;
                    case 'alone':
                        break;
                    default:
                        break;
                }
                if ($key == 'help') {
                    $this->getHelp();
                    return [];
                }
                $temp[self::optionType($key)][$key] = $spec->value;
            }
            $re = [];
            foreach ($temp as $key => $value) {
                if ($value != []) {
                    $re = $value;
                }
            }
            return($re);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
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
