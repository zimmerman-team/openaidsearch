<?php

	include_once( 'constants.php' );
	if(file_exists('countries.php') && empty($_COUNTRY_ISO_MAP)) include_once( 'countries.php' );
	include_once( 'map_regions.php' );
	
	$FILTER = getFilter($_GET);
	$limit=20;
	if(!empty($FILTER['countries'])) {
	
		$countries = explode('|', $FILTER['countries']);
		$array['objects'] = array();
		foreach($countries AS $c) {
			$array['objects'][$c] = array('path' => $_GM_POLYGONS[$c], 'name' => $_COUNTRY_ISO_MAP[$c], 'total_cnt' => $_COUNTRY_ACTIVITY_COUNT[$c]);
		}
		
	} else {

		$array['objects'] = array();
		$array['meta']['total_count'] = COUNT($_COUNTRY_ISO_MAP);
		foreach($_COUNTRY_ISO_MAP AS $iso=>$c) {
			if(isset($array['objects'][$iso])) {
				$array['objects'][$iso]['total_cnt']++;
			} else {
				if(isset($_GM_POLYGONS[$iso])) {
					$array['objects'][$iso] = array('path' => $_GM_POLYGONS[$iso], 'name' => $c, 'total_cnt' => $_COUNTRY_ACTIVITY_COUNT[$iso]);
				}
			}
		}
		
	}
	
	
	
	if(!isset($FILTER['inline'])) {
		echo json_encode($array);
	}




function getFilter(&$DATA, $format=1) {
	if (empty($DATA)) return false;
	if($format>2) return false;
	
	foreach ($DATA AS $key=>$value) {
		if($format==2) {
			$tmp->$key = $value;
		}elseif($format==1){
			$tmp["$key"] = $value;
		}
	}
	
	return $tmp;
}
?>