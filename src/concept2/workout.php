<?php

namespace cmhunt\concept2;

class workout
{
    // Row types
    const TIME = 'time';
    const INTERVAL = 'interval';
    const DISTANCE = 'distance';


    public $date;
    public $workoutName;
    public $type;

    public $time;
    public $metres;
    public $averageSPM;
    public $averageHeartRate;

    public $averagePace;
    public $calPerHour;
    public $averageWatt;

    public $splits = [];

    public function __construct($date, $workoutName)
    {
        $this->date = $date;
        $this->workoutName = $workoutName;

        if (strPos($workoutName, 'r') > 0) {
            $this->type = self::INTERVAL;
        } else if (strPos($workoutName, 'm' )) {
            $this->type = self::DISTANCE;
        } else {
            $this->type = self::TIME;
        }
     }

    public function addSplit($split)
    {
        $this->splits[] = $split;
    }
}


