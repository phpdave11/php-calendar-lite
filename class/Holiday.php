<?php

class Holiday
{
    public $id;
    public $name;
    public $after_solstice_or_equinox;
    public $after_moon_phase;
    public $day_of_week;
    public $week_order;
    public $week;
    public $month;
    public $day;
    public $day_offset;
    public $until_dst_value;

    public function getDate($year, $moonPhases)
    {
        $date = null;

        if (!empty($this->after_solstice_or_equinox))
        {
            $calc = AstronomicalCalculation::getInstance();
            switch ($this->after_solstice_or_equinox)
            {
                case 'vernal equinox':
                    $date = $calc->calcEquiSol(AstronomicalCalculation::VERNAL_EQUINOX, $year);
                    break;
                case 'summer solstice':
                    $date = $calc->calcEquiSol(AstronomicalCalculation::SUMMER_SOLSTICE, $year);
                    break;
                case 'autumnal equinox':
                    $date = $calc->calcEquiSol(AstronomicalCalculation::AUTUMNAL_EQUINOX, $year);
                    break;
                case 'winter solstice':
                    $date = $calc->calcEquiSol(AstronomicalCalculation::WINTER_SOLSTICE, $year);
                    break;
            }
        }

        if (!empty($this->after_moon_phase))
        {
            foreach ($moonPhases as $phaseDate => $phaseArray)
            {
                if (strtolower($phaseArray[0]) == $this->after_moon_phase && strtotime($phaseDate) >= $date)
                {
                    $date = strtotime($phaseDate);
                    break;
                }
            }
        }

        if (!empty($this->day_of_week) && !empty($this->month) && !empty($this->week) && $this->week_order == 'first')
        {
            $day = 1;
            $date = mktime(12, 0, 0, $this->month, $day, $year);
            while (date('l', $date) != $this->day_of_week)
                $date = mktime(12, 0, 0, $this->month, ++$day, $year);
            $date = mktime(12, 0, 0, $this->month, $day + ($this->week - 1) * 7, $year);
        }
        elseif (!empty($this->day_of_week) && !empty($this->month) && !empty($this->week) && $this->week_order == 'last')
        {
            $day = date('t', mktime(12, 0, 0, $this->month, 1, $year)); // number of days in month (28-31)
            $date = mktime(12, 0, 0, $this->month, $day, $year);
            while (date('l', $date) != $this->day_of_week)
                $date = mktime(12, 0, 0, $this->month, --$day, $year);
            $date = mktime(12, 0, 0, $this->month, $day - ($this->week - 1) * 7, $year);
        }
        elseif (!empty($this->month) && !empty($this->day) && strlen($this->until_dst_value) == 1)
        {
            $day = $this->day;
            do
            {
                $date = mktime(12, 0, 0, $this->month, $day++, $year);
            }
            while (date('I', $date) != $this->until_dst_value);
        }
        elseif (!empty($this->month) && !empty($this->day))
        {
            $date = mktime(12, 0, 0, $this->month, $this->day, $year);
        }
        elseif (!empty($this->day_of_week))
        {
            $day = idate('j', $date);
            $month = idate('n', $date);
            while (date('l', $date) != $this->day_of_week)
                $date = mktime(0, 0, 0, $month, ++$day, $year);
        }

        if (!empty($this->day_offset))
        {
            $date = strtotime($this->day_offset . ' days', $date);
        }

        return date('Y-m-d', $date);
    }
}

?>
