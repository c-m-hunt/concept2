<?php

namespace cmhunt\concept2;

class concept2 {

    private $file;
    private $workouts = [];

    public function getWorkous($type = null)
    {
        if ($type == null) {
            return $this->workouts;
        } else {
            $workouts = [];
            foreach ($this->workouts as $workout) {
                if ($workout->type == $type) {
                    $workouts[] = $workout;
                }
            }
            return $workouts;
        }
    }

    public function loadFile($file)
    {
        $this->file = $file;
        $file = fopen($file,"r");
        $data = [];
        while(! feof($file))
        {
            $data[] = (fgetcsv($file));
        }
        fclose($file);

        $workout = null;

        foreach ($data as $row) {
            if (isset($row[5]) && isset($row[6]) && strlen($row[5] > 0 && strlen($row[6]) > 0)) {
                $workoutDate = \DateTime::createFromFormat('d/m/Y H:i', $row[2] . " " . $row[3]);
                if ($workoutDate) {
                    if ($workout instanceof workout) {
                        $this->workouts[] = $workout;
                    }
                    $workout = new workout($workoutDate, $row[4]);
                    $workout->time = $row[5];
                    $workout->metres = $row[6];
                    $workout->averageSPM = $row[7];
                    $workout->averageHearRate = $row[8];
                    $workout->averagePace = $row[13];
                    $workout->calPerHour = $row[14];
                    $workout->averageWatt = $row[15];
                }
            }

            if (isset($row[9]) && isset($row[10]) && strlen($row[9] > 0 && strlen($row[10]) > 0)) {
                $split = new split($row[9], $row[10], $row[11], $row[12]);
                $workout->addSplit(($split));
            }
        }
        $this->workouts[] = $workout;
    }

    public function totalMetres()
    {
        $metres = 0;
        foreach ($this->workouts as $workout) {
            $metres += $workout->metres;
        }
        return $metres;
    }

    public function metresByMonth()
    {
        return $this->metresByDateSplit('M Y');
    }

    public function metresByHourOfDay()
    {
        return $this->metresByDateSplit('H');
    }

    public function metresByDayOfMonth()
    {
        return $this->metresByDateSplit('d');
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
}