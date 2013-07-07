<?php 
include_once('../../config/symbini.php');

$locality = $_REQUEST['locality'];
$country = array_key_exists('country',$_REQUEST)?$_REQUEST['country']:'';
$state = array_key_exists('state',$_REQUEST)?$_REQUEST['state']:'';
$county = array_key_exists('county',$_REQUEST)?$_REQUEST['county']:'';

if(!$country || !$state || !$county){
	$locArr = explode(";",$locality);
	$locality = array_pop($locArr);
	if(!$country && $locArr) $country = array_shift($locArr);
	if(!$state && $locArr) $state = array_shift($locArr);
	if(!$county && $locArr) $county = array_shift($locArr);
}
//Modify TRS data to make it more compatable to the GeoLocate format (S23 needs to be Sec23) 
if(preg_match('/\d{1,2}[NS]{1}T\s\d{1,2}[EW]{1}R\s\d{1,2}S/',$locality)){
	$locality = preg_replace('/(\d{1,2}[NS]{1})T\s(\d{1,2}[EW]{1})R\s(\d{1,2})S/', 'T$1 R$2 Sec$3', $locality);
}
elseif(preg_match('/R\d{1,2}[EW]{1}\sS\d{1,2}/i',$locality)){
	$locality = preg_replace('/\sS(\d{1,2})/', ' Sec$1', $locality);
}
//Convert latin1 character sets to utf-8
if(strtolower($charset) == "iso-8859-1"){
	if(mb_detect_encoding($country,'UTF-8,ISO-8859-1') == "UTF-8"){
		$country = utf8_encode($country);
	}
	if(mb_detect_encoding($state,'UTF-8,ISO-8859-1') == "UTF-8"){
		$state = utf8_encode($state);
	}
	if(mb_detect_encoding($county,'UTF-8,ISO-8859-1') == "UTF-8"){
		$county = utf8_encode($county);
	}
	if(mb_detect_encoding($locality,'UTF-8,ISO-8859-1') == "UTF-8"){
		$locality = utf8_encode($locality);
	}
}

$country = removeAccents($country);

$urlVariables = 'country='.urlencode($country).'&state='.urlencode($state).'&county='.$county.'&locality='.$locality;

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>GEOLocate Tool</title>
	<link rel="stylesheet" href="<?php echo $clientRoot; ?>/css/main.css" type="text/css" />
	<style>
		iframe {
			width: 1020px;
			height:720px;
			margin: 0px;
			border: 1px solid #000;
		}
	</style>
	<script type="text/javascript">
	    function transferCoord(evt) {
	        if(evt.origin !== "http://www.museum.tulane.edu") {
				alert("iframe url does not have permision to interact with me");
	        }
	        else {
	            var breakdown = evt.data.split("|");
                if(breakdown.length == 4){
                    if(breakdown[0] == ""){
                    	alert("There are no data points to tranfer");
                    }
                    else{
	                	opener.geoLocateUpdateCoord(breakdown[0],breakdown[1],breakdown[2]);
	                    self.close();
                    }
                }
	        }
	    }
	    if(window.addEventListener) {
	        // For standards-compliant web browsers
	        window.addEventListener("message", transferCoord, false);
	    }
	    else {
	        window.attachEvent("onmessage", transferCoord);
	    }
	</script>
</head>

<body>
	<div id="container">
		<div >
			<!--<iframe id="da-iframe" src="http://www.museum.tulane.edu/nelson/gravier2.html"></iframe>-->
			<iframe id="Iframe1" src="http://www.museum.tulane.edu/geolocate/web/webgeoreflight.aspx?v=1&georef=run&tab=locality&<?php echo $urlVariables; ?>"></iframe>
		</div>
	</div>
</body>
</html>
<?php 

function removeAccents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
	return $string;

	$chars = array(
	// Decompositions for Latin-1 Supplement
	chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
	chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
	chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
	chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
	chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
	chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
	chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
	chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
	chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
	chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
	chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
	chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
	chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
	chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
	chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
	chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
	chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
	chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
	chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
	chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
	chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
	chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
	chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
	chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
	chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
	chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
	chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
	chr(195).chr(191) => 'y',
	// Decompositions for Latin Extended-A
	chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
	chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
	chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
	chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
	chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
	chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
	chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
	chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
	chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
	chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
	chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
	chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
	chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
	chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
	chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
	chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
	chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
	chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
	chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
	chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
	chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
	chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
	chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
	chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
	chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
	chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
	chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
	chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
	chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
	chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
	chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
	chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
	chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
	chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
	chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
	chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
	chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
	chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
	chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
	chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
	chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
	chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
	chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
	chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
	chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
	chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
	chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
	chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
	chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
	chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
	chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
	chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
	chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
	chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
	chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
	chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
	chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
	chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
	chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
	chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
	chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
	chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
	chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
	chr(197).chr(190) => 'z', chr(197).chr(191) => 's');

	$string = strtr($string, $chars);

    return $string;
}
?>