<?php

class GoodFriday extends Easter
{
    public function getName()
    {
        return 'Good Friday';
    }

    public function getDate($year)
    {
        return $this->getGoodFriday($year);
    }

    protected function getGoodFriday($year)
    {
        $date = strtotime('-2 days', strtotime($this->getEaster($year)));
        return date('Y-m-d', $date);
    }
}

?>
