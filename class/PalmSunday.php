<?php

class PalmSunday extends Easter
{
    public function getName()
    {
        return 'Palm Sunday';
    }

    public function getDate($year)
    {
        return $this->getPalmSunday($year);
    }

    protected function getPalmSunday($year)
    {
        $date = strtotime('-7 days', strtotime($this->getEaster($year)));
        return date('Y-m-d', $date);
    }
}

?>
