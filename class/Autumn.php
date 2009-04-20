<?php

class Autumn implements Holiday
{
    public function getName()
    {
        return 'Autumn Starts';
    }

    public function getDate($year)
    {
        $calc = AstronomicalCalculation::getInstance();
        $ts = $calc->calcEquiSol(AstronomicalCalculation::AUTUMN, $year);
        return date('Y-m-d', $ts);
    }
}

?>
