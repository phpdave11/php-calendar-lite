<?php

class Winter implements Holiday
{
    public function getName()
    {
        return 'Winter Starts';
    }

    public function getDate($year)
    {
        $calc = AstronomicalCalculation::getInstance();
        $ts = $calc->calcEquiSol(AstronomicalCalculation::WINTER, $year);
        return date('Y-m-d', $ts);
    }
}

?>
