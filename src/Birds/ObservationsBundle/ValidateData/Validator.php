<?php

namespace Birds\ObservationsBundle\ValidateData;

class Validator
{
    /**
     * Test fonction to avoid bad data
     * @param $date
     * @return bool|\DateTime|null
     */
    function matchDate($date)
    {
        $pattern = '/^[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        if(!preg_match($pattern, $date))
            return null;
        return \DateTime::createFromFormat("Y-m-d",$date);

    }



    /**
     * Test fonction to avoid bad data
     * @param $hour : string or int
     * @param $default
     * @return int
     */
    function matchHours($hour, $default)
    {
        //Test de valeur
        $hour = intval($hour);

        if(!is_int($hour))
            $hour = $default;


        if($hour<0)
            $hour = 0;
        if($hour > 23)
            $hour = 23;

        return $hour;

    }

}