<?php
namespace SmallFreshMeat\SaveFile;
class FileProcess{

    private function informationArrayToString($informationArray){
        $nowTime=strtotime("now");
        $outPutString=$informationArray["機構名稱"].",".$informationArray["機構地址"].",".$informationArray["成人口罩"].",";
        if(@$informationArray['星等']!=""){
            $outPutString.=$informationArray['星等'].",";
        }
        else{
            $outPutString.="無星等資訊,";
        }
        if(@is_array($informationArray['營業時間'])){
            $outPutString.=$this->arrayOpenTimeToString($informationArray).",";
        }
        else{
            $outPutString.="無營業時間資訊,";
        }
        $outPutString.=$nowTime."\n";
        return $outPutString;
    }

    private function arrayOpenTimeToString($inputArray){
        $openTimeString="";
        for($closeAndOpenTime=0;$closeAndOpenTime<count($inputArray['營業時間']);$closeAndOpenTime++){
            $openTimeString.=$inputArray['營業時間'][$closeAndOpenTime]['close']['day'];
            $openTimeString.="#".$inputArray['營業時間'][$closeAndOpenTime]['open']['time'];
            if($closeAndOpenTime<(count($inputArray['營業時間'])-1)){
                $openTimeString.="#".$inputArray['營業時間'][$closeAndOpenTime]['close']['time']."?";
            }
            else{
                $openTimeString.="#".$inputArray['營業時間'][$closeAndOpenTime]['close']['time'];
            }
        }
        return $openTimeString;
    }

    private function appendStarToArray(&$needAppendArray,$starString){
        $needAppendArray["星等"]=(string)$starString;
    }

    private function appendOpenTimeToArray(&$needAppendArray,$openTimeString){
        $openAndCloseTimeArray=explode("?",$openTimeString);
        for($Day=0;$Day<count($openAndCloseTimeArray);$Day++){
            $openAndCloseDayArray=explode("#",$openAndCloseTimeArray[$Day]);
            $needAppendArray["營業時間"][$Day]=["close" =>["day" => $openAndCloseDayArray[0] , "time" =>$openAndCloseDayArray[2]],"open" =>[ "day" =>$openAndCloseDayArray[0],"time"=>$openAndCloseDayArray[1]]];
        }
    }

    private function makeHashForFile($makeHashFile,$wantExplodeArray){
        $hash_array=[];
        for($i=0;$i<count($makeHashFile);$i++){
            $makeHashFile[$i]=str_replace(["\r", "\n", "\r\n", "\n\r"],'',$makeHashFile[$i]);
            $fileExplodeByComma=explode(",",$makeHashFile[$i]);
            if($wantExplodeArray){
                $hash_array[$fileExplodeByComma[1]]=["fileData"=>$fileExplodeByComma,"inFileIndex"=>$i];
            }
            else{
                $hash_array[$fileExplodeByComma[1]]=["fileData"=>$makeHashFile[$i],"inFileIndex"=>$i];
            }
        }
        return $hash_array;
    }

    private function searchFile($inputDataArray,$hash_array){
        $noInFile=[];
        $inFile=[];
        $nowTime=(int)strtotime("now");
        for($i=0;$i<count($inputDataArray);$i++){
            $hospital_address=$inputDataArray[$i]["機構地址"];
            if(isset($hash_array[$hospital_address]) and $this->checkDataTime($nowTime,$hash_array[$hospital_address]["fileData"][5])){
                $this->appendStarToArray($inputDataArray[$i],$hash_array[$hospital_address]["fileData"][3]);
                if($hash_array[$hospital_address]["fileData"][4]!="無營業時間資訊"){
                    $this->appendOpenTimeToArray($inputDataArray[$i],$hash_array[$hospital_address]["fileData"][4]);
                }
                else{
                    $inputDataArray[$i]['營業時間']=$hash_array[$hospital_address]["fileData"][4];
                }
                $inFile[]=$inputDataArray[$i];
            }

            else{
                $inputDataArray[$i]['星等']='';
                $inputDataArray[$i]['營業時間']='';
                $noInFile[]=$inputDataArray[$i];
            }
        }
        return ["yes"=>$inFile,"no"=>$noInFile];
    }

    private function saveFile($wantSaveFile,$openFileMethod,$useInformationArrayToString){
        $openFileToSaveData=fopen("GoogleMapInforamtion.txt",$openFileMethod);
        for($i=0;$i<count($wantSaveFile);$i++){
            if($useInformationArrayToString){
                $informationString=$this->informationArrayToString($wantSaveFile[$i]);
                fwrite($openFileToSaveData,$informationString);
            }
            else{
                fwrite($openFileToSaveData,$wantSaveFile[$i]);
            }
        }
    }

    public function checkDataNotInFile($wantSaveData){
        @$localFile=file("GoogleMapInforamtion.txt");
        if(@$localFile){
            $maskHashArray=$this->makeHashForFile($localFile,False);
            for($i=0;$i<count($wantSaveData);$i++){
                $informationString=$this->informationArrayToString($wantSaveData[$i]);
                if (isset($maskHashArray[$wantSaveData[$i]["機構地址"]])){
                    $localFile[$maskHashArray[$wantSaveData[$i]["機構地址"]]["inFileIndex"]]=$informationString;
                }
                else{
                    $localFile[]=$informationString;
                }
            }
            $this->saveFile($localFile,"w",false);
        }
        else{
            $this->saveFile($wantSaveData,"w",true);
        }
    }
    
    private function checkDataTime($nowTime,$needCheckTimeString){
        //要測試要改數字2592000
        if ($nowTime-$needCheckTimeString<2592000){
            return true;
        }
        else{
            return False;
        }
    }

    public function return_yes_or_no($rawData){
        @$useToCheckrawData=file("GoogleMapInforamtion.txt");
        if(@$useToCheckrawData){
            $hashArray=$this->makeHashForFile($useToCheckrawData,true);
            $saveYesAndNoArray=$this->searchFile($rawData,$hashArray);
            return $saveYesAndNoArray;
        }
        else{
            return ["yes"=>[],"no"=>$rawData];
        }
    }
    
    public function save_data($wantSaveData){
        $this->checkDataNotInFile($wantSaveData);
    }

}