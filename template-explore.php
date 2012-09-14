<?php
/*
Template Name: Explore Template
*/
?>
<?php get_header(); ?>
	<!-- main -->
	<section id="main">
		<?php get_search_form(); ?>
			<?php if ( get_post_meta(get_the_ID(), 'Header Image', true) ) : ?>
			<!-- map -->
			<div class="map-block">			
			    <div id="map_canvas" style="width: 940px; height: 300px;"></div>
			</div>
			<?php endif; ?>
			<?php $activities = wp_generate_results_html($meta, $has_filter); ?>
			<!-- nav-section -->
			<nav class="nav-section">
				<strong class="title">Found <mark><?php echo $meta->total_count; ?></mark> activities</strong>
				<!-- info-list -->
				<ul class="info-list" id="info-list" <?php echo ($has_filter?'':' style="display: none;"')?>>
					<?php 
					
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
						if(!empty($_REQUEST['organisations'])) {
							$tmp = explode('|', $_REQUEST['organisations']);
							foreach($tmp AS $idx => &$s) {
								echo '<li><a href="#">'.$_ORG_CHOICES[$s].'</a>';
								unset($tmp[$idx]);
								break;
							}
							echo '<div class="drop">
									<ul>
										<li class="remove"><a href="#" id="organisations">Clear</a></li>';
							
							if(!empty($tmp)) {
								foreach($tmp AS $idx => &$s) {
									echo '<li><a href="#">'.$_ORG_CHOICES[$s].'</a></li>';
								}
							}
							
							echo "</ul>
								</div></li>";
						}
						if(!empty($_REQUEST['budgets'])) {
							$tmp = explode('|', $_REQUEST['budgets']);
							foreach($tmp AS $idx => &$s) {
								echo '<li><a href="#">'.$_BUDGET_CHOICES[$s].'</a>';
								unset($tmp[$idx]);
								break;
							}
							echo '<div class="drop">
									<ul>
										<li class="remove"><a href="#" id="budgets">Clear</a></li>';
							
							if(!empty($tmp)) {
								foreach($tmp AS $idx => &$s) {
									echo '<li><a href="#">'.$_BUDGET_CHOICES[$s].'</a></li>';
								}
							}
							
							echo "</ul>
								</div></li>";
						}
						if(!empty($_REQUEST['regions'])) {
							$tmp = explode('|', $_REQUEST['regions']);
							foreach($tmp AS $idx => &$s) {
								echo '<li><a href="#">'.$_REGION_CHOICES[$s].'</a>';
								unset($tmp[$idx]);
								break;
							}
							echo '<div class="drop">
									<ul>
										<li class="remove"><a href="#" id="regions">Clear</a></li>';
							
							if(!empty($tmp)) {
								foreach($tmp AS $idx => &$s) {
									echo '<li><a href="#">'.$_REGION_CHOICES[$s].'</a></li>';
								}
							}
							
							echo "</ul>
								</div></li>";
						}
						if(!empty($_REQUEST['sectors'])) {
							$tmp = explode('|', $_REQUEST['sectors']);
							foreach($tmp AS $idx => &$s) {
								echo '<li><a href="#">'.$_SECTOR_CHOICES[$s].'</a>';
								unset($tmp[$idx]);
								break;
							}
							echo '<div class="drop">
									<ul>
										<li class="remove"><a href="#" id="sectors">Clear</a></li>';
							
							if(!empty($tmp)) {
								foreach($tmp AS $idx => &$s) {
									echo '<li><a href="#">'.$_SECTOR_CHOICES[$s].'</a></li>';
								}
							}
							
							echo "</ul>
								</div></li>";
						}
						if(!empty($_REQUEST['countries'])) {
							$tmp = explode('|', $_REQUEST['countries']);
							$countries = "";
							$cSep = "";
							foreach($tmp AS $idx => &$s) {
								echo '<li><a href="#">'.$_COUNTRY_ISO_MAP[$s].'</a>';
								$countries .= $cSep . $s;
								$cSep = '|';
								unset($tmp[$idx]);
								break;
							}
							echo '<div class="drop">
									<ul>
										<li class="remove"><a href="#" id="countries">Clear</a></li>';
							
							if(!empty($tmp)) {
								foreach($tmp AS $idx => &$s) {
									echo '<li><a href="#">'.$_COUNTRY_ISO_MAP[$s].'</a></li>';
									$countries .= $cSep . $s;
									$cSep = '|';
								}
							}
							
							if(!empty($srch_countries) && !in_array($srch_countries, $tmp)) {
								echo '<li><a href="#">'.$_COUNTRY_ISO_MAP[$srch_countries].'</a></li>';
								$countries .= $cSep . $srch_countries;
							}
							
							echo "</ul>
								</div></li>";
						} else {
							if(!empty($srch_countries)) {
								echo '<li><a href="#">'.$_COUNTRY_ISO_MAP[$srch_countries].'</a>';
								echo '<div class="drop">
										<ul>
											<li class="remove"><a href="#" id="countries">Clear</a></li>';
								echo "</ul>
									</div></li>";
								$countries = $srch_countries;
							}
						}
					?>
				</ul>
			</nav>
			<!-- two-columns -->
			<div class="two-columns">
				<!-- aside -->
				<aside class="aside">
					<!-- buttons-list -->
					<ul class="buttons-list">
						<li><a href="?page_id=16">Reset Filters</a></li>
						<!-- temp hide this	<li><a href="#">CSV</a></li>
						<li><a href="#">PDF</a></li> -->
					</ul>
					<!-- filter-form -->
					<form action="#" class="filter-form" id="filter-form">
						<fieldset>
							<div class="aside-block">
								<h2>Publishers</h2>
								<?php echo wp_generate_filter_html('organisation'); ?>
							</div>
							<!-- aside-block -->
							<div class="aside-block">
								<h2>Budget</h2>
								<?php echo wp_generate_filter_html('budget'); ?>
							</div>
							<!-- aside-block -->
							<div class="aside-block">
								<h2>Countries</h2>
								<?php echo wp_generate_filter_html('country'); ?>
							</div>
							<!-- aside-block -->
							<div class="aside-block">
								<h2>Regions</h2>
								<?php echo wp_generate_filter_html('region'); ?>
							</div>
							<!-- aside-block -->
							<div class="aside-block">
								<h2>Sectors</h2>
								<?php echo wp_generate_filter_html('sector'); ?>
							</div>
						</fieldset>
					</form>
				</aside>
				<!-- pages-block -->
				<div class="pages-block">
					<!-- info-table -->
					<table class="info-table" id="info-table">
						<thead>
							<tr>
								<th class="col1">Title</th>
								<th>Country</th>
								<th>Start Date</th>
								<th>Budget</th>
								<th class="last">Sector</th>
							</tr>
						</thead>
						<tbody>
							<?php echo $activities; ?>
						</tbody>
					</table>
					<!-- paging-block -->
					<div class="paging-block" id="paging-block">
						<input type="hidden" id="total_results" value="<?php echo $meta->total_count; ?>">
						<!-- counter-form -->
						<form action="#" class="counter-form" id="counter-form">
							<fieldset>
								<select name="perpage">
									<option value="10">10</option>
									<option value="20" selected>20</option>
									<option value="50">50</option>
									<option value="100">100</option>
								</select>
							</fieldset>
						</form>
						<!-- paging -->						
						<?php echo wp_generate_paging($meta); ?>
					</div>
				</div>
			</div>
	</section>
	<script type="text/javascript" charset="utf-8">
		function initPageMap(country) {
			var baseUrl = top.location.pathname.toString(),
			url = "<?php bloginfo('template_directory') ?>/map_search.php?countries=<?php echo $countries ?><?php echo $search_url; ?>";
			var countries = '<?php echo $countries ?>';

			jQuery.ajax({
				url: url,
				type: "GET",
				dataType: "json",
				success: function(data){
					initMap(data);
				},
				error: function(msg){
					alert('AJAX error!' + msg);
					return false;
				}
			});


			function initMap(result) {
				var myLatLng = new google.maps.LatLng(9.795678,26.367188);
				var myOptions = {
					zoom : 2,
					center : myLatLng,
					mapTypeId : google.maps.MapTypeId.TERRAIN,
					scrollwheel: false,
					streetViewControl : false
				};

				var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
				if (!google.maps.Polygon.prototype.getBounds) {

					google.maps.Polygon.prototype.getBounds = function(latLng) {

							var bounds = new google.maps.LatLngBounds();
							var paths = this.getPaths();
							var path;
							
							for (var p = 0; p < paths.getLength(); p++) {
									path = paths.getAt(p);
									for (var i = 0; i < path.getLength(); i++) {
											bounds.extend(path.getAt(i));
									}
							}

							return bounds;
					}

				}
				var data = result['objects'];
				for(idx in data) {
					var lats = [];
					var lat_size =  data[idx]['path'].length;

					for (var t=0; t <lat_size; t++) {
						var inner = [];
						for (var i=0; i <data[idx]['path'][t].length; i++) {
							var lat = data[idx]['path'][t][i].split(',');
							inner.push(new google.maps.LatLng(lat[0], lat[1]));
						}
						lats.push(inner);
					}
					var polygon = new google.maps.Polygon({
						paths: lats,
						strokeColor: "#FFFFFF",
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: "#F96B15",
						fillOpacity: 0.65,
						country: data[idx]['name'],
						total_cnt: data[idx]['total_cnt'],
						total_activities_url: "?countries="+idx,
						iso2 : idx
					});
					if (countries) map.setCenter(polygon.getBounds().getCenter());
					polygon.setMap(map);
					google.maps.event.addListener(polygon, 'click', showInfo);
					infowindow = new google.maps.InfoWindow();
					google.maps.event.addListener(infowindow, 'closeclick', resetColor);
				}
				
				function showInfo(event){
					if (typeof currentPolygon != 'undefined') {
						currentPolygon.setOptions({fillColor: "#F96B15"});
					}
					this.setOptions({fillColor: "#2D6A98"});
					var keyword = jQuery('#search-field').val();
					
					if(keyword) {
						keyword = encodeURI(keyword);
					}
					var contentString = "" + 
					"<h2>" + 
						"<img src='<?php echo bloginfo('template_url'); ?>/images/flags/" + this.iso2.toLowerCase() + ".gif' />" +
						this.country + 
					"</h2>" +
					"<dl>" +
					"<dt>Total Activities:</dt>" +
					"<dd>" +
						"<a href='<?php echo get_option('home'); ?>/?page_id=16&query=" + keyword + "&countries=" + this.iso2 + "'>"+this.total_cnt+" project(s)</a>" +
					"</dd>" +
						"<a href='<?php echo get_option('home'); ?>/?page_id=16&query=" + keyword + "&countries=" + this.iso2 + "'>show all activities for this country</a>" +
					"</dl>";
					
					infowindow.setContent(contentString);
					infowindow.setPosition(event.latLng);
					infowindow.open(map);
					currentPolygon = this;
				}
				
				function resetColor(){
					currentPolygon.setOptions({fillColor: "#F96B15"});
				}
			}
		}
		jQuery(document).ready(function() {
			var script = document.createElement("script");
			script.type = "text/javascript";
			script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initPageMap";
			document.body.appendChild(script);
		});
	</script>
<?php get_footer(); ?>