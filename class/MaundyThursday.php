<?php

class MaundyThursday extends Easter
{
    public function getName()
    {
        return 'Maundy Thursday';
    }

    public function getDate($year)
    {
        return $this->getMaundyThursday($year);
    }

    protected function getMaundyThursday($year)
    {
        $date = strtotime('-3 days', strtotime($this->getEaster($year)));
        return date('Y-m-d', $date);
    }
}

?>
