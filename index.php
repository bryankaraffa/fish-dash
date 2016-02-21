<!-- Attempt #1 -->
<link rel="stylesheet" href="//cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css" />
<script src="https://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
<script src="./includes/heatmap-js/build/heatmap.js"> </script>
<script>
// don't forget to include leaflet-heatmap.js
var testData = {
  max: 8,
  data: [{lat: 24.6408, lng:46.7728, count: 3},{lat: 50.75, lng:-1.55, count: 1}]
};

var baseLayer = L.tileLayer(
  '//{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
    attribution: '...',
    maxZoom: 18
  }
);

var cfg = {
  // radius should be small ONLY if scaleRadius is true (or small radius is intended)
  // if scaleRadius is false it will be the constant radius used in pixels
  "radius": 2,
  "maxOpacity": .8, 
  // scales the radius based on map zoom
  "scaleRadius": true, 
  // if set to false the heatmap uses the global maximum for colorization
  // if activated: uses the data maximum within the current map boundaries 
  //   (there will always be a red spot with useLocalExtremas true)
  "useLocalExtrema": true,
  // which field name in your data represents the latitude - default "lat"
  latField: 'lat',
  // which field name in your data represents the longitude - default "lng"
  lngField: 'lng',
  // which field name in your data represents the data value - default "value"
  valueField: 'count'
};


var heatmapLayer = new HeatmapOverlay(cfg);

var map = new L.Map('map-canvas', {
  center: new L.LatLng(25.6586, -80.3568),
  zoom: 4,
  layers: [baseLayer, heatmapLayer]
});

heatmapLayer.setData(testData);
</script>
 <div id="map-canvas"></div>

<?php
///////////////////////////////////////////////////////////
// Fishing Dashboard
// By: Bryan Karaffa (github.com/bryankaraffa)
///////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////
// Include required libraries
require_once './config.php';
require_once './includes/php-solunar/solunar.php';

function get_string_between($string, $start, $end)
{
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0)
        return "";
    $ini += strlen($start);
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}
function get_all_strings_between($content, $start, $end) {
    preg_match_all("$start([^<]*)$end/", $content, $m);

    return $m[1];
}
function cardinal_direction($angle, $division = 8) {
//Takes an angle in degrees and returns a text description of that angle
//Parameters:
//$angle is the angle to be described in degrees (may be positive or negative)
//$division is number of cardinal points to be used. 
// It may be 2 (N or S), 4 (N, E, S, W), 8 (N, NE, W, etc), 16 (N, NNE, NE, ENE, etc) or
// 32 (N, NNE by N, NNE, NNE by E, NE by N, etc)
//Returns:
//Array with keys
// 'resolved_angle' is the numerical value of the returned angle
// 'short_name' is the abbreviation of the text description of the returned angle (N, NE, NNExE, etc)
// 'full_name' is the full text version of the decription of the returned angle (N, North East by North)
//Check division is one of the acceptable angles.  
        if ($division == !in_array($division, array('2', '4', '8', '16', '32'))) {
          return FALSE;
        }

        $short_name = array(
            '0' => 'N',
            11.25 => 'NxE',
            22.5 => 'NNE',
            33.75 => 'NExN',
            45 => 'NE',
            56.25 => 'NExE',
            67.5 => 'ENE',
            78.75 => 'ExN',
            90 => 'E',
            101.25 => 'ExS',
            112.5 => 'ESE',
            123.75 => 'SExE',
            135 => 'SE',
            146.25 => 'SExS',
            157.5 => 'SSE',
            168.75 => 'SxE',
            180 => 'S',
            191.25 => 'SxW',
            202.5 => 'SSW',
            213.75 => 'SWxS',
            225 => 'SW',
            236.25 => 'SWxW',
            247.5 => 'WSW',
            258.75 => 'WxS',
            270 => 'W',
            281.25 => 'WxN',
            292.5 => 'WNW',
            303.75 => 'NWxW',
            315 => 'NW',
            326.25 => 'NWxN',
            337.5 => 'NNW',
            348.75 => 'NxW'
        );

        $full_name = array(
            '0' => 'North',
            11.25 => 'North by East',
            22.5 => 'North North East',
            33.75 => 'North East by North',
            45 => 'North East',
            56.25 => 'North East by East',
            67.5 => 'East North East',
            78.75 => 'East by North',
            90 => 'East',
            101.25 => 'East by South',
            112.5 => 'East South East',
            123.75 => 'South East by East',
            135 => 'South East',
            146.25 => 'South East by South',
            157.5 => 'South South East',
            168.75 => 'South by East',
            180 => 'South',
            191.25 => 'South by West',
            202.5 => 'South South West',
            213.75 => 'South West by South',
            225 => 'South West',
            236.25 => 'South West by West',
            247.5 => 'West South West',
            258.75 => 'West by South',
            270 => 'West',
            281.25 => 'West by North',
            292.5 => 'West North West',
            303.75 => 'North West by West',
            315 => 'North West',
            326.25 => 'North West by North',
            337.5 => 'North North West',
            348.75 => 'North by West'
        );

//Make sure angle is 0-359 and positive
        $angle = $angle % 360;

        if ($angle < 0) {
          $angle = 360 + $angle; //Addition beacause angle is negative
        }


//Work out how big each segment is in degrees (e.g NSEW is 90deg segments)
        $segment_size = 360 / $division;

//Resolved angle is the closest 'segment' to the passed $angle
        $resolved_angle = (float) round($angle / $segment_size) * $segment_size;
        if ($resolved_angle == 360) {
          $resolved_angle = 0; //0 will resolve to 360 so set it back to 0
        };

        return array('resolved_angle' => $resolved_angle, 'short_name' => $short_name[$resolved_angle], 'full_name' => $full_name[$resolved_angle]);
      }

///////////////////////////////////////////////////////////
// Setup Inputs
$timezone_Feed = 'http://api.geonames.org/timezone?username=demo&lat=34.43013&lng=-119.7118';
$oceantemp_Feed = 'http://www.ndbc.noaa.gov/rss/ndbc_obs_search.php?lat=34.405N&lon=119.692W';


$today = getdate();  //defualt to todays date
$year = $today[year];
$month = $today[mon];
$day = $today[mday];
$tz = -8;

$lat = 34.43013;
$underlong = -119.7118;
$tz = file_get_contents($timezone_Feed."&lat=".$lat."&"."lng=".$underlong);
$tz = (int)get_string_between($tz,'<rawOffset>','</rawOffset>');

if ($_GET['day']) {
    $day = $_GET['day'];
}
///////////////////////////////////////////////////////////

// Get Water Temperature
$oceantemp_Contents = file_get_contents($oceantemp_Feed);

preg_match_all('/(?:Water Temperature:<\/strong> )(\d*.\d*)(?:&#176;)([CF])(?: \()(\d*.\d*)(?:&#176;)([CF])(?:\))/',$oceantemp_Contents, $oceantemp_Results);


/*********************************************************************/
/* 
 * Required:
 *      $year, $month, $day, $tz, $lat, $underlong, $UT
 * 
 * $year -> year part of date we will calculate for in yyyy format. example 2008
 *
 * $month -> month part of date we will calculate for in mm format. example 2 or 02
 *
 * $day -> day part of date we will calculate for in dd format. example 2 or 02
 *
 * $tz -> timezone offset to calculate results in. example -5 for EST
 *
 * $lat -> latitude (NEGATIVE NUMBERS ARE WEST)
 *
 * $underlong -> longitude  (NEGATIVE NUMBERS ARE SOUTH)
 *
 * $UT = 0.0, Universal time, keep this set at zero, its for the julian
 * date calculations, for our purposes we only need the julian date at
 * the start of the day, however  I might change that in later versions.
/*********************************************************************/
//get dates	
	$JD = get_Julian_Date ($year, $month, $day, $UT);
	$date = ($JD - 2400000.5 - ($tz/24.0));
/*********************************************************************/	
//get rise, set and transit times for moon and sun
	get_rst  (1, $date, 0.0 - $underlong , $lat, $sunrise, $sunset, $suntransit);
	get_rst  (0, $date, 0.0 - $underlong, $lat, $moonrise, $moonset, $moontransit);
	$moonunder = get_underfoot($date, $underlong);
/*********************************************************************/
//get solunar minor periods
	sol_get_minor1($minorstart1, $minorstop1, $moonrise);
	sol_get_minor2($minorstart2, $minorstop2, $moonset);
/*********************************************************************/
//get solunar major periods
	sol_get_major1 ($majorstart1, $majorstop1, $moontransit);
	sol_get_major2 ($majorstart2, $majorstop2, $moonunder);
/*********************************************************************/
//get moon phase 
	$moonage = get_moon_phase ($JD, $PhaseName, $illumin);
/*********************************************************************/
//get day scale
	$phasedayscale = phase_day_scale ($moonage);
	$soldayscale = sol_get_dayscale ($moonrise, $moonset, $moontransit, $sunrise, $sunset);
    $dayscale = 0;
    $dayscale = ($soldayscale + $phasedayscale);
/*********************************************************************/
/**
	echo "<h4>Moon</h4>";
	//set the event title:
	$event = sprintf("rise =");
	//call function to display event and time
	display_event_time($moonrise, $event);
	$event = sprintf("transit =");
	display_event_time($moontransit, $event);
	$event = sprintf("set =");
	display_event_time($moonset, $event);
	echo "<br>Phase is $PhaseName, ";
	$illumin = $illumin*100;
	echo round($illumin, 1);
	echo "% illuminated, ";
	echo round($moonage, 1);
	echo " days since new.";
	echo "<h4>Sun</h4>";
	$event = sprintf("rise = ");
	display_event_time($sunrise, $event);
	$event = sprintf("transit =");
	display_event_time($suntransit, $event);
	$event = sprintf("set =");
	display_event_time($sunset, $event);
*/	
	

?>
<!-- Attempt #2 -->
<!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/css/materialize.min.css">

  <!-- Compiled and minified JavaScript -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>

  <!-- Content -->
  <body>
  <div class="container">
  <!-- Each Section is a `card` -->
  
        <div class="row">
  
          <div class="col s12 m6">
          <div class="card light-blue lighten-5">
            <div class="card-content blue-text text-darken-2">
              <span class="card-title">Fishing Dashboard</span>
              <p>
              <?php 
              
              echo "Showing current conditions for <b>$year/$month/$day</b><br />Position: <b>"; 
              
              if ($lat < 0){
              $lat1 = 0 - $lat;
              echo round($lat1,2);
              echo "S/";
              }else{
              echo round($lat,2);
              echo "N/";
              }

              if ($underlong < 0){
              $long1 = 0 - $underlong;
              echo round($long1,2);
              echo "W";
              }else{
              echo round($long,2);
              echo "E";
              }
                
              
              ?>
              </b></p>
            </div>
          </div>
        </div>  
  
  
  
        <div class="col s12 m6">
          <div class="card light-blue darken-2">
            <div class="card-content white-text">
              <span class="card-title">Solunar Calendar</span>
              <p><?php echo "Todays action is rated a <b>$dayscale</b>"; ?></p>
              <p>
            <?php
            echo "<p>Major Periods</p>";
            //display earlier major time first
            if (moontransit < 9.5){
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($majorstart1, $event);
                $event = sprintf(" -");
                display_event_time($majorstop1, $event);
                echo '  </div>';
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($majorstart2, $event);
                $event = sprintf(" -");
                display_event_time($majorstop2, $event);
                echo '  </div>';
                }
            else {
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($majorstart2, $event);
                $event = sprintf(" -");
                display_event_time($majorstop2, $event);
                echo '  </div>';
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($majorstart1, $event);
                $event = sprintf(" -");
                display_event_time($majorstop1, $event);
                echo '  </div>';
                }
            
            echo "<p>Minor Periods</p>";
            //display earlier minor time first, minor 1 is based on moonset, minor2 on moonrise
            if (moonrise > moonset){
                
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($minorstart1, $event);
                $event = sprintf(" -");
                display_event_time($minorstop1, $event);
                echo '  </div>';
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($minorstart2, $event);
                $event = sprintf(" -");
                display_event_time($minorstop2, $event);
                echo '  </div>';
                }
            else {                            
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($minorstart2, $event);
                $event = sprintf(" -");
                display_event_time($minorstop2, $event);
                echo '  </div>';
                $event = sprintf("");
                echo '  <div class="chip">';
                display_event_time($minorstart1, $event);
                $event = sprintf(" -");
                display_event_time($minorstop1, $event);
                echo '  </div>';
                }
              ?>
            </div>
          </div>
        </div>
        
        
<?php
// Current Weather
$result = Array();
$loc = preg_replace('/[^-0-9,.]/', '', 	$lat.",".$underlong); // Sanitize $loc. Only allow numbers, hyphen, and period (coordinates)

$forecast = file_get_contents('https://api.forecast.io/forecast/'.$config['api_key'].'/'.$loc); // Call Forecast.io
$forecast = json_decode($forecast,TRUE); // Convert JSON to Array

$forecast['currently']['windDirection']=cardinal_direction($forecast['currently']['windBearing']);
?>   
        
        <div class="col s12 m6">
          <div class="card light-blue darken-2">
            <div class="card-content white-text">
              <span class="card-title valign-wrapper">Current Weather</span>
              <p class="valign-wrapper">
              <div class="chip valign center-align">Summary: <?php echo $forecast['currently']['summary']; ?></div>
              <div class="chip valign center-align">Temperature: <?php echo $forecast['currently']['temperature']."&#176;F"; ?></div>
              <div class="chip valign center-align">Precipitation: <?php echo $forecast['currently']['precipProbability']."%"; ?></div>
              <div class="chip valign center-align">Wind: <?php echo $forecast['currently']['windSpeed']."mph (".$forecast['currently']['windDirection']['short_name'].")"; ?></div>
              <div class="chip valign center-align">Pressure: <?php echo $forecast['currently']['pressure']."mb"; ?></div>
              </p>
            </div>
          </div>
        </div>          
       
        <div class="col s12 m6">
          <div class="card light-blue darken-2">
            <div class="card-content white-text">
              <span class="card-title">Water Temperature</span>
              <h4><?php echo $oceantemp_Results[1][0]."&#176;".$oceantemp_Results[2][0]; ?></h4>
            </div>
          </div>
        </div>           
        
            <?php
            /*********************************************************************/
            /*
            * RAW DATA DUMP
            */
            if ($_GET && $_GET['rawdata'] ==1) {
            ?>
        <div class="col s12 m6">
          <div class="card light-blue darken-2">
            <div class="card-content white-text">
            <span class="card-title">Raw Data</span>
            <p class="flow-text"><small>
            <?php            
                echo "<br>julian date = $JD";
                echo "<br>moonrise = $moonrise";
                echo "<br>moontransit = $moontransit";
                echo "<br>moonunder = $moonunder";
                echo "<br>moonset = $moonset";
                echo "<br>sunrise = $sunrise";
                echo "<br>suntransit = $suntransit";
                echo "<br>sunset = $sunset";
                echo "<br>minor 1 start = $minorstart1";
                echo "<br>minor 1 stop = $minorstop1";
                echo "<br>minor 2 start = $minorstart2";
                echo "<br>minor 2 stop = $minorstop2";
                echo "<br>major 1 start = $majorstart1";
                echo "<br>major 1 stop = $majorstop1";
                echo "<br>major 2 start = $majorstart2";
                echo "<br>major 2 stop = $majorstop2";
                echo "<br>soldayscale = $soldayscale";
                echo "<br>phasedayscale = $phasedayscale";
                //daily action is the sum of $soldayscale and $phasedayscale
                echo "<br>daily action is a sum = $soldayscale + $phasedayscale";
                echo "<br>moonage in days = $moonage";
                echo "<br>moon illumination = $illumin";
                echo "<br>moonphase name = $PhaseName";
                echo "<br>timezone offset = $tz";
                echo "<pre>";
                var_dump($oceantemp_Contents);
                var_dump($oceantemp_Results);
                var_dump($forecast);
                echo "</pre>";   
            ?>
                </small></p>
                </div>
            </div>
            </div>    
            <?php            
            }
            //thats it were done
            ?> 
      </div>
    </div>
    
    <footer class="page-footer blue darken-5">
          <div class="container">
            <div class="row">
              <div class="col l6 s12">
                <p class="grey-text text-lighten-4">Please feel free to send comments and suggestions to <i>fishingdashboard</i> at calcoasttech.com</p>
              </div>
            </div>
          </div>
          <div class="footer-copyright">
            <div class="container">
            © 2016 Copyright Bryan Karaffa
            <a class="grey-text text-lighten-4 right" href="https://github.com/bryankaraffa" target="_blank">github</a>
            </div>
          </div>
        </footer>
</body>