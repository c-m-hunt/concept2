<?php

namespace concept2;

class split
{
    public $time;
    public $metres;
    public $averageSR;
    public $averageHeartRate;

    public function __construct($time, $metres, $averageSR, $averageHeartRate)
    {
        $this->time = $time;
        $this->metres = $metres;
        $this->averageSR = $averageSR;
        $this->averageHeartRate = $averageHeartRate;
    }

}


