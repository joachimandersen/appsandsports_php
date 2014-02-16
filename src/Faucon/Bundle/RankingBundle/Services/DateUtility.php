<?php

namespace Faucon\Bundle\RankingBundle\Services;
/**
 * Description of DateUtility
 *
 * @author jfa
 */
class DateUtility 
{
    // convert a date into a string that tells how long ago that date was.... eg: 2 days ago, 3 minutes ago.
    public function ago($date)
    {
	$c = getdate();
	$p = array('year', 'mon', 'mday', 'hours', 'minutes', 'seconds');
	$display = array('år', 'måneder', 'dage', 'timer', 'minutter', 'sekunder');
	$factor = array(0, 12, 30, 24, 60, 60);
	$d = $this->datetoarr($date);
	for ($w = 0; $w < 6; $w++) {
		if ($w > 0) {
			$c[$p[$w]] += $c[$p[$w-1]] * $factor[$w];
			$d[$p[$w]] += $d[$p[$w-1]] * $factor[$w];
		}
		if ($c[$p[$w]] - $d[$p[$w]] > 1) { 
                    return ($c[$p[$w]] - $d[$p[$w]]).' '.$display[$w].' siden';
		}
	}
	return '';
    }
    
    private function datetoarr($d) {
	preg_match("/([0-9]{4})(\\-)([0-9]{2})(\\-)([0-9]{2}) ([0-9]{2})(\\:)([0-9]{2})(\\:)([0-9]{2})/", $d, $matches);
        return array( 
		'seconds' => $matches[10], 
		'minutes' => $matches[8], 
		'hours' => $matches[6],  
		'mday' => $matches[5], 
		'mon' => $matches[3],  
		'year' => $matches[1], 
	);
    }
}

?>
