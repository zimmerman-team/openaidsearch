<?php

include( TEMPLATEPATH.'/constants.php' );
include( TEMPLATEPATH.'/widgets.php' );

if(file_exists(TEMPLATEPATH . '/countries.php')) {
	$fmdate = filemtime(TEMPLATEPATH . '/countries.php');
	if((time() - $fmdate)>$_RELAOD_FILTERS_TIMEOUT) {
		wp_generate_constants();
	}
	include_once( TEMPLATEPATH . '/countries.php' );
	asort($_COUNTRY_ISO_MAP);
} else {
	wp_generate_constants();
	include_once( TEMPLATEPATH . '/countries.php' );
	asort($_COUNTRY_ISO_MAP);
	
}
if(file_exists(TEMPLATEPATH . '/sectors.php')) {
	include_once( TEMPLATEPATH . '/sectors.php' );
	asort($_SECTOR_CHOICES);
}
if(file_exists(TEMPLATEPATH . '/regions.php')) {
	include_once( TEMPLATEPATH . '/regions.php' );
	asort($_REGION_CHOICES);
}
if(file_exists(TEMPLATEPATH . '/organisations.php')) {
	include_once( TEMPLATEPATH . '/organisations.php' );
	asort($_ORG_CHOICES);
}

/**
 * Disable automatic general feed link outputting.
 */
automatic_feed_links( false );

//remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'wp_generator');

if ( function_exists('register_sidebar') ) {

	register_sidebar(array(
		'id' => 'partners',
		'name' => 'Partners',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => ''
	));
	
	register_sidebar(array(
		'id' => 'copyright',
		'name' => 'Copyright',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => ''
	));	
	
	
}


if ( function_exists( 'add_theme_support' ) ) {
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 50, 50, true ); // Normal post thumbnails
	add_image_size( 'single-post-thumbnail', 400, 9999, true );
	add_image_size( 'thumbnail_260_120', 260, 120, true );
	add_image_size( 'thumbnail_940_300', 940, 300, true );
}

register_nav_menus( array(
	'primary' => __( 'Primary Navigation', 'oipa' ),
) );
register_nav_menus( array(
	'header_menu' => __( 'Header Navigation', 'oipa' ),
) );
register_nav_menus( array(
	'footer_menu' => __( 'Footer Navigation', 'oipa' ),
) );

if(empty($_COUNTRY_ISO_MAP)) {
	wp_generate_constants();
}

//add [email]...[/email] shortcode
function shortcode_email($atts, $content) {
	$result = '';
	for ($i=0; $i<strlen($content); $i++) {
		$result .= '&#'.ord($content{$i}).';';
	}
	return $result;
}
add_shortcode('email', 'shortcode_email');

// register tag [template-url]
function filter_template_url($text) {
	return str_replace('[template-url]',get_bloginfo('template_url'), $text);
}
add_filter('the_content', 'filter_template_url');
add_filter('get_the_content', 'filter_template_url');
add_filter('widget_text', 'filter_template_url');

// register tag [site-url]
function filter_site_url($text) {
	return str_replace('[site-url]',get_bloginfo('url'), $text);
}
add_filter('the_content', 'filter_site_url');
add_filter('get_the_content', 'filter_site_url');
add_filter('widget_text', 'filter_site_url');


/* Replace Standart WP Menu Classes */
function change_menu_classes($css_classes) {
        $css_classes = str_replace("current-menu-item", "active", $css_classes);
        $css_classes = str_replace("current-menu-parent", "active", $css_classes);
        return $css_classes;
}
add_filter('nav_menu_css_class', 'change_menu_classes');


//allow tags in category description
$filters = array('pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description');
foreach ( $filters as $filter ) {
    remove_filter($filter, 'wp_filter_kses');
}


function wp_generate_constants() {
	set_time_limit(0);
	$limit=50;
	$activities_url = SEARCH_URL . "countries/?format=json&limit={$limit}&statistics__total_activities__gte=0";
	$content = file_get_contents($activities_url);
	$result = json_decode($content);
	$meta = $result->meta;
	$count = $meta->total_count;
	$objects = $result->objects;
	$data = objectToArray($objects);
	
	$start=$limit;
	$countries = array();
	$countries_activities = array();
	$sectors = array();
	$regions = array();
	$organisations = array();
	
	if(!empty($data)) {
		foreach($data AS $a) {
			
			if(intval($a['statistics']['total_activities'])<=0) continue;
			$countries[$a['iso']] = $a['name'];
			$countries_activities[$a['iso']] = $a['statistics']['total_activities'];
		}
	}
	
	while($start<$count) {
		$activities_url = SEARCH_URL . "countries/?format=json&offset={$start}&limit={$limit}&statistics__total_activities__gte=0";
		
		$content = file_get_contents($activities_url);
		$result = json_decode($content);
		
		$objects = $result->objects;
		
		$data = objectToArray($objects);
		if(!empty($data)) {
			foreach($data AS $a) {
				if(intval($a['statistics']['total_activities'])<=0) continue;
				$countries[$a['iso']] = $a['name'];
				$countries_activities[$a['iso']] = $a['statistics']['total_activities'];
			}
		}
		$start+=$limit;
		sleep(1);
	}
	
	$to_write = '<?php
$_COUNTRY_ISO_MAP = array(
';
	if(!empty($countries)) {
		
		foreach($countries AS $key=>$value) {
			if(empty($value) || $value=='#N/A') continue;
			$name = addslashes($value);
			$to_write .= "'{$key}' => '{$name}',\n";
		}
		
	}
	$to_write .= ');
	
$_COUNTRY_ACTIVITY_COUNT = array(
';
	if(!empty($countries_activities)) {
		
		foreach($countries_activities AS $key=>$value) {
			$value = intval($value);
			$to_write .= "'{$key}' => '{$value}',\n";
		}
		
	}

$to_write .= ');
?>';
	$fp = fopen(TEMPLATEPATH . '/countries.php', 'w+');
	fwrite($fp, $to_write);
	fclose($fp);
	
	
$activities_url = SEARCH_URL . "sectors/?format=json&limit={$limit}";
$content = file_get_contents($activities_url);
$result = json_decode($content);
$meta = $result->meta;
$count = $meta->total_count;
$objects = $result->objects;
$data = objectToArray($objects);
if(!empty($data)) {
	foreach($data AS $a) {
		$sectors[$a['code']] = trim($a['name']);
	}
}

$start=$limit;

while($start<$count) {
	$activities_url = SEARCH_URL . "sectors/?format=json&offset={$start}&limit={$limit}";
	$content = file_get_contents($activities_url);
	$result = json_decode($content);
	$objects = $result->objects;
	$data = objectToArray($objects);
	if(!empty($data)) {
		foreach($data AS $a) {
			$sectors[$a['code']] = trim($a['name']);
		}
	}
	
	$start+=$limit;
}	
	
	$to_write = '<?php
$_SECTOR_CHOICES = array(
';
	if(!empty($sectors)) {
		
		foreach($sectors AS $key=>$value) {
			if(empty($value) || $value=='#N/A') continue;
			$name = addslashes($value);
			$to_write .= "'{$key}' => '{$name}',\n";
		}
		
	}
	$to_write .= ');
?>';
	$fp = fopen(TEMPLATEPATH . '/sectors.php', 'w+');
	fwrite($fp, $to_write);
	fclose($fp);

	
$activities_url = SEARCH_URL . "regions/?format=json&limit={$limit}";
$content = file_get_contents($activities_url);
$result = json_decode($content);
$meta = $result->meta;
$count = $meta->total_count;
$objects = $result->objects;
$data = objectToArray($objects);
if(!empty($data)) {
	foreach($data AS $a) {
		$regions[$a['code']] = trim($a['name']);
	}
}
$start=$limit;

while($start<$count) {
	$activities_url = SEARCH_URL . "regions/?format=json&offset={$start}&limit={$limit}";
	$content = file_get_contents($activities_url);
	$result = json_decode($content);
	$objects = $result->objects;
	$data = objectToArray($objects);
	if(!empty($data)) {
		foreach($data AS $a) {
			$regions[$a['code']] = trim($a['name']);
		}
	}
	
	$start+=$limit;
}
	
	$to_write = '<?php
$_REGION_CHOICES = array(
';
	if(!empty($regions)) {
		
		foreach($regions AS $key=>$value) {
			if(empty($value) || $value=='#N/A') continue;
			$name = addslashes($value);
			$to_write .= "'{$key}' => '{$name}',\n";
		}
	}
	
	$to_write .= ');
?>';
	$fp = fopen(TEMPLATEPATH . '/regions.php', 'w+');
	fwrite($fp, $to_write);
	fclose($fp);
	
	
$activities_url = SEARCH_URL . "organisations/?format=json&statistics__total_activities__gt=0&limit={$limit}";
$content = file_get_contents($activities_url);
$result = json_decode($content);
$meta = $result->meta;
$count = $meta->total_count;
$objects = $result->objects;
$data = objectToArray($objects);
if(!empty($data)) {
	foreach($data AS $a) {
		$organisations[$a['ref']] = trim($a['org_name']);
	}
}

$start=$limit;

while($start<$count) {
	$activities_url = SEARCH_URL . "organisations/?format=json&statistics__total_activities__gt=0&offset={$start}&limit={$limit}";
	$content = file_get_contents($activities_url);
	$result = json_decode($content);
	$objects = $result->objects;
	$data = objectToArray($objects);
	if(!empty($data)) {
		foreach($data AS $a) {
			$organisations[$a['ref']] = trim($a['org_name']);
		}
	}
	
	$start+=$limit;
}
	
	$to_write = '<?php
$_ORG_CHOICES = array(
';
	if(!empty($organisations)) {
		
		foreach($organisations AS $key=>$value) {
			if(empty($value) || $value=='#N/A') continue;
			$name = addslashes($value);
			$to_write .= "'{$key}' => '{$name}',\n";
		}
	}
	
	$to_write .= ');
?>';
	$fp = fopen(TEMPLATEPATH . '/organisations.php', 'w+');
	fwrite($fp, $to_write);
	fclose($fp);
	
	set_time_limit(30);
}


//Make WP Admin Menu HTML Valid
function wp_admin_bar_valid_search_menu( $wp_admin_bar ) {
	if ( is_admin() )
		return;

	$form  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch"><div>';
	$form .= '<input class="adminbar-input" name="s" id="adminbar-search" tabindex="10" type="text" value="" maxlength="150" />';
	$form .= '<input type="submit" class="adminbar-button" value="' . __('Search') . '"/>';
	$form .= '</div></form>';

	$wp_admin_bar->add_menu( array(
		'parent' => 'top-secondary',
		'id'     => 'search',
		'title'  => $form,
		'meta'   => array(
			'class'    => 'admin-bar-search',
			'tabindex' => -1,
		)
	) );
}
function fix_admin_menu_search() {
	remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
	add_action( 'admin_bar_menu', 'wp_admin_bar_valid_search_menu', 4 );
}
add_action( 'add_admin_bar_menus', 'fix_admin_menu_search' );


function sub_style($text){
	$input = '<p><label for="s2email">Your email:</label><br /><input type="text" name="email" id="s2email" value="Enter email address..." size="20" onfocus="if (this.value == \'Enter email address...\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'Enter email address...\';}" /></p><p><input type="submit" name="subscribe" value="Subscribe" />&nbsp;<input type="submit" name="unsubscribe" value="Unsubscribe" /></p>';
	$style_input = '<div class="row">
				<label for="email-field">* Email</label>
					<div class="box">
						<input class="text" name="email" id="email-field" type="text" value="" />
					</div>
			</div>
			<input class="btn-submit subscribe" type="submit" name="subscribe" value="Subscribe" />
			<input class="btn-submit unsubscribe" type="submit" name="unsubscribe" value="Unsubscribe" />';
	return str_replace($input, $style_input, $text);
}

remove_filter( 'the_content', 'wpautop' );



function wp_tag_cloud_custom( $args = '' ) {
	$defaults = array(
		'smallest' => TAG_smallest, 'largest' => TAG_largest, 'unit' => 'px', 'number' => TAG_number,
		'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
		'exclude' => '', 'include' => '', 'link' => 'view', 'taxonomy' => 'post_tag', 'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );

	$tags = get_terms( $args['taxonomy'], array_merge( $args, array( 'orderby' => 'count', 'order' => 'DESC' ) ) ); // Always query top tags

	if ( empty( $tags ) || is_wp_error( $tags ) )
		return;
        
	foreach ( $tags as $key => $tag ) {
		if ( 'edit' == $args['link'] )
			$link = get_edit_tag_link( $tag->term_id, $tag->taxonomy );
		else
			$link = get_term_link( intval($tag->term_id), $tag->taxonomy );
		if ( is_wp_error( $link ) )
			return false;

		$tags[ $key ]->link = $link;
		$tags[ $key ]->id = $tag->term_id;
	}
        

	$return = wp_generate_tag_cloud_custom( $tags, $args ); // Here's where those top tags get sorted according to $args

	$return = apply_filters( 'wp_tag_cloud', $return, $args );

	if ( 'array' == $args['format'] || empty($args['echo']) )
		return $return;

	echo $return;
}



function wp_generate_tag_cloud_custom( $tags, $args = '' ) {
	$defaults = array(
		'smallest' => 8, 'largest' => 22, 'unit' => 'pt', 'number' => 0,
		'format' => 'flat', 'separator' => "\n", 'orderby' => 'name', 'order' => 'ASC',
		'topic_count_text_callback' => 'default_topic_count_text',
		'topic_count_scale_callback' => 'default_topic_count_scale', 'filter' => 1,
	);

	if ( !isset( $args['topic_count_text_callback'] ) && isset( $args['single_text'] ) && isset( $args['multiple_text'] ) ) {
		$body = 'return sprintf (
			_n(' . var_export($args['single_text'], true) . ', ' . var_export($args['multiple_text'], true) . ', $count),
			number_format_i18n( $count ));';
		$args['topic_count_text_callback'] = create_function('$count', $body);
	}

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	if ( empty( $tags ) )
		return;

	$tags_sorted = apply_filters( 'tag_cloud_sort', $tags, $args );
	if ( $tags_sorted != $tags  ) { // the tags have been sorted by a plugin
		$tags = $tags_sorted;
		unset($tags_sorted);
	} else {
		if ( 'RAND' == $order ) {
			shuffle($tags);
		} else {
			// SQL cannot save you; this is a second (potentially different) sort on a subset of data.
			if ( 'name' == $orderby )
				uasort( $tags, '_wp_object_name_sort_cb' );
			else
				uasort( $tags, '_wp_object_count_sort_cb' );

			if ( 'DESC' == $order )
				$tags = array_reverse( $tags, true );
		}
	}

	if ( $number > 0 )
		$tags = array_slice($tags, 0, $number);

	$counts = array();
	$real_counts = array(); // For the alt tag
	foreach ( (array) $tags as $key => $tag ) {
		$real_counts[ $key ] = $tag->count;
		$counts[ $key ] = $topic_count_scale_callback($tag->count);
	}

	$min_count = min( $counts );
	$spread = max( $counts ) - $min_count;
	if ( $spread <= 0 )
		$spread = 1;
	$font_spread = $largest - $smallest;
	if ( $font_spread < 0 )
		$font_spread = 1;
	$font_step = $font_spread / $spread;

	$a = array();

	foreach ( $tags as $key => $tag ) {
		$count = $counts[ $key ];
		$real_count = $real_counts[ $key ];
		$tag_link = '#' != $tag->link ? esc_url( $tag->link ) : '#';
		$tag_id = isset($tags[ $key ]->id) ? $tags[ $key ]->id : $key;
		$tag_name = $tags[ $key ]->name;
		
		$font_size = str_replace( ',', '.', ( $smallest + ( ( $count - $min_count ) * $font_step ) ) );
		$line_height = ' line-height: '.($font_size + 2).'px ';
		
		if($font_size < TAG_color_1){
			$color = " color:#9cf;";
		}elseif($font_size  < TAG_color_2 ){
			$color = " color:#00468c;";
		}else{
			$color = " color:#fff;";
		}
		
		$a[] = "<a href='$tag_link' class='tag-link-$tag_id' title='" . esc_attr( call_user_func( $topic_count_text_callback, $real_count ) ) . "' style='font-size: " .
			$font_size
			. "$unit; $color $line_height '>$tag_name</a>";
	}

	switch ( $format ) :
	case 'array' :
		$return =& $a;
		break;
	case 'list' :
		$return = "<ul class='wp-tag-cloud'>\n\t<li>";
		$return .= join( "</li>\n\t<li>", $a );
		$return .= "</li>\n</ul>\n";
		break;
	default :
		$return = join( $separator, $a );
		break;
	endswitch;

	if ( $filter )
		return apply_filters( 'wp_generate_tag_cloud', $return, $tags, $args );
	else
		return $return;
}

function wp_generate_filter_html( $filter, $limit = 4 ) {
	$limit = intval($limit);
	if($limit<=0) $limit = 4;
	
	$filter = strtoupper($filter);
	
	$return = "<menu>";
	$add_more = false;
	$generate_popup = false;
	switch($filter) {
		case 'COUNTRY':
			global $_COUNTRY_ISO_MAP;
			if(empty($_COUNTRY_ISO_MAP) && !file_exists(TEMPLATEPATH . '/countries.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/countries.php' );
				asort($_COUNTRY_ISO_MAP);
			}
			$_data = $_COUNTRY_ISO_MAP;
			$selected = array();
			if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
				$query = rawurlencode($_REQUEST['query']);
				$srch_countries = array_map('strtolower', $_COUNTRY_ISO_MAP);
				$srch_countries = array_flip($srch_countries);
				$key = strtolower($query);
				if(isset($srch_countries[$key])) {
					$srch_countries = $srch_countries[$key];
				} else {
					$srch_countries = null;
				}
			}
			
			if(!empty($_REQUEST['countries'])) {
				$tmp = explode('|', $_REQUEST['countries']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_COUNTRY_ISO_MAP[$s];
				}
				
				if(!empty($srch_countries) && !isset($selected[$srch_countries])) {
					$selected[$srch_countries] = $_COUNTRY_ISO_MAP[$srch_countries];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = array_diff($_data, $selected);
				} else {
					$limit -= count($selected);
					$_data = array_diff($_data, $selected);
				}
			} else {
				if(!empty($srch_countries)) {
					$selected[$srch_countries] = $_COUNTRY_ISO_MAP[$srch_countries];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = $selected;
				} else {
					$limit -= count($selected);
					$_data = array_diff($_data, $selected);
				}
			}
			$cnt = 0;
			$checked = "";
			if(!empty($selected)) {
				foreach($selected AS $iso=>$c) {
					$checked = "checked=\"checked\"";
					$cnt++;
					$return .= "<li>
								<input name=\"countries\" id=\"check-country{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked}/>
								<label for=\"check-country{$cnt}\">{$c}</label>
								</li>";
				}
				
				$limit+=$cnt-1;
			}
			foreach($_data AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"countries\" id=\"check-country{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
							<label for=\"check-country{$cnt}\">{$c}</label>
							</li>";
				if($cnt>$limit) break;
			}
			
			if($limit<count($_COUNTRY_ISO_MAP)) {
				$add_more = true;
				$generate_popup = true;
			}
			
			break;
		case 'REGION':
			global $_REGION_CHOICES;
			if(empty($_REGION_CHOICES) && !file_exists(TEMPLATEPATH . '/regions.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/regions.php' );
				asort($_REGION_CHOICES);
			}
			$_data = $_REGION_CHOICES;

			$selected = array();
			if(!empty($_REQUEST['regions'])) {
				$tmp = explode('|', $_REQUEST['regions']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_REGION_CHOICES[$s];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = $selected;
				} else {
					$limit -= count($selected);
					$_data = array_diff($_data, $selected);
				}
			}
			
			$cnt = 1;
			$checked = "";
			if(!empty($selected)) {
				foreach($selected AS $iso=>$c) {
					$checked = "checked=\"checked\"";
					$cnt++;
					$return .= "<li>
								<input name=\"regions\" id=\"check-region{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
								<label for=\"check-region{$cnt}\">{$c}</label>
								</li>";
				}
				
				$limit+=$cnt-1;
			}
			foreach($_data AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"regions\" id=\"check-region{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
							<label for=\"check-region{$cnt}\">{$c}</label>
							</li>";
				if($cnt>$limit) break;
			}
			
			if($limit<count($_REGION_CHOICES)) {
				$add_more = true;
				$generate_popup = true;
			}

			break;
		case 'SECTOR':
			global $_SECTOR_CHOICES;
			if(empty($_SECTOR_CHOICES) && !file_exists(TEMPLATEPATH . '/sectors.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/sectors.php' );
				asort($_SECTOR_CHOICES);
			}
			$_data = $_SECTOR_CHOICES;
			$selected = array();
			if(!empty($_REQUEST['sectors'])) {
				$tmp = explode('|', $_REQUEST['sectors']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_SECTOR_CHOICES[$s];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = $selected;
				} else {
					$limit -= count($selected);
					$_data = array_diff($_data, $selected);
				}
			}
			
			$cnt = 1;
			$checked = "";
			if(!empty($selected)) {
				foreach($selected AS $iso=>$c) {
					$checked = "checked=\"checked\"";
					$cnt++;
					$return .= "<li>
								<input name=\"sectors\" id=\"check-sector{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
								<label for=\"check-sector{$cnt}\">{$c}</label>
								</li>";
				}
				
				$limit+=$cnt-1;
			}
			foreach($_data AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"sectors\" id=\"check-sector{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
							<label for=\"check-sector{$cnt}\">{$c}</label>
							</li>";
				if($cnt>$limit) break;
			}
			
			if($limit<count($_SECTOR_CHOICES)) {
				$add_more = true;
				$generate_popup = true;
			}

			break;
		case 'BUDGET':
			global $_BUDGET_CHOICES;
			$_data = $_BUDGET_CHOICES;
			$limit=count($_BUDGET_CHOICES); //Show all
			$selected = array();
			if(!empty($_REQUEST['budgets'])) {
				$tmp = explode('|', $_REQUEST['budgets']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_BUDGET_CHOICES[$s];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = $selected;
				}
			}
			
			$cnt = 1;
			
			foreach($_data AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked";
				$cnt++;
				$return .= "<li>
							<input name=\"budgets\" id=\"check-budget{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" />
							<label for=\"check-budget{$cnt}\">{$c}</label>
						</li>";
				if($cnt>$limit) break;
			}
			
			if($limit<count($_BUDGET_CHOICES)) {
				$add_more = true;
				$generate_popup = true;
			}
			
			break;
		case 'ORGANISATION':
			global $_ORG_CHOICES;
			if(empty($_ORG_CHOICES) && !file_exists(TEMPLATEPATH . '/organisations.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/organisations.php' );
				asort($_ORG_CHOICES);
			}
			$_data = $_ORG_CHOICES;
			$selected = array();
			if(!empty($_REQUEST['organisations'])) {
				$tmp = explode('|', $_REQUEST['organisations']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_ORG_CHOICES[$s];
				}
				
				if(count($selected)>$limit) {
					$limit=count($selected);
					$_data = $selected;
				} else {
					$limit -= count($selected);
					$_data = array_diff($_data, $selected);
				}
			}
			
			$cnt = 1;
			$checked = "";
			if(!empty($selected)) {
				foreach($selected AS $iso=>$c) {
					$checked = "checked=\"checked\"";
					$cnt++;
					$return .= "<li>
								<input name=\"organisations\" id=\"check-org{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
								<label for=\"check-org{$cnt}\">{$c}</label>
								</li>";
				}
				
				$limit+=$cnt-1;
			}
			foreach($_data AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"organisations\" id=\"check-org{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked} />
							<label for=\"check-org{$cnt}\">{$c}</label>
							</li>";
				if($cnt>$limit) break;
			}
			
			if($limit<count($_ORG_CHOICES)) {
				$add_more = true;
				$generate_popup = true;
			}
			
			break;
		default:
			
			break;
	}
	$return .= "</menu>";
	if($add_more) {
		$href = strtolower($filter) . '_popup';
		$return .= "<a href=\"#{$href}\" class=\"more open-popup\">More..</a>";
	}
	return $return;
	
}

function wp_generate_filter_popup($filter, $limit = 4 ) {
	$limit = intval($limit);
	if($limit<=0) $limit = 4;
	
	$return = '	<div class="lightbox" id="__filter___popup">
					<header class="heading">
						<strong class="title">__filter_name__</strong>
						<a href="#" class="btn-close">save filters</a>
					</header>
					<!-- check-form -->
					<form action="#" class="check-form">
						<fieldset>';
	
	$filter = strtoupper($filter);
	
	switch($filter) {
		case 'COUNTRY':
			global $_COUNTRY_ISO_MAP;
			if(empty($_COUNTRY_ISO_MAP) && !file_exists(TEMPLATEPATH . '/countries.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/countries.php' );
				asort($_REGION_CHOICES);
			}
			
			if($limit>=count($_COUNTRY_ISO_MAP)) {
				return "";
			}
			
			$selected = array();
			if(!empty($_REQUEST['countries'])) {
				$tmp = explode('|', $_REQUEST['countries']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_COUNTRY_ISO_MAP[$s];
				}
			}
			
			$return = preg_replace("/__filter__/", strtolower($filter), $return);
			$return = preg_replace("/__filter_name__/", "Countries", $return);
			
			$fltr_cnt = count($_COUNTRY_ISO_MAP);
			
			if($fltr_cnt%3!=0) $fltr_cnt++;
			while($fltr_cnt%3!=0) {
				$fltr_cnt++;
			}
			
			$items_per_col = $fltr_cnt/3;
			$cnt = 0;
			$return .= "<menu class=\"column\">";
			foreach($_COUNTRY_ISO_MAP AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input id=\"check-country{$cnt}\" class=\"check\" type=\"checkbox\" name=\"countries\" value=\"{$iso}\" {$checked}/>
							<label for=\"check-country{$cnt}\">{$c}</label>
						</li>";
				if($cnt%$items_per_col==0) {
					$return .= "</menu><menu class=\"column\">";
				}
			}
			$return .= "</menu>";
			
			
			break;
		case 'REGION':
			global $_REGION_CHOICES;
			if(empty($_REGION_CHOICES) && !file_exists(TEMPLATEPATH . '/regions.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/regions.php' );
				asort($_REGION_CHOICES);
			}
			
			if($limit>=count($_REGION_CHOICES)) {
				return "";
			}
			
			$selected = array();
			if(!empty($_REQUEST['regions'])) {
				$tmp = explode('|', $_REQUEST['regions']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_REGION_CHOICES[$s];
				}
			}
			
			$return = preg_replace("/__filter__/", strtolower($filter), $return);
			$return = preg_replace("/__filter_name__/", "Regions", $return);
			
			$fltr_cnt = count($_REGION_CHOICES);
			
			if($fltr_cnt%3!=0) $fltr_cnt++;
			while($fltr_cnt%3!=0) {
				$fltr_cnt++;
			}
			
			$items_per_col = $fltr_cnt/3;
			$cnt = 0;
			$return .= "<menu class=\"column\">";
			foreach($_REGION_CHOICES AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"regions\" id=\"check-region{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked}/>
							<label for=\"check-region{$cnt}\">{$c}</label>
						</li>";
				if($cnt%$items_per_col==0) {
					$return .= "</menu><menu class=\"column\">";
				}
			}
			$return .= "</menu>";
			
			
			break;
		case 'SECTOR':
			global $_SECTOR_CHOICES;
			if(empty($_SECTOR_CHOICES) && !file_exists(TEMPLATEPATH . '/sectors.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/sectors.php' );
				asort($_SECTOR_CHOICES);
			}
			
			if($limit>=count($_SECTOR_CHOICES)) {
				return "";
			}
			
			$selected = array();
			if(!empty($_REQUEST['sectors'])) {
				$tmp = explode('|', $_REQUEST['sectors']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_SECTOR_CHOICES[$s];
				}
			}
			
			$return = preg_replace("/__filter__/", strtolower($filter), $return);
			$return = preg_replace("/__filter_name__/", "Sectors", $return);
			
			$fltr_cnt = count($_SECTOR_CHOICES);
			
			if($fltr_cnt%3!=0) $fltr_cnt++;
			while($fltr_cnt%3!=0) {
				$fltr_cnt++;
			}
			
			$items_per_col = $fltr_cnt/3;
			$cnt = 0;
			$return .= "<menu class=\"column\">";
			foreach($_SECTOR_CHOICES AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"sectors\" id=\"check-sector{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked}/>
							<label for=\"check-sector{$cnt}\">{$c}</label>
						</li>";
				if($cnt%$items_per_col==0) {
					$return .= "</menu><menu class=\"column\">";
				}
			}
			$return .= "</menu>";
			
			
			break;
		
		case 'ORGANISATION':
			global $_ORG_CHOICES;
			if(empty($_ORG_CHOICES) && !file_exists(TEMPLATEPATH . '/organisations.php')) {
				wp_generate_constants();
				include_once( TEMPLATEPATH . '/organisations.php' );
				asort($_ORG_CHOICES);
			}
			
			if($limit>=count($_ORG_CHOICES)) {
				return "";
			}
			
			$selected = array();
			if(!empty($_REQUEST['organisations'])) {
				$tmp = explode('|', $_REQUEST['organisations']);
				foreach($tmp AS &$s) {
					$selected[$s] = $_ORG_CHOICES[$s];
				}
			}
			
			$return = preg_replace("/__filter__/", strtolower($filter), $return);
			$return = preg_replace("/__filter_name__/", "Publishers", $return);
			
			$fltr_cnt = count($_ORG_CHOICES);
			
			if($fltr_cnt%3!=0) $fltr_cnt++;
			while($fltr_cnt%3!=0) {
				$fltr_cnt++;
			}
			
			$items_per_col = $fltr_cnt/3;
			$cnt = 0;
			$return .= "<menu class=\"column\">";
			foreach($_ORG_CHOICES AS $iso=>$c) {
				$checked = "";
				if(isset($selected[$iso])) $checked = "checked=\"checked\"";
				$cnt++;
				$return .= "<li>
							<input name=\"organisations\" id=\"check-org{$cnt}\" class=\"check\" type=\"checkbox\" value=\"{$iso}\" {$checked}/>
							<label for=\"check-org{$cnt}\">{$c}</label>
						</li>";
				if($cnt%$items_per_col==0) {
					$return .= "</menu><menu class=\"column\">";
				}
			}
			$return .= "</menu>";
			
			
			break;
		default:
			
			break;
	}
	
	
					
					
	$return .= '		</fieldset>
					</form>
				</div>';
	
	return $return;
}

function wp_generate_results_html(&$meta, &$has_filter) {
	global $_DEFAULT_ORGANISATION_ID, $_PER_PAGE, $_COUNTRY_ISO_MAP;
	$search_url = SEARCH_URL . "activities/?format=json&limit={$_PER_PAGE}";
	if(!empty($_DEFAULT_ORGANISATION_ID)) {
		$search_url .= "&organisations=" . $_DEFAULT_ORGANISATION_ID;
	}
	if(isset($_REQUEST['query']) && !empty($_REQUEST['query'])) {
		$query = rawurlencode($_REQUEST['query']);
		
		$srch_countries = array_map('strtolower', $_COUNTRY_ISO_MAP);
		$srch_countries = array_flip($srch_countries);
		$key = strtolower($query);
		if(isset($srch_countries[$key])) {
			$srch_countries = $srch_countries[$key];
		} else {
			$search_url .= "&query={$query}";
			$srch_countries = null;
		}
		
	}
	
	if(!empty($_REQUEST['countries'])) {
		$countries = explode('|', trim($_REQUEST['countries']));
		foreach($countries AS &$c) $c = trim($c);
		$countries = implode('|', $countries);
		$search_url .= "&countries={$countries}";
		$has_filter = true;
		if(!empty($srch_countries)) {
			$search_url .= "|{$srch_countries}";
		}
	} else {
		if(!empty($srch_countries)) {
			$search_url .= "&countries={$srch_countries}";
			$has_filter = true;
		}
		/*
		if($has_filter!==true) {
			$countries = $_COUNTRY_ISO_MAP;
			unset($countries['WW']);
			$search_url .= "&countries=" . implode('|', array_keys($countries));
		}*/
		
	}
	
	if(!empty($_REQUEST['regions'])) {
		$regions = explode('|', trim($_REQUEST['regions']));
		foreach($regions AS &$c) $c = trim($c);
		$regions = implode('|', $regions);
		$search_url .= "&regions={$regions}";
		$has_filter = true;
	}
	
	if(!empty($_REQUEST['sectors'])) {
		$sectors = explode('|', trim($_REQUEST['sectors']));
		foreach($sectors AS &$c) $c = trim($c);
		$sectors = implode('|', $sectors);
		$search_url .= "&sectors={$sectors}";
		$has_filter = true;
	}
	
	if(!empty($_REQUEST['organisations'])) {
		$organisations = explode('|', trim($_REQUEST['organisations']));
		foreach($organisations AS &$c) $c = trim($c);
		$organisations = implode('|', $organisations);
		$search_url .= "&organisations={$organisations}";
		$has_filter = true;
	}
	
	if(!empty($_REQUEST['budgets'])) {
		$budgets = explode('|', trim($_REQUEST['budgets']));
		//Get the lowest budget from filter and use this one, all the other are included in the range
		ksort($budgets);
		$search_url .= "&statistics__total_budget__gt={$budgets[0]}";
		$has_filter = true;
	}
	
	$back_url = $_SERVER['REQUEST_URI'];
	
	$content = file_get_contents($search_url);
	$result = json_decode($content);
	$meta = $result->meta;
	$objects = $result->objects;
	
	$return = "";

	$return = '';

	if(!empty($objects)) {
		$base_url = get_option('home');
		foreach($objects AS $project) {
			$currency = '';
			if(!empty($project->activity_transactions)) {
				foreach($project->activity_transactions AS $at) {
					if($at->currency=='USD') $currency = '$ ';
					if($at->currency=='GBP') $currency = '£ ';
					if($at->currency=='EUR') $currency = '€ ';
					break;
				}
			}
			$return .= '<tr>
						<td class="col1">
							<strong class="title"><a href="'.$base_url.'/explore-detail/?id='.$project->iati_identifier.'">'.$project->titles[0]->title.'</a></strong>
							<p>'.neat_trim($project->descriptions[0]->description, 70).'</p>
						</td>
						<td>';
			$sep = '';
			foreach($project->recipient_country AS $country) {
				$return .= $sep . '<a href="'.$base_url.'/explore/?countries='.$country->iso.'">' . $country->name . '</a>';
				$sep = ', ';
			}
			$return .= '</td>
						<td>'.$project->start_actual.'</td>
						<td>';
			if(!empty($project->statistics->total_budget)) {
				$return .= $currency.format_custom_number($project->statistics->total_budget);
			} else {
				$return .= 'N/A';
			}
			$return .= '</td>
						<td class="last">';
			$sep = '';
			if(empty($project->activity_sectors)) {
				$return .= EMPTY_LABEL;
			} else {
				foreach($project->activity_sectors AS $sector) {
					$return .= $sep . '<a href="'.$base_url.'/explore/?sectors='.$sector->code.'">' . $sector->name . '</a>';
					$sep = ', ';
				}
			}
			$return .= '</td>
					</tr>
					';
				
		}
	}
	return $return;
	
}

function wp_generate_paging($meta) {
	global $_PER_PAGE;
	//fix the paging 
	$total_count = $meta->total_count;
	$offset = $meta->offset;
	$limit = $meta->limit;
	$per_page = $_PER_PAGE;
	$total_pages = ceil($total_count/$limit);
	$cur_page = $offset/$limit + 1;
	
	$paging_block = '<ul class="paging" id="paging"><li class="link-prev"><a href="javascript:void(0);">previous</a></li>';
	$page_limit = 6;
	$show_dots = true;
		

	for($i=1; $i<=$total_pages; $i++) {
		if ($i == 1 || $i == $total_pages || ($i >= $cur_page - $page_limit && $i <= $cur_page + $page_limit) ) {
			$show_dots = true;
			if($cur_page==$i) {
				$paging_block .= "<li><strong id='cur_page'>{$i}</strong></li>";
			} else {
				$paging_block .= "<li><a href='javascript:void(0);'>{$i}</a></li>";
			}
		
		} else if ($show_dots == true) {
			$show_dots = false;
			$paging_block .= "<li>...</li>";
		}
	}
		
	$paging_block .= '<li class="link-next"><a href="javascript:void(0);">next</a></li></ul>';
	
	echo $paging_block;
}

function wp_get_activity($identifier) {
	if(empty($identifier)) return null;
	$search_url = "http://oipa.openaidsearch.org/api/v2/activities/{$identifier}/?format=json";
	
	$content = file_get_contents($search_url);
	$activity = json_decode($content);
	return $activity;

}

function format_custom_number($num) {
	
	$s = explode('.', $num);
	
	$parts = "";
	if(strlen($s[0])>3) {
		$parts = "." . substr($s[0], strlen($s[0])-3, 3);
		$s[0] = substr($s[0], 0, strlen($s[0])-3);
		
		if(strlen($s[0])>3) {
			$parts = "." . substr($s[0], strlen($s[0])-3, 3) . $parts;
			$s[0] = substr($s[0], 0, strlen($s[0])-3);
			if(strlen($s[0])>3) {
				$parts = "." . substr($s[0], strlen($s[0])-3, 3) . $parts;
				$s[0] = substr($s[0], 0, strlen($s[0])-3);
			} else {
				$parts = $s[0] . $parts;
			}
		} else {
			$parts = $s[0] . $parts;
		}
	} else {
		$parts = $s[0] . $parts;
	}
	
	
	$ret = $parts;
	
	if(isset($s[1])) {
		if($s[1]!="00") {
			$ret .= "," + $s[1];
		}
	}
	
	return $ret;
}

function objectToArray($d) {
	if (is_object($d)) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars($d);
	}

	if (is_array($d)) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ (Magic constant)
		* for recursive call
		*/
		return array_map(__FUNCTION__, $d);
	}
	else {
		// Return array
		return $d;
	}
}

function neat_trim($str, $n, $delim='...') {
   $len = strlen($str);
   if ($len > $n) {
       preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
       return rtrim($matches[1]) . $delim;
   }
   else {
       return $str;
   }
}
?>