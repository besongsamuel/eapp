<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class uses google API to 
 * calculate distance between two points
 */
class Geo {

    /**
     * This method extracts longitude and latitude address coordinates
     * for a given address using google api
     * @param type $city The address city
     * @param type $street The street
     * @param type $province the province
     * @return array
     */
    function get_coordinates($city, $street, $state, $country)
    {
        $street = rtrim($street, ',');
        $address = urlencode($city.','.rtrim($street, ',').','.$state);
        $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=".trim($country);
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_PROXYPORT, 3128);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl_handle);
        curl_close($curl_handle);
        $response_a = json_decode($response);
        $status = $response_a->status;

        if ( $status != 'ZERO_RESULTS' )
        {
            $return = array('lat' => $response_a->results[0]->geometry->location->lat, 'long' => $response_a->results[0]->geometry->location->lng);
            return $return;
        }
        
        return FALSE;
    }
    
    /**
     * This method gets a driving distance between two points
     * @param type $lat1 the latitude of the first address
     * @param type $lat2 the latitude of the second address
     * @param type $long1 the longitude of the first address
     * @param type $long2 the longitude of the second address
     * @return type an array containing the distance and time
     */
    function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving&language=en-US";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

        return array('distance' => $dist, 'time' => $time);
    }
    
    /**
     * This method calculates the driving distance between two points. 
     * @param type $source The home address
     * @param type $destination The destination address
     * @return array containing 'distance' and 'time'
     */
    public function distance_time_between($user_profile, $department_store) 
    {
        set_error_handler(function(){ });
        
        $coordinates1 = $this->get_coordinates($user_profile->city, $user_profile->address, $user_profile->state, $user_profile->country);
        $coordinates2 = $this->get_coordinates($department_store->city, $department_store->address, $department_store->state, $department_store->country); 
        
        if ( !$coordinates1 || !$coordinates2 )
        {
            restore_error_handler();
            return array('distance' => 0, 'time' => 0);
            
        }
        else
        {
            $dist = $this->GetDrivingDistance($coordinates1['lat'], $coordinates2['lat'], $coordinates1['long'], $coordinates2['long']);
            restore_error_handler();
            return $dist;
        }
    }
}
