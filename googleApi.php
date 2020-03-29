<?php
require_once __DIR__ . "/vendor/autoload.php";
use SKAgarwal\GoogleApi\PlacesApi;

/**
 * This class handles the google information on pharmacies
 */
class GoogleInfoForPharmacy {
    /**
     * the data that has already been stored in our file
     * @param array
     */
    public $isInFile;
    /**
     * the data that has not been stored in our file
     * @var array
     */
    public $isNotInFile;
    private $googlePlaces;

    public function __construct(array $data){
        $this->isInFile = $data[0];
        $this->isNotInFile = $data[1];
    }
    /**
     * This method appends google information into the original data
     * @return array
     */
    public function appendGoogleInfo(){
        $place_details = $this->getPlaceDetail();
        if($place_details == array()){
            return $this->isInFile;
        }

        $isOpen = array();
        foreach($place_details as $detail){
            if(isset($detail['opening_hours']['open_now'])){
                $isOpen[] = $detail['opening_hours']['periods'];
            }else{
                $isOpen[] = $detail['opening_hours'][0];
            }
        }
        for($i = 0; $i < count($this->isNotInFile); $i++){
            $this->isNotInFile[$i]['星等'] = $place_details[$i]['rating'];
            $this->isNotInFile[$i]['營業時間'] = $isOpen[$i];
        }
        $appendDatas = array_merge($this->isNotInFile, $this->isInFile);
        usort($appendDatas, function($a, $b){
            if ($a['成人口罩剩餘數'] == $b['成人口罩剩餘數']) {
                return 0;
            }
            return ($a['成人口罩剩餘數'] > $b['成人口罩剩餘數']) ? -1 : 1;
        });

        // 處理現在是否營業中
        date_default_timezone_set("Asia/Taipei");
        $t = getdate();
        foreach($appendDatas as &$data){
            if(!is_string($data['營業時間'])){
                $now = $t['hours']. $t['minutes'];
                if(isset($data['營業時間'][$t['wday'] % 7])){
                    $close = $data['營業時間'][$t['wday'] % 7]['close']['time'];
                    $open = $data['營業時間'][$t['wday'] % 7]['open']['time'];
                    if((int) $now < (int) $close && (int) $now >= (int) $open){
                        $data['營業時間'] = '營業中，到'. $data['營業時間'][$t['wday'] % 7]['close']['time'];
                    }
                }else{
                    $data['營業時間'] = '目前沒有營業';
                }
            }
        }
        return $appendDatas;
    }
    /**
     * This method calls google place API to obtain place_ids
     * @return array
     */
    private function getPlaceId(){
        if($this->isNotInFile == array()){
            return array();
        }
        $places = array_map(function($val){
            return $val['醫事機構名稱']. " ". $val['醫事機構地址'];
        }, $this->isNotInFile);
        $place_ids = array();
        foreach($places as $place){
            $info = $this->googlePlaces->findPlace($place, "textquery", ['language'=>'zh_TW'])->all();
            $place_id = array_map(function($val){
                return $val['place_id'];
            }, $info['candidates']->all())[0];
            
            $place_ids[] = $place_id;
        }
        return $place_ids;
    }
    /**
     * This method calls google place API using place_ids to find place details
     * @return array
     */
    private function getPlaceDetail(){
        $place_ids = $this->getPlaceId();
        if($place_ids == array()){
            return array();
        }
        $place_details = array();
        foreach($place_ids as $place_id){
            $details = $this->googlePlaces->placeDetails($place_id, ['language'=>'zh_TW'])->all()['result'];
            $openingHours = (isset($details['opening_hours']))? $details['opening_hours']: ["無營業時間資訊"];
            $rating = (isset($details['rating']))? $details['rating']: "無星等資訊";
            $place_details[] = array
                (
                    'opening_hours' => $openingHours,
                    'rating' => $rating,
                );
        }
        return $place_details;
    }
    public function setAccessKey(string $key){
        // ACCESS_KEY is required
        $ACCESS_KEY = $key;
        $this->googlePlaces = new PlacesApi($ACCESS_KEY);
    }
}