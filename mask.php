<?php
require_once("vendor/autoload.php");
require_once("argvParser.php");
require_once("src/commandRecognize.php");
require_once("src/renderColor.php");
use JamesGordo\CSV\Parser;
use League\CLImate\CLImate;

function downloadFile()
{
    $maskDataUrl = "http://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv";
    if (time() - filemtime("maskdata.csv") > 300) {
        unlink("maskdata.csv");
    }
    if (is_file("maskdata.csv") === false) {
        if (file_put_contents("maskdata.csv", file_get_contents($maskDataUrl))) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function convertHeader($datas) {
    $map = [
        '醫事機構代碼' => ID,
        '醫事機構名稱' => INSTITUTION,
        '醫事機構地址' => ADDRESS,
        '醫事機構電話' => PHONENUMBER,
        '成人口罩剩餘數' => ADULT,
        '兒童口罩剩餘數' => CHILD,
        '來源資料時間' => TIME,
    ];

    $ret = [];
    foreach ($datas as $id => $data){
        foreach ($data as $key => $val) {
            $ret[$id][$map[$key]] = $val;
        }
    }
    return $ret;
}

function dealField(&$datas) {
    foreach($datas as &$data) {
        unset($data[ID]);
        unset($data[TIME]);
        $data[SUM] = $data[ADULT]+$data[CHILD];
    }
}

if (downloadFile() === false) {
    print("下載檔案錯誤");
    exit();
}

// parse argv parameter to $option
$parser = new argvParser($argv);
$option = $parser->getOption();
print_r($option);

// convert maskdata
$datas = new Parser("maskdata.csv");
$datas = convertHeader($datas->all());
$time = $datas[0][TIME];
dealField($datas);
$cmdRcnz = new CommandRecognize($datas);
$cmdRcnz->run($option);
$datas = $cmdRcnz->getTable();

// show
$climate = new CLImate();
if ($datas) {
    $climate->table($datas);
} else {
    echo "No Result\n";
}
$climate->put("Last updat: $time");



