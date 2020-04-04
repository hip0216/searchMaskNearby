<?php
namespace SmallFreshMeat\GoogleApi;

require "vendor/autoload.php";
use SKAgarwal\GoogleApi\PlacesApi;



/**
 * This class handles the google information on pharmacies
 */
class GoogleInfoForPharmacy {
    /**
     * the data that has already been stored in our file
     * @var array
     */
    public $isInFile;
    /**
     * the data that has not been stored in our file
     * @var array
     */
    public $isNotInFile;
    /**
     * the data that append google info
     * @var array
     */
    public $appendDatas;
    private $googlePlaces;

    public function __construct(string $key = NULL) {
        if($key) {
            $this->setAccessKey($key);
        }
    }
    /**
     * This method appends google information into the original data
     * @return array
     */
    public function appendGoogleInfo($data) {
        $this->isInFile = $data['yes'];
        $this->isNotInFile = $data['no'];

        // print_r($this->isNotInFile); exit;
        $place_details = $this->getPlaceDetail();
        if($place_details == array()) {
            $this->isInFile;
        }

        $isOpen = array();
        foreach($place_details as $detail) {
            if(isset($detail['opening_hours']['open_now'])) {
                $isOpen[] = $detail['opening_hours']['periods'];
            }else {
                $isOpen[] = $detail['opening_hours'][0];
            }
        }
        for($i = 0; $i < count($this->isNotInFile); $i++) {
            $this->isNotInFile[$i]['星等'] = $place_details[$i]['rating'];
            $this->isNotInFile[$i]['營業時間'] = $isOpen[$i];
        }
        $appendDatas = array_merge($this->isNotInFile, $this->isInFile);
        usort($appendDatas, function($a, $b) {
            if ($a['成人口罩'] == $b['成人口罩']) {
                return 0;
            }
            return ($a['成人口罩'] > $b['成人口罩']) ? -1 : 1;
        });

        // 處理現在是否營業中
        date_default_timezone_set("Asia/Taipei");
        $t = getdate();
        foreach($appendDatas as &$data) {
            if(!is_string($data['營業時間'])) {
                $now = (int) $t['hours']. $t['minutes'];
                $thisDay = $t['wday'] % 7;
                
                $close = array_values(array_map(function($val) {
                    return (int) $val['close']['time'];
                },array_filter($data['營業時間'], function($val) use($thisDay) {
                    return $val['close']['day'] == $thisDay;
                })));
                $open = array_values(array_map(function($val){
                    return (int) $val['open']['time'];
                },array_filter($data['營業時間'], function($val) use($thisDay) {
                    return $val['open']['day'] == $thisDay;
                })));
            
                for($i = 0; $i < count($close); $i++) {
                    if($now < $close[$i] && $now >= $open[$i]) {
                        $data['營業時間'] = '營業中，到'. $close[$i];
                        break;
                    }else {
                        $data['營業時間'] = '目前沒有營業';
                    }
                }
            }
        }
        $this->appendDatas = $appendDatas;
    }
    /**
     * This method calls google place API to obtain place_ids
     * @return array
     */
    private function getPlaceId() {
        if($this->isNotInFile == array()) {
            return array();
        }
        $places = array_map(function($val) {
            return $val['機構名稱']. " ". $val['機構地址'];
        }, $this->isNotInFile);
        $place_ids = array();
        foreach($places as $place) {
            $info = $this->googlePlaces->findPlace($place, "textquery", ['language'=>'zh_TW'])->all();
            $place_id = array_map(function($val) {
                return $val['place_id'];
            }, $info['candidates']->all());
            
            $place_ids[] = isset($place_id[0])? $place_id[0]: '0';
        }
        return $place_ids;
    }
    /**
     * This method calls google place API using place_ids to find place details
     * @return array
     */
    private function getPlaceDetail() {
        $place_ids = $this->getPlaceId();
        if($place_ids == array()) {
            return array();
        }
        $place_details = array();
        foreach($place_ids as $place_id) {
            if($place_id !== '0') {
                $details = $this->googlePlaces->placeDetails($place_id, ['language'=>'zh_TW'])->all()['result'];
                $openingHours = (isset($details['opening_hours']))? $details['opening_hours']: ["無營業時間資訊"];
                $rating = (isset($details['rating']))? $details['rating']: "無星等資訊";
                $place_details[] = array
                    (
                        'opening_hours' => $openingHours,
                        'rating' => $rating,
                    );
            }else {
                $place_details[] = array
                    (
                        'opening_hours' => ['無營業時間資訊'],
                        'rating' => '無星等資訊',
                    );
            }
        }
        return $place_details;
    }
    public function setAccessKey(string $key) {
        // ACCESS_KEY is required
        $ACCESS_KEY = $key;
        $this->googlePlaces = new PlacesApi($ACCESS_KEY);
    }
}