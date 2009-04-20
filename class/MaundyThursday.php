<?php

class MaundyThursday implements Holiday
{
    private $moonPhases;

    public function __construct($moonPhases)
    {
        $this->moonPhases = $moonPhases;
    }

    public function getName()
    {
        return 'Maundy Thursday';
    }

    public function getDate($year)
    {
        return $this->getMaundyThursday($year);
    }

    private function getMaundyThursday($year)
    {
        $calc = AstronomicalCalculation::getInstance();
        $date = $calc->calcEquiSol(AstronomicalCalculation::SPRING, $year);
        foreach ($this->moonPhases as $phaseDate => $phaseArray)
        {
            if ($phaseArray[0] == 'Full Moon' && strtotime($phaseDate) >= $date)
            {
                $date = strtotime($phaseDate);
                break;
            }
        }
        $day = idate('j', $date);
        $month = idate('n', $date);
        $year = idate('Y', $date);
        while (date('N', $date) < 7)
        {
            $date = mktime(0, 0, 0, $month, ++$day, $year);
        }
        $date = mktime(0, 0, 0, $month, $day - 3, $year);
        return date('Y-m-d', $date);
    }
}

?>
