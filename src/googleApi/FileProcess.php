<?php
namespace SmallFreshMeat\SaveFile;
/** 
*This class for googleApi to savedata and readdata to file.
*/
class FileProcess{
    /**
    *this method change file format to output format
    *@return string
    */
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
            $outPutString.=$this->appendOpenTimeToString($informationArray).",";
        }
        else{
            $outPutString.="無營業時間資訊,";
        }
        $outPutString.=$nowTime."\n";
        return $outPutString;
    }
    /**
    *this method change array opentime information format to string  
    *@return string
    */
    private function appendOpenTimeToString($inputArray){
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
    /**
    *this method append file star information to array
    */
    private function appendStarToArray(&$needAppendArray,$starString){
        $needAppendArray["星等"]=(string)$starString;
    }
    /**
    *this method append file opentime information to array
    */
    private function appendOpenTimeToArray(&$needAppendArray,$openTimeString){
        $openAndCloseTimeArray=explode("?",$openTimeString);
        for($Day=0;$Day<count($openAndCloseTimeArray);$Day++){
            $openAndCloseDayArray=explode("#",$openAndCloseTimeArray[$Day]);
            $needAppendArray["營業時間"][$Day]=["close" =>["day" => $openAndCloseDayArray[0] , "time" =>$openAndCloseDayArray[2]],"open" =>[ "day" =>$openAndCloseDayArray[0],"time"=>$openAndCloseDayArray[1]]];
        }
    }
    /**
    *this method use file data to make hash table format is hash table['hospital address']=array("fileData"=>the hospital address all information,"inFileIndex"=>in file index)
    *@return array
    */
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
    /**
    *this method check data is in file or not in file output format is array(in file data,not in file data)
    *@return array
    */
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
    /**
    *this method for data save in file 
    */
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
    /**
    *this method check input data status is in file or not in file then save data  
    */
    private function checkDataStatus($wantSaveData){
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
    //確認在檔案裡的時間是否超過時限,時限在此定為一個月,當超過時有讀取到該資訊須對其進行更新,返回布林值
    private function checkDataTime($nowTime,$needCheckTimeString){
        //$aMonthTime=給檔案需要更新的時限
        $aMonthTime=2592000;
        if ($nowTime-$needCheckTimeString<$aMonthTime){
            return true;
        }
        else{
            return False;
        }
    }
    /**
    *this method for googleApi to search data is in file or not in file and use file data append star and opentime
    *@return array
    */
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
    /**
    *this method for googleApi to save data
    */
    public function save_data($wantSaveData){
        $this->checkDataStatus($wantSaveData);
    }

}