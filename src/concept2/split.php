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
        $this->metres = (int) $metres;
        $this->averageSR = (int) $averageSR;
        $this->averageHeartRate = (int) $averageHeartRate;
        $this->averagePace = $averagePace;;
    }

}