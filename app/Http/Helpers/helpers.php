<?php

function format_money ($number) {
    return number_format($number, 0, ',', ',');
}


function toWords($number)
{
    $number = abs($number);
    $words = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
    ];

    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    if ($number < 20) {
        $inWords = ' ' . $words[$number];
    } elseif ($number < 100) {
        $inWords = ' ' . $tens[(int)($number / 10)];
        if ($number % 10 !== 0) {
            $inWords .= ' ' . $words[$number % 10];
        }
    } elseif ($number < 1000) {
        $inWords = ' ' . $words[(int)($number / 100)] . ' Hundred';
        if ($number % 100 !== 0) {
            $inWords .= ' and' . toWords($number % 100);
        }
    } elseif ($number < 1000000) {
        $inWords = toWords((int)($number / 1000)) . ' Thousand';
        if ($number % 1000 !== 0) {
            $inWords .= toWords($number % 1000);
        }
    } elseif ($number < 1000000000) {
        $inWords = toWords((int)($number / 1000000)) . ' Million';
        if ($number % 1000000 !== 0) {
            $inWords .= toWords($number % 1000000);
        }
    } elseif ($number < 1000000000000) {
        $inWords = toWords((int)($number / 1000000000)) . ' Billion';
        if ($number % 1000000000 !== 0) {
            $inWords .= toWords($number % 1000000000);
        }
    } else {
        $inWords = 'Number is too large to convert.';
    }

    return $inWords;
}

function us_date($date, $show_day = true)
{
    $day_names  = array(
        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
    );
    $month_names = array(1 =>
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    );

    $year   = substr($date, 0, 4);
    $month   = $month_names[(int) substr($date, 5, 2)];
    $day = substr($date, 8, 2);
    $text    = '';

    if ($show_day) {
        $day_index = date('w', mktime(0,0,0, substr($date, 5, 2), $day, $year));
        $day_name        = $day_names[$day_index];
        $text       .= "$day_name, $month $day, $year";
    } else {
        $text       .= "$month $day, $year";
    }
    
    return $text; 
}
function add_zero_in_front($value, $threshold = null)
{
    return sprintf("%0". $threshold . "s", $value);
}