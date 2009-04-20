<?php

class Spring implements Holiday
{
    public function getName()
    {
        return 'Spring Starts';
    }

    public function getDate($year)
    {
        $calc = AstronomicalCalculation::getInstance();
        $ts = $calc->calcEquiSol(AstronomicalCalculation::SPRING, $year);
        return date('Y-m-d', $ts);
    }
}

?>
