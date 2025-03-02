<?php
	
	//meditations params
	// UTA timezone
	class meditations{
	
		public 
			$firstDate, 
			$secondDate,
			$thirdDate, 
			$fourthDate;
			//set next months meditation 
		public
			$nmFirstDate, 
			$nmSecondDate;
			//set next peace meditation display dates
		public
			$nextFirstDate, 
			$nextFirstDateFirstTime, //5:30 utc
			$nextFirstDateSecondTime, //7:00 utc
			$nextSecondDate,
			$nextSecondDateFirstTime; //7:00 utc
			//set top display dates	
		public $nextYear;
		public $nextMonth,$monthName;
			
		
			
		function __construct()
		{
			//set the dates
			// select the timezone for your countdown
			//$timezone = trim($_GET['timezone']);
			//putenv("TZ=$timezone");

		}			
		
		function days_in_month($month, $year){
			// calculate number of days in a month
			return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
		} 

		//month  = $m
		function setDates($m = NULL){
			
			$date = time();

			$day = date('d',$date); 
			$month = date('m',$date);
			$year = date('Y',$date);
			
			$firstDay = mktime(0,0,0,$month, 1, $year);
			
			$monthName = date('F',$firstDay);
			
			$theFirstOfTheMonth = date('D',$firstDay);
			
			//die($theFirstOfTheMonth);
			// $numberOfDaysInMonth = cal_days_in_month(0,$month,$year);
			$numberOfDaysInMonth = $this->days_in_month($month, $year);
			
			$HasStarted = false;
			$counter = 0;
			$counterRange = 15;
			
			for($i=1;$i<=$numberOfDaysInMonth;$i++)
			{
				$dayName;
				$dayName = date('D', mktime(0,0,0,$month, $i, $year));
				
				//print $dayName. .$counter;
				if($dayName=='Sat' || $dayName=='Sun'){
					
					if($dayName=='Sat' && $counter == 0){
					 $HasStarted = true;	
					 $this->firstDate = $i;			
					}elseif($dayName=='Sun' && $counter == 1){
					 $this->secondDate = $i;	
					}elseif($dayName=='Sat' && $counter == 14){
					 $this->thirdDate = $i;			
					}elseif($dayName=='Sun' && $counter == 15){
					 $this->fourthDate = $i;	
					}
					
				}
				
				if($HasStarted == true){
				   $counter++;	
				}
				$this->monthName = $month;
				
			}
			

			//if today is past the fourth day meditation
			//set the next group
			
			
			//day is today
			if($day > $this->fourthDate){
			
			
					//die( $year . "     ".$month);
					
					if($month == 12)
						$year = date("Y",strtotime($year."-".$month."+1 years"));
					else
						$year = date("Y",strtotime($year."-".$month));
					
					$month =  date("m",strtotime($year."-".$month."-01 +1 months"));
					
					
						
					
					//$year = date("Y",strtotime($year."-".$month));
					
					
					//die( $year . "     ".$month);
					
					
					
					
					
					//$numberOfDaysInMonth = cal_days_in_month(CAL_GREGORIAN,$month,$year);
					$numberOfDaysInMonth = $this->days_in_month($month,$year);
			 
					$HasStarted = false;
					$counter = 0;
					$counterRange = 15;
					
					for($i=1;$i<=$numberOfDaysInMonth;$i++)
					{
						$dayName;
						$dayName = date('D', mktime(0,0,0,$month, $i, $year));
						
						//print $dayName. .$counter;
						if($dayName=='Sat' || $dayName=='Sun'){
							
							if($dayName=='Sat' && $counter == 0){
							 $HasStarted = true;	
							 $this->firstDate = $i;			
							}elseif($dayName=='Sun' && $counter == 1){
							 $this->secondDate = $i;	
							}elseif($dayName=='Sat' && $counter == 14){
							 $this->thirdDate = $i;			
							}elseif($dayName=='Sun' && $counter == 15){
							 $this->fourthDate = $i;	
							}
							
						}
						
						if($HasStarted == true)
						   $counter++;	
						
					}

				$this->monthName = $month;

				$this->nextFirstDate = $this->firstDate;
				$this->nextSecondDate = $this->secondDate;		

			}elseif($day <= $this->fourthDate && $day > $this->secondDate){
				
				$this->nextFirstDate = $this->thirdDate;
				$this->nextSecondDate = $this->fourthDate;
				
			}else{
				
				$this->nextFirstDate = $this->firstDate;
				$this->nextSecondDate = $this->secondDate;				
				
			}	
			
		
		
		$this->nextFirstDateFirstTime = $year.'-'.$month.'-'.$this->nextFirstDate.' 17:30:00';   //YYYY-MM-DD HH:MM:SS
		$this->nextFirstDateSecondTime = $year.'-'.$month.'-'.$this->nextFirstDate.' 19:00:00';   //YYYY-MM-DD HH:MM:SS
		$this->nextSecondDateFirstTime = $year.'-'.$month.'-'.$this->nextSecondDate.' 19:00:00';   //YYYY-MM-DD HH:MM:SS

		}
		







		
		// Date difference function. Will be using below
		public function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
		  /*
			$interval can be:
			yyyy - Number of full years
			q - Number of full quarters
			m - Number of full months
			y - Difference between day numbers
			  (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
			d - Number of full days
			w - Number of full weekdays
			ww - Number of full weeks
			h - Number of full hours
			n - Number of full minutes
			s - Number of full seconds (default)
		  */
		  
		  if (!$using_timestamps) {
			$datefrom = strtotime($datefrom, 0);
			$dateto = strtotime($dateto, 0);
		  }
		  $difference = $dateto - $datefrom; // Difference in seconds
		   
		  switch($interval) {
		   
			case 'yyyy': // Number of full years
		
			  $years_difference = floor($difference / 31536000);
			  if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
				$years_difference--;
			  }
			  if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
				$years_difference++;
			  }
			  $datediff = $years_difference;
			  break;
		
			case "q": // Number of full quarters
		
			  $quarters_difference = floor($difference / 8035200);
			  while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			  }
			  $quarters_difference--;
			  $datediff = $quarters_difference;
			  break;
		
			case "m": // Number of full months
		
			  $months_difference = floor($difference / 2678400);
			  while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
				$months_difference++;
			  }
			  $months_difference--;
			  $datediff = $months_difference;
			  break;
		
			case 'y': // Difference between day numbers
		
			  $datediff = date("z", $dateto) - date("z", $datefrom);
			  break;
		
			case "d": // Number of full days
		
			  $datediff = floor($difference / 86400);
			  break;
		
			case "w": // Number of full weekdays
		
			  $days_difference = floor($difference / 86400);
			  $weeks_difference = floor($days_difference / 7); // Complete weeks
			  $first_day = date("w", $datefrom);
			  $days_remainder = floor($days_difference % 7);
			  $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
			  if ($odd_days > 7) { // Sunday
				$days_remainder--;
			  }
			  if ($odd_days > 6) { // Saturday
				$days_remainder--;
			  }
			  $datediff = ($weeks_difference * 5) + $days_remainder;
			  break;
		
			case "ww": // Number of full weeks
		
			  $datediff = floor($difference / 604800);
			  break;
		
			case "h": // Number of full hours
		
			  $datediff = floor($difference / 3600);
			  break;
		
			case "n": // Number of full minutes
		
			  $datediff = floor($difference / 60);
			  break;
		
			default: // Number of full seconds (default)
		
			  $datediff = $difference;
			  break;
		  }    
		
		  return $datediff;
		}



	public function nToMonth($nm){
		$stringmonth = '';
	
				switch(trim($nm))
				{
					case "01":
						$stringmonth = "January";
					break;
					case "02":
						$stringmonth = "February";
					break;
					case "03":
						$stringmonth = "March";
					break;
					case "04":
						$stringmonth = "April";
					break;
					case "05":
						$stringmonth = "May";
					break;
					case "06":
						$stringmonth = "June";
					break;
					case "07":
						$stringmonth = "July";
					break;
					case "08":
						$stringmonth = "August";
					break;
					case "09":
						$stringmonth = "September";
					break;
					case "10":
						$stringmonth = "October";
					break;
					case "11":
						$stringmonth = "November";
					break;
					case "12":
						$stringmonth = "December";
					break;
				}
		
		return $stringmonth;
	}
}

	 
	$m = new meditations;
	$m->setDates();
	
/*	print $m->nextFirstDateFirstTime.'<br/>'.
		  $m->nextFirstDateSecondTime.'<br/>'.
		  $m->nextSecondDateFirstTime.'<br/>';
*/		  
	$date1 = $m->nextFirstDate;
	$med1 = $m->datediff("s",gmdate("Y-m-d H:i:s"),$m->nextFirstDateFirstTime);
	$med2 = $m->datediff("s",gmdate("Y-m-d H:i:s"),$m->nextFirstDateSecondTime);

	$date2 = $m->nextSecondDate;
	$med3 = $m->datediff("s",gmdate("Y-m-d H:i:s"),$m->nextSecondDateFirstTime);

	$nMonth = $m->nToMonth($m->monthName);
		
?>
<script type="text/javascript">
// Here’s where the Javascript starts
var med1 = <?php print $med1 ?>;
var med2 = <?php print $med2 ?>;
var med3 = <?php print $med3 ?>;

var nMonth = '<?php print $nMonth ?>';

// Converting date difference from seconds to actual time
function convert_to_time(secs) {
    secs = parseInt(secs);
    hh = secs / 3600;
    hh = parseInt(hh);
    mmt = secs - (hh * 3600);
    mm = mmt / 60;
    mm = parseInt(mm);
    ss = mmt - (mm * 60);
    //day go down one
    dd = hh / 24;
    dd = parseInt(dd);

    if (hh > 23) {
        hh = hh - (dd * 24);
    } else {
        dd = 0;
    }

    if (ss < 10) {
        ss = "0" + ss;
    }
    if (mm < 10) {
        mm = "0" + mm;
    }
    if (hh < 10) {
        hh = "0" + hh;
    }
    if (dd == 0) {
        return (hh + ":" + mm + ":" + ss);
    } else {
        if (dd > 1) {
            return ("<b>" + dd + "</b> Days <b>" + hh + "</b> Hours <b>" + mm + "</b> Minutes <b>" + ss +
                "</b> Seconds");
        } else {
            return ("<b>" + dd + "</b> Day <b>" + hh + "</b> Hours <b>" + mm + "</b> Minutes <b>" + ss +
            "</b> Seconds");
        }
    }
}

// Our function that will do the actual countdown
function do_cd() {
    var _this = this;
    this.target = '';
    this.med = '';

    this.doNext = function() {
        /**/
        if (1 * (_this.med) < 1 * (-1600)) {
            // change text
            document.getElementById(_this.target).innerHTML = "Completed!";
        } else if (_this.med < 0) {
            // change text
            document.getElementById(_this.target).innerHTML = "Meditation in Progress";
        } else {
            document.getElementById(_this.target).innerHTML = convert_to_time(_this.med);
            setTimeout(function() {
                _this.doNext()
            }, 1000);
        } //
        _this.med = _this.med - 1;
    }

}

do_cd.prototype.m1 = function() {
    this.target = 'medTime1';
    this.med = '<?php print $med1 ?>';
    this.doNext();
}

do_cd.prototype.m2 = function() {
    this.target = 'medTime2';
    this.med = '<?php print $med2 ?>';
    this.doNext();
}

do_cd.prototype.m3 = function() {
    this.target = 'medTime3';
    this.med = '<?php print $med3 ?>';
    this.doNext();
}
</script>

<?php /*?>
<div id="peace_times_box">
    <div>
        <div style="text-align:left; color:#FFF; font-weight:bold;"><?php print "Saturday, ".$nMonth.' '.$date1 ?></div>
        <span>Count Down (<span class="meditation" id='medTime1'>Meditation</span>) 5:30 PM UTC</span><br />
        <span>Count Down (<span class="meditation" id='medTime2'>Meditation</span>) 7:00 PM UTC</span>
    </div>
    <div>
        <div style="text-align:left; color:#FFF; font-weight:bold;"><?php print "Sunday, ".$nMonth.' '.$date2  ?> </div>
        <span>Count Down (<span class="meditation" id='medTime3'>Meditation</span>) 7:00 PM UTC</span>
    </div>
</div>





<script type="text/javascript">
//meditation start
med_1 = new do_cd;
med_1.m1();

med_2 = new do_cd;
med_2.m2();

med_3 = new do_cd;
med_3.m3();
</script><?php */?>