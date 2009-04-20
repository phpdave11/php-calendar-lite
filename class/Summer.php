<?php

class Summer implements Holiday
{
    public function getName()
    {
        return 'Summer Starts';
    }

    public function getDate($year)
    {
        $calc = AstronomicalCalculation::getInstance();
        $ts = $calc->calcEquiSol(AstronomicalCalculation::SUMMER, $year);
        return date('Y-m-d', $ts);
    }
}

?>
