<?php

namespace cmhunt\concept2;

class split
{
    public $time;
    public $metres;
    public $averageSR;
    public $averageHeartRate;
    public $averagePace;

    public function __construct($time, $metres, $averageSR, $averageHeartRate, $averagePace)
    {
        $this->time = $time;
        $this->metres = $metres;
        $this->averageSR = $averageSR;
        $this->averageHeartRate = $averageHeartRate;
        $this->averagePace = $averagePace;;
    }

}


