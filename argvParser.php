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
    public function __construct($argv)
    {
        $sortval = 'adult, child, sum, addr, init';
        $this->argv = $argv;
        $this->specs = new OptionCollection;
        $this->specs->add('s|sort+', '排序方法'.$sortval)
            ->isa('string');
        $this->specs->add('sortDecrease+', '排序方法'.$sortval)
            ->isa('string');
        $this->specs->add('sortIncrease+', '排序方法'.$sortval)
            ->isa('string');

        $this->specs->add('d|address+', '篩選地址')
            ->isa('string');
        $this->specs->add('i|institution+', '篩選機構名')
            ->isa('string');
        $this->specs->add('a|adult+', '用成人口罩數量篩選')
            ->isa('Number');
        $this->specs->add('c|child+', '用小孩口罩數量篩選')
            ->isa('Number');
        $this->specs->add('sum+', '用總數篩選')
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
            $re = [];
            foreach ($result->keys as $key => $spec) {
                if ($key == 'help') {
                    $this->getHelp();
                }
                $re[$key] = $spec->value;
            }
            return($re);
        } catch (Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
