<?php

class AstronomicalCalculation
{
    const SPRING = 1;
    const SUMMER = 2;
    const AUTUMN = 3;
    const WINTER = 4;
    const VERNAL_EQUINOX = 1;
    const SUMMER_SOLSTICE = 2;
    const AUTUMNAL_EQUINOX = 3;
    const WINTER_SOLSTICE = 4;

    public $year;

    protected static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new AstronomicalCalculation;
        return self::$instance;
    }

    function setYear($y)
    {
        $this->year = $y;
    }
    
    function getYear()
    {
        return $this->year;
    }

    function setCurrentYear()
    {
        $this->setYear(idate('Y'));
    }
    
    // Moon Phase Calculation
    function calcMoonPhase()
    {
        $retval = array();
        $Y = $this->getYear();
        $R1 = M_PI / 180;
        $U = false;
        $K0 = null;
        $T = null;
        $T2 = null;
        $T3 = null;
        $J0 = null;
        $F0 = null;
        $J = null;
        $F = null;
        $M0 = null;
        $M1 = null;
        $B1 = null;
        $K9 = null;
        $K = null;
        $M5 = null;
        $M6 = null;
        $B6 = null;
        $str = null;
        $s = ""; // Formatted Output String
        $K0 = floor(($Y - 1900) * 12.3685);
        $T = ($Y - 1899.5) / 100;
        $T2 = $T * $T;
        $T3 = $T * $T * $T;
        $J0 = 2415020 + 29 * $K0;
        $F0 = 0.0001178 * $T2 - 0.000000155 * $T3;
        $F0 += (0.75933 + 0.53058868 * $K0);
        $F0 -= (0.000837 * $T + 0.000335 * $T2);
        $M0 = $K0 * 0.08084821133;
        $M0 = 360 * ($M0 - floor($M0)) + 359.2242;
        $M0 -= 0.0000333 * $T2;
        $M0 -= 0.00000347 * $T3;
        $M1 = $K0 * 0.07171366128;
        $M1 = 360 * ($M1 - floor($M1)) + 306.0253;
        $M1 += 0.0107306 * $T2;
        $M1 += 0.00001236 * $T3;
        $B1 = $K0 * 0.08519585128;
        $B1 = 360 * ($B1 - floor($B1)) + 21.2964;
        $B1 -= 0.0016528 * $T2;
        $B1 -= 0.00000239 * $T3;
        for ($K9 = 0; $K9 <= 28; $K9++)
        {
            $J = $J0 + 14 * $K9;
            $F = $F0 + 0.765294 * $K9;
            $K = $K9 / 2;
            $M5 = ($M0 + $K * 29.10535608) * $R1;
            $M6 = ($M1 + $K * 385.81691806) * $R1;
            $B6 = ($B1 + $K * 390.67050646) * $R1;
            $F -= 0.4068 * sin($M6);
            $F += (0.1734 - 0.000393 * $T) * sin($M5);
            $F += 0.0161 * sin(2 * $M6);
            $F += 0.0104 * sin(2 * $B6);
            $F -= 0.0074 * sin($M5 - $M6);
            $F -= 0.0051 * sin($M5 + $M6);
            $F += 0.0021 * sin(2 * $M5);
            $F += 0.0010 * sin(2 * $B6 - $M6);
            $F += 0.5 / 1440; //Adds 1/2 minute for proper rounding to minutes per Sky & Tel article
            $JDE = $J + $F;                          // Julian Empheris Day with fractions for time of day
            $TDT = $this->fromJDtoUTC($JDE);         // Convert Julian Days to TDT in a Date Object
            $UTC = $this->fromTDTtoUTC($TDT);        // Correct TDT to UTC, both as Date Objects    
            if ($this->getYear() == idate('Y', $UTC))
            {
                $str = date('Y-m-d', $UTC);
                if (!$U)
                    $retval[$str] = array('New Moon', '&#9679;');
                else
                    $retval[$str] = array('Full Moon', '&#9675;');
            }
            $U = !$U;
        }
        return $retval;
    }

    // Calculate and Display a single event for a single year (Either a Equiniox or Solstice)
    public function calcEquiSol($i, $year)
    {
        $k = $i - 1;
        $str = null;
        $JDE0 = $this->calcInitial($k, $year); // Initial estimate of date of event
        $T = ($JDE0 - 2451545.0) / 36525;
        $W = 35999.373 * $T - 2.47;
        $dL = 1 + 0.0334 * cos(deg2rad($W)) + 0.0007 * cos(deg2rad(2 * $W));
        $S = $this->periodic24($T);
        $JDE = $JDE0 + ((0.00001 * $S) / $dL);     // This is the answer in Julian Emphemeris Days
        $TDT = $this->fromJDtoUTC($JDE);                // Convert Julian Days to TDT in a Date Object
        $UTC = $this->fromTDTtoUTC($TDT);               // Correct TDT to UTC, both as Date Objects
        return $UTC;
    }
    
    // Calcualte an initial guess as the JD of the Equinox or Solstice of a Given Year
    // Valid for years 1000 to 3000
    public function calcInitial($k, $year)
    {
        $JDE0 = 0;
        $Y = ($year - 2000) / 1000;
        switch ($k)
        {
            case 0:
                $JDE0 = 2451623.80984 + 365242.37404 * $Y + 0.05169 * pow($Y, 2) - 0.00411 * pow($Y, 3) - 0.00057 * pow($Y, 4);
                break;
            case 1:
                $JDE0 = 2451716.56767 + 365241.62603 * $Y + 0.00325 * pow($Y, 2) + 0.00888 * pow($Y, 3) - 0.00030 * pow($Y, 4);
                break;
            case 2:
                $JDE0 = 2451810.21715 + 365242.01767 * $Y - 0.11575 * pow($Y, 2) + 0.00337 * pow($Y, 3) + 0.00078 * pow($Y, 4);
                break;
            case 3:
                $JDE0 = 2451900.05952 + 365242.74049 * $Y - 0.06223 * pow($Y, 2) - 0.00823 * pow($Y, 3) + 0.00032 * pow($Y, 4);
                break;
        }
        return $JDE0;
    }
    
    // Calculate 24 Periodic Terms
    public function periodic24($T)
    {
        $A = array(485, 203, 199, 182, 156, 136, 77, 74, 70, 58, 52, 50, 45, 44, 29, 18, 17, 16, 14, 12, 12, 12, 9, 8);
        $B = array(324.96, 337.23, 342.08, 27.85, 73.14, 171.52, 222.54, 296.72, 243.58, 119.81, 297.17, 21.02, 
                247.54, 325.15, 60.93, 155.12, 288.79, 198.04, 199.76, 95.39, 287.11, 320.81, 227.73, 15.45);
        $C = array(1934.136, 32964.467, 20.186, 445267.112, 45036.886, 22518.443, 
                65928.934, 3034.906, 9037.513, 33718.147, 150.678, 2281.226, 
                29929.562, 31555.956, 4443.417, 67555.328, 4562.452, 62894.029, 
                31436.921, 14577.848, 31931.756, 34777.259, 1222.114, 16859.074);
        $S = 0;
        for ($i = 0; $i < 24; $i++)
            $S += $A[$i] * cos(deg2rad($B[$i] + ($C[$i] * $T)));
        return $S;
    } 
    
    // Correct TDT to UTC
    public function fromTDTtoUTC($tobj)
    {
        // Correction lookup table has entry for every even year between TBLfirst and TBLlast
        $TBLfirst = 1620;
    
        // Range of years in lookup table
        $TBLlast = 2002; 
    
        // Corrections in Seconds
        $TBL = array(
            /*1620*/ 121,112,103, 95, 88,  82, 77, 72, 68, 63,  60, 56, 53, 51, 48,  46, 44, 42, 40, 38,
            /*1660*/  35, 33, 31, 29, 26,  24, 22, 20, 18, 16,  14, 12, 11, 10,  9,   8,  7,  7,  7,  7,
            /*1700*/   7,  7,  8,  8,  9,   9,  9,  9,  9, 10,  10, 10, 10, 10, 10,  10, 10, 11, 11, 11,
            /*1740*/  11, 11, 12, 12, 12,  12, 13, 13, 13, 14,  14, 14, 14, 15, 15,  15, 15, 15, 16, 16,
            /*1780*/  16, 16, 16, 16, 16,  16, 15, 15, 14, 13,  
            /*1800*/ 13.1, 12.5, 12.2, 12.0, 12.0,  12.0, 12.0, 12.0, 12.0, 11.9,  11.6, 11.0, 10.2,  9.2,  8.2,
            /*1830*/  7.1,  6.2,  5.6,  5.4,  5.3,   5.4,  5.6,  5.9,  6.2,  6.5,   6.8,  7.1,  7.3,  7.5,  7.6,
            /*1860*/  7.7,  7.3,  6.2,  5.2,  2.7,   1.4, -1.2, -2.8, -3.8, -4.8,  -5.5, -5.3, -5.6, -5.7, -5.9,
            /*1890*/ -6.0, -6.3, -6.5, -6.2, -4.7,  -2.8, -0.1,  2.6,  5.3,  7.7,  10.4, 13.3, 16.0, 18.2, 20.2,
            /*1920*/ 21.1, 22.4, 23.5, 23.8, 24.3,  24.0, 23.9, 23.9, 23.7, 24.0,  24.3, 25.3, 26.2, 27.3, 28.2,
            /*1950*/ 29.1, 30.0, 30.7, 31.4, 32.2,  33.1, 34.0, 35.0, 36.5, 38.3,  40.2, 42.2, 44.5, 46.5, 48.5,
            /*1980*/ 50.5, 52.5, 53.8, 54.9, 55.8,  56.9, 58.3, 60.0, 61.6, 63.0,  63.8, 64.3 /*2002 last entry*/
        );
    
        // Values for Delta T for 2000 thru 2002 from NASA
        $deltaT = 0; // deltaT = TDT - UTC (in Seconds)
        $Year = gmdate('Y', $tobj);
        $t = ($Year - 2000) / 100; // Centuries from the epoch 2000.0
    
        // Find correction in table
        if ($Year >= $TBLfirst && $Year <= $TBLlast)
        {
            if ($Year % 2) // Odd year - interpolate
            {
                $deltaT = ($TBL[($Year - $TBLfirst - 1) / 2] + $TBL[($Year - $TBLfirst + 1) / 2]) / 2;
            }
            else // Even year - direct table lookup
            {
                $deltaT = $TBL[($Year - $TBLfirst) / 2];
            }
        }
        else if ($Year < 948)
        { 
            $deltaT = 2177 + 497 * $t + 44.1 * pow($t, 2);
        }
        else if ($Year >= 948)
        {
            $deltaT =  102 + 102 * $t + 25.3 * pow($t, 2);
    
            // Special correction to avoid discontinurity in 2000
            if ($Year >= 2000 && $Year <= 2100)
            {
                $deltaT += 0.37 * ($Year - 2100);
            }
        }
        else
        {
            echo "Error: TDT to UTC correction not computed";
        }
        return ($tobj - $deltaT);
    }

    // Julian Date to UTC Date Object
    public function fromJDtoUTC($JD)
    {
        // JD = Julian Date, possible with fractional days
        // Output is a JavaScript UTC Date Object
        $A = null;
        $alpha = null;
        $Z = floor($JD + 0.5); // Integer JD's
        $F = ($JD + 0.5) - $Z;     // Fractional JD's
        if ($Z < 2299161)
        {
            $A = $Z;
        }
        else
        {
            $alpha = floor(($Z - 1867216.25) / 36524.25);
            $A = $Z + 1 + $alpha - floor($alpha / 4);
        }
        $B = $A + 1524;
        $C = floor(($B - 122.1) / 365.25);
        $D = floor(365.25 * $C);
        $E = floor(($B - $D) / 30.6001);
        $DT = $B - $D - floor(30.6001 * $E) + $F;    // Day of Month with decimals for time
        $Mon = $E - ($E < 13.5 ? 1 : 13);        // Month Number
        $Yr  = $C - ($Mon > 2.5 ? 4716 : 4715);  // Year    
        $Day = floor($DT);                        // Day of Month without decimals for time
        $H = 24 * ($DT - $Day);                    // Hours and fractional hours 
        $Hr = floor($H);                          // Integer Hours
        $M = 60 * ($H - $Hr);                      // Minutes and fractional minutes
        $Min = floor($M);                         // Integer Minutes
        $Sec = floor(60 * ($M - $Min));            // Integer Seconds (Milliseconds discarded)

        return gmmktime($Hr, $Min, $Sec, $Mon, $Day, $Yr);
    }
}

?>
