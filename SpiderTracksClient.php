<?php

class SpiderTracksClient
{
    protected $username;
    protected $password;
    protected $sysId;
    
    protected static $url = 'https://go.spidertracks.com/api/aff/feed';
    
    public function __construct($username, $password, $sys_id = 'SpiderTracksPHP') {
        $this->username = $username;
        $this->password = $password;
        $this->sysId = $sys_id;
    }
    
    /** retrieves and array-formats position data collected since $sinceDate **/
    public function getSince(DateTime $sinceDate) {
        $requestBody = '<?xml version="1.0" encoding="utf-8"?>
            <data xmlns="https://aff.gov/affSchema" sysId="'.$this->sysId.'" rptTime="'.date('c').'" version="2.23"> 
             <msgRequest to="spidertracks" from="'.$this->sysId.'" msgType="Data Request" subject="Async"
             dateTime="'.$sinceDate->format('c').'"><body>'.$sinceDate->format('c').'</body></msgRequest></data>';     
        
        $ch = curl_init(self::$url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SpiderTracks PHP Library 0.1');
        
        $output = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($responseCode === 401) {
            throw new RuntimeException('Authentication failed; use your SpiderTracks GO username and password.', 401);
        } else if ($responseCode !== 200) {
            throw new RuntimeException($output, $responseCode);
        }
        
        curl_close($ch);
        
        return self::format($output);
    }
    
    /** Takes the body from the SpiderTracks response and returns an array of stdClass objects with appropriate response data **/
    public static function format($response_body) {
        $xml = simplexml_load_string($response_body);
        
        $output = array();
        foreach ($xml->posList->acPos as $position) {
            $output[] = (object) array(
                'imei' => (string) $position['esn'],
                'date' => new DateTime((string) $position['dateTime']),
                'latitude' => (float) $position->Lat,
                'longitude' => (float) $position->Long,
                'altitude' => (float) $position->altitude,
                'speed' => (float) $position->speed,
                'heading' => (float) $position->heading
            );
        }
    
        return $output;
    }
}
