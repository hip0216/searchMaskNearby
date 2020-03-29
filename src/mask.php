<?php

/* Dev: russell.tseng
 * A console version mask map in Taiwan
 */

# import relation file
require_once('vendor/autoload.php');
require_once('adjustText.php');
require_once('renderColor.php');
require_once('searchKeyword.php');
const MAX_ROW_LEN = 1000;
const FILE_NAME = "maskdata.csv";
$clmt = new League\CLImate\CLImate();

# try to update file
$clmt->comment("Try to update data from the Internet...");
system("curl -L --max-time 5 -O \"http://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv\"");
$clmt->comment("Trying update is end. Weither <green>SUCCESS</green> or <red>NOT</red>.");
$clmt->comment("Import data...");

# import data
if (!file_exists(FILE_NAME)) {
    $clmt->error("Import data fail!!");
    $clmt->comment("It seems we didn't have old data and fail on update new one at the same time");
    $clmt->comment("Try it later~~");
    return;
}
$fp = fopen(FILE_NAME, "r");

$title = fgetcsv($fp, MAX_ROW_LEN); // not used title
for ($rowid = 0; ($row = fgetcsv($fp, MAX_ROW_LEN)) !== false; ++$rowid) {
    //$data[$rowid]["id"] = $row[0];
    $data[$rowid]["機構"] = adjustText($row[1]);
    $data[$rowid]["地址"] = adjustText($row[2]);
    $data[$rowid]["電話"] = $row[3];
    $data[$rowid]["成人口罩"] = (int)$row[4];
    $data[$rowid]["孩童口罩"] = (int)$row[5];
    //$data[$rowid]["更新時間"] = $row[6];
    $lastUpdateTime = $row[6];
}
$clmt->info("Import data finished.");
$clmt->info("Last update time: $lastUpdateTime");

# Recursively search
$ret = null;
$continued = false;
while (true) {
    $clmt->whisper(str_repeat('=', 40));
    $clmt->comment("Input <green>keyword</green> to search or <green>man</green> to show commands:");
    $clmt->yellow()->inline("    > ");
    $keyword = adjustText(trim(fgets(STDIN)));
    
    # Input protect and command check
    if (!$keyword) {
        $clmt->comment("Keyword should not be space, tab or null");
        continue;
    } elseif ($keyword === "clear") {
        system("clear");
        continue;
    } elseif ($keyword === "exit") {
        return;
    } elseif ($keyword === "man") {
        $clmt->comment("    * <green>man</green> to show command menu.");
        $clmt->comment("    * <green>clear</green> to clear console.");
        $clmt->comment("    * <green>continue</green> to keep search on current result.");
        $clmt->comment("    * <red>exit</red> to exit.");
        $clmt->comment("    * <red>^c</red> to exit.");
        continue;
    } elseif ($keyword === "continue") {
        $dealData = $ret;
        $ret = null;
        $continued = true;
        continue;
    } else {
        $clmt->out("Your keyword is: \"$keyword\".");
        if (!$continued) {
            $dealData = $data;
        }
        $ret = null;
        $continued = false;
    }

    # Search keyword in $data
    $clmt->comment("Search data...");
    $ret = searchKeyword($dealData, $keyword, "地址", "機構");

    # Ouput result
    $clmt->info("Result:");
    if ($ret) {
        usort($ret, function ($a, $b) {
            return $a["成人口罩"] < $b["成人口罩"];
        });
        renderColor($ret, "成人口罩");
        renderColor($ret, "孩童口罩");
        $clmt->table($ret);
        $clmt->info("Last update time: $lastUpdateTime");
    } else {
        $clmt->error(">>> Sorry! No result! <<<");
    }
}
