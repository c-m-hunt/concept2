<?php

namespace cmhunt\concept2;

class concept2 {

    private $file;
    private $workouts = [];

    public function getWorkouts()
    {
        return $this->workouts;
    }

    public function getWorkoutsByType($type)
    {
        $workouts = [];
        foreach ($this->workouts as $key=>$workout) {
            if ($workout->type == $type) {
                $workouts[$key] = $workout;
            }
        }
        return $workouts;
    }

    public function getWorkoutsByMonth($month, $year)
    {

        $workouts = [];
        foreach ($this->workouts as $key=>$workout) {
            if ($workout->date->format('Y') == $year && $workout->date->format('m') == $month) {
                $workouts[$key] = $workout;
            }
        }
        return $workouts;
    }

    public function loadData($data)
    {
        $workout = null;

        // Split data into array
        $data = explode("\n", $data);

        foreach ($data as $row) {
            $row = explode(',', $row);
            if (isset($row[5]) && isset($row[6]) && strlen($row[5] > 0 && strlen($row[6]) > 0)) {
                $workoutDate = \DateTime::createFromFormat('d/m/Y H:i', $row[2] . " " . $row[3]);
                if ($workoutDate) {
                    if ($workout instanceof workout) {

                        $this->workouts[$this->getWorkoutHash($workout)] = $workout;
                    }
                    $workout = new workout($workoutDate, $row[4]);
                    $workout->time = $row[5];
                    $workout->metres = $row[6];
                    $workout->averageSPM = $row[7];
                    $workout->averageHeartRate = $row[8];
                    $workout->averagePace = $row[13];
                    $workout->calPerHour = $row[14];
                    $workout->averageWatt = $row[15];
                }
            }

            if (isset($row[9]) && isset($row[10]) && strlen($row[9]) > 0 && strlen($row[10]) > 0 && is_numeric($row[10])) {
                if ($workout instanceof workout) {
                    $split = new split($row[9], $row[10], $row[11], $row[12], $row[13]);
                    $workout->addSplit(($split));
                }                
            }
        }
        $this->workouts[$this->getWorkoutHash($workout)] = $workout;
    }

    private function getWorkoutHash($workout)
    {
        $hash = md5($workout->date->format("Y-m-d H:i:s") . $workout->workoutName);
        return $hash;
    }

    public function totalMetres()
    {
        $metres = 0;
        foreach ($this->workouts as $workout) {
            $metres += $workout->metres;
        }
        return $metres;
    }

    public function totalTime()
    {
        $seconds = 0;
        foreach ($this->workouts as $workout) {
            $seconds = $seconds + $this->secondsFromTime($workout->time);
        }
        return $seconds;
    }

    public function formatSecondsToTime($seconds, $inclHours = true)
    {
        $t = round($seconds);
        if ($inclHours) {
            return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);  
        } else {
            return sprintf('%02d:%02d', ($t/60%60), $t%60);  
        }
         
    }

    public function timeByMonth()
    {
        return $this->timeByDateSplit('M Y');
    }

    public function metresByMonth()
    {
        $metresByMonth = $this->metresByDateSplit('M Y');

        /*
        foreach ($metresByMonth as $key=>&$month) {

            print_r($month);exit;

            $date = \DateTime::createFromFormat('M Y', $key);
            $month['month'] = $date->format('m');
            $month['year'] = $date->format('y');
        */
        return $metresByMonth;
    }

    public function metresByHourOfDay()
    {
        return $this->metresByDateSplit('H');
    }

    public function metresByDayOfMonth()
    {
        return $this->metresByDateSplit('d');
    }

    private function secondsFromTime($time)
    {
        $seconds = 0;
        $time = explode(':', $time);
        if (count($time) == 2) {
            $seconds = $seconds + ($time[0] * 60)  + $time[1];
        } else {
            $seconds = $seconds + ($time[0] * 60 * 60)+ ($time[1] * 60) + $time[2];   
        } 
        return $seconds;    
    }

    private function metresByDateSplit($split)
    {
        $metres = [];
        foreach ($this->workouts as $workout) {
            $dateSplit = $workout->date->format($split);
            if (!isset($metres[$dateSplit])) {
                $metres[$dateSplit] = 0;
            }
            $metres[$dateSplit] += $workout->metres;
        }
        return $metres;
    }

    private function timeByDateSplit($split)
    {
        $times = [];
        foreach ($this->workouts as $workout) {
            $dateSplit = $workout->date->format($split);
            if (!isset($times[$dateSplit])) {
                $times[$dateSplit] = 0;
            }
            $times[$dateSplit] += $this->secondsFromTime($workout->time);
        }

        return $times;
    }
}
