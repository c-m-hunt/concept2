<?php

namespace cmhunt\concept2;

class concept2 {

    private $file;
    private $name;
    private $workouts = [];
    private $allWorkouts = [];

    public function switchUser($name)
    {
        $this->workouts = $this->allWorkouts[$name];
    }

    public function getWorkouts()
    {
        return $this->workouts;
    }

    public function getName()
    {
        return $this->name;
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

    public function getWorkoutsByName($name)
    {
        $workouts = [];
        foreach ($this->workouts as $key=>$workout) {
            if ($workout->workoutName == $name) {
                $workouts[$key] = $workout;
            }
        }
        return $workouts;
    }

    public function loadData($data, $name = null)
    {
        $workouts = [];
        $workout = null;
        $this->name = trim($name);
        
        // Split data into array
        $data = explode("\n", $data);
        foreach ($data as $row) {
            $row = explode(',', $row);

            if (count($row) == 2 && $row[0] == 'Log Data for:') {
                if (!$this->name) {
                    $this->name = trim($row[1]);
                }
            }

            if (isset($row[1])) {
                $name = $row[1];
            }

            if (isset($row[5]) && isset($row[6]) && strlen($row[5]) > 0 && strlen($row[6]) > 0) {
                $workoutDate = \DateTime::createFromFormat('d/m/Y H:i', $row[2] . " " . $row[3]);
                if ($workoutDate) {
                    if ($workout instanceof workout) {
                        $workouts[$this->name][$this->getWorkoutHash($workout)] = $workout;
                    }
                    $workout = new workout($workoutDate, $row[4]);
                    $workout->username = $this->name;
                    $workout->name = $row[1];
                    $workout->time = $this->secondsFromTime($row[5]);
                    $workout->metres = (int) $row[6];
                    $workout->averageSPM = (int) $row[7];
                    $workout->averageHeartRate = (int) $row[8];
                    $workout->averagePace = $this->secondsFromTime($row[13]);
                    $workout->calPerHour = (int) $row[14];
                    $workout->averageWatt = (int) $row[15];
                    $workout->workoutId = $this->getWorkoutHash($workout);
                }
            }

            if (isset($row[9]) && isset($row[10]) && strlen($row[9]) > 0 && strlen($row[10]) > 0 && is_numeric($row[10])) {
                if ($workout instanceof workout) {
                    $split = new split($this->secondsFromTime($row[9]), $row[10], $row[11], $row[12], $this->secondsFromTime($row[13]));
                    $workout->addSplit(($split));
                }
            }
        }

        if (!is_null($workout) > 0) {
            $workouts[$this->name][$this->getWorkoutHash($workout)] = $workout;
        }        
        $this->allWorkouts = $workouts;
        $this->workouts = $workouts[$this->name];
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
            $seconds = $seconds + $workout->time;
        }
        return $seconds;
    }

    public function formatSecondsToTime($seconds, $inclHours = true)
    {    	    	
        $t = $seconds; // round($seconds);
        if ($t > 3600) { $inclHours = true; }
        if ($inclHours) {
            return sprintf('%02d:%02d:%02d.%01d', ($t/3600),($t/60%60), $t%60, fmod($t,1) * 10);  
        } else {
            return sprintf('%02d:%02d.%01d', ($t/60%60), $t%60, fmod($t,1) * 10);  
        }         
    }

    public function summaryByMonth()
    {
        return $this->summaryByDateSplit('M Y');
    }

    public function summaryByHourOfDay()
    {
        return $this->summaryByDateSplit('H');
    }

    public function summaryByDayOfMonth()
    {
        return $this->summaryByDateSplit('d');
    }

    public function summaryByDayOfWeek()
    {
        $days = $this->summaryByDateSplit('N');
        $dayMap = [1=>'Mon', 2=>'Tues', 3=>'Wed', 4=>'Thurs', 5=>'Fri', 6=>'Sat', 7=>'Sun'];
        ksort($days);
        $daysOut = [];
        foreach ($days as $dayId=>$day)
        {
            $daysOut[$dayMap[$dayId]] = $day;
        }
        return $daysOut;
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

    private function summaryByDateSplit($split) 
    {
        $summary = [];
        foreach ($this->workouts as $workout) {
            $dateSplit = $workout->date->format($split);
            if (!isset($summary[$dateSplit])) {
                $summary[$dateSplit] = ['distance' => 0, 'time' => 0];
            }
            $summary[$dateSplit]['distance'] += $workout->metres;
            $summary[$dateSplit]['time'] +=  $workout->time;
        }
        return $summary;
    }
}
