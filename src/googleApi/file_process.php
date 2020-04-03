<?php
namespace SmallFreshMeat\SaveFile;
class FileProcess{
    
    private function clean_data($file_array){
        for($i=0;$i<count($file_array);$i++){
            // unset($file_array[$i]["醫事機構電話"],$file_array[$i]["醫事機構名稱"],$file_array[$i]["成人口罩剩餘數"],);
            unset($file_array[$i]["醫事機構名稱"],$file_array[$i]["成人口罩剩餘數"],);
        }
        return $file_array;
    }

    private function make_hash($file){
        $hash_array=[];
        for($i=0;$i<count($file);$i++){
            $file[$i]=str_replace(["\r", "\n", "\r\n", "\n\r"],'',$file[$i]);
            $file[$i]=explode(",",$file[$i]);
            $hash_array[$file[$i][0]]=$file[$i];
        }
        return $hash_array;
    }

    private function search_file($file,$hash_array){
        $no=[];
        $yes=[];
        for($i=0;$i<count($file);$i++){
            $hospital_address=$file[$i]["醫事機構地址"];
            if(isset($hash_array[$hospital_address])){
                if($hash_array[$hospital_address][2]!="無營業時間資訊" and $hash_array[$hospital_address][1]!="無星等資訊"){
                    $file[$i]["星等"]=$hash_array[$hospital_address][5];
                    $day_and_open_and_close_all_week=explode("?",$hash_array[$hospital_address][6]);
                    for($j=0;$j<count($day_and_open_and_close_all_week);$j++){
                        $day_and_open_and_close=explode("#",$day_and_open_and_close_all_week[$j]);
                        $file[$i]["營業時間"][$j]=["close" =>["day" => $day_and_open_and_close[0] , "time" =>$day_and_open_and_close[2]],"open" =>[ "day" =>$day_and_open_and_close[0],"time"=>$day_and_open_and_close[1]]];
                    }
                }
                else{
                    $file[$i]['星等']='無星等資訊';
                    $file[$i]['營業時間']='無營業時間資訊';
                }
                $yes[]=$file[$i];
            }
            else{
                $file[$i]['星等']='';
                $file[$i]['營業時間']='';
                $no[]=$file[$i];
            }
        }
        return ["yes"=>$yes,"no"=>$no];
    }

    private function savefile($file){
        $open_file=fopen("1test_file.txt","a");
        // print_r($file);
        for($i=0;$i<count($file);$i++){
            $open_and_end="";
            if(@$file[$i]['營業時間']!="無營業時間資訊" and @$file[$i]['星等']!="無星等資訊"){
                for($j=0;$j<count($file[$i]['營業時間']);$j++){
                    $open_and_end.=$file[$i]['營業時間'][$j]['close']['day'];
                    $open_and_end.="#".$file[$i]['營業時間'][$j]['open']['time'];
                    if($j<(count($file[$i]['營業時間'])-1)){
                        $open_and_end.="#".$file[$i]['營業時間'][$j]['close']['time']."?";
                    }
                    else{
                        $open_and_end.="#".$file[$i]['營業時間'][$j]['close']['time'];
                    }
                }
            }
            else{
                $file[$i]["星等"]="無星等資訊";
                $open_and_end="無營業時間資訊";
            }
            // fwrite($open_file,$file[$i]["醫事機構名稱"].",".$file[$i]["醫事機構地址"].",".$file[$i]["醫事機構電話"].",".$file[$i]["成人口罩剩餘數"].",".$file[$i]["醫事機構地址"].",".$file[$i]["星等"].","."$open_and_end"."\n");
            fwrite($open_file,$file[$i]["醫事機構名稱"].",".$file[$i]["醫事機構地址"].",".$file[$i]["成人口罩剩餘數"].",".$file[$i]["醫事機構地址"].",".$file[$i]["星等"].","."$open_and_end"."\n");
        }
    }

    public function return_yes_or_no($raw_data){
        @$file=file("1test_file.txt");
        if(@$file){
            $hash_array=$this->make_hash($file);
            return $this->search_file($raw_data,$hash_array);
        }
        else{
            return ["yes"=>[],"no"=>$raw_data];
        }
    }
    
    public function save_data($no_already_ok){
        // $no_already_ok=$this->clean_data($no_already_ok);
        $this->savefile($no_already_ok);
    }

}