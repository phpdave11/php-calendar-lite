<?php

class TalkLikeAPirateDay implements Holiday
{
    public function getName()
    {
        return 'Talk Like a Pirate Day';
    }

    public function getDate($year)
    {
        $ts = mktime(12, 0, 0, 9, 19, $year);
        return date('Y-m-d', $ts);
    }
}

?>
