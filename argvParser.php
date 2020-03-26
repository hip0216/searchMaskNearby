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
        $this->argv = $argv;
        $this->specs = new OptionCollection;
        $this->specs->add('s|sort+', '排序方法-s=a')
            ->isa('String');
        $this->specs->add('f|fil+', '篩選方法')
            ->isa('String');
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
                $re[$key] = $spec->value;
            }
            return($re);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
