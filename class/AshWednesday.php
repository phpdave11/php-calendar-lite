<?php

class AshWednesday extends Easter
{
    public function getName()
    {
        return 'Ash Wednesday';
    }

    public function getDate($year)
    {
        return $this->getAshWednesday($year);
    }

    protected function getAshWednesday($year)
    {
        $date = strtotime('-46 days', strtotime($this->getEaster($year)));
        return date('Y-m-d', $date);
    }
}

?>
