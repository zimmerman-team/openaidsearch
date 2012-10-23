<?php
/*
Template Name: Project details
*/
$back_url = $_REQUEST['back_url'];
if(empty($back_url) && !empty($_SERVER['HTTP_REFERER'])) $back_url = $_SERVER['HTTP_REFERER'];

$project_id = $_REQUEST['id'];
//print_r($_REQUEST);
$activity = wp_get_activity($project_id);

?>
<?php get_header(); ?>
	<!-- main -->
	<section id="main">
		<?php get_search_form(); ?>
		<!-- nav-section -->
		<nav class="nav-section" style="display: none;">
			<strong class="title">All <mark>2656</mark> IATI sets</strong>
			<!-- info-list -->
			<ul class="info-list" id="info-list">
				<li><a href="#">World bank</a>
					<div class="drop">
						<ul>
							<li class="remove"><a href="#">Remove</a></li>
							<li><a href="#">African Dev..</a></li>
							<li><a href="#">Asian Dev..</a></li>
							<li><a href="#">Inter Amer..</a></li>
							<li class="more"><a href="#popup" class="open-popup">More..</a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">€ 1.000.000</a>
					<div class="drop">
						<ul>
							<li class="remove"><a href="#">Remove</a></li>
							<li><a href="#">€ 10.000</a></li>
							<li><a href="#">€ 50.000</a></li>
							<li><a href="#">€ 100.000</a></li>
							<li><a href="#">€ 500.000</a></li>
							<li><a href="#">€ 1.000.000</a></li>
							<li><a href="#">€ 5.000.000</a></li>
							<li><a href="#">€ 10.000.000</a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Central America</a>
					<div class="drop">
						<ul>
							<li class="remove"><a href="#">Remove</a></li>
							<li><a href="#">Caribbean</a></li>
							<li><a href="#">Eastern Africa</a></li>
							<li><a href="#">Eastern Asia</a></li>
							<li class="more"><a href="#popup" class="open-popup">More..</a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Advanced technical and managerial training</a>
					<div class="drop">
						<ul>
							<li class="remove"><a href="#">Remove</a></li>
							<li><a href="#">Advanced technical and managerial training</a></li>
							<li><a href="#">Agrarian reform</a></li>
							<li><a href="#">Agricultural co-operatives</a></li>
							<li class="more"><a href="#popup" class="open-popup">More..</a></li>
						</ul>
					</div>
				</li>
				<li><a href="#">Suriname</a>
					<div class="drop">
						<ul>
							<li class="remove"><a href="#">Remove</a></li>
							<li><a href="#">Algeria</a></li>
							<li><a href="#">Angola</a></li>
							<li><a href="#">Argentina</a></li>
							<li class="more"><a href="#popup" class="open-popup">More..</a></li>
						</ul>
					</div>
				</li>
			</ul>
		</nav>
		<!-- two-columns -->
		<div class="two-columns" style="display: none;">
			<!-- aside -->
			<aside class="aside">
				<!-- buttons-list -->
				<ul class="buttons-list">
					<li><a href="#">Reset Filters</a></li>
					<!-- temp hide this	<li><a href="#">CSV</a></li>
					<li><a href="#">PDF</a></li>-->
				</ul>
			</aside>
		</div>
		<?php 
			$total_disbursments = 0;
			$currency = '';
			foreach($activity->activity_transactions AS $at) {
				$total_disbursments += floatval($at->value);
				if($at->currency=='USD') $currency = 'US$ ';
				if($at->currency=='GBP') $currency = '£ ';
				if($at->currency=='EUR') $currency = '€ ';
				
				
			}
		?>
		<!-- details-block -->
		<section class="details-block">
			<!-- details-box -->
			<div class="details-box">
				<div class="holder">
					<h1><?php echo $activity->titles[0]->title; ?></h1>
					<p><?php echo $activity->descriptions[0]->description; ?></p>
				</div>
			</div>
			<!-- intro-block -->
			<div class="intro-block">
				<!-- info-block -->
				<div class="info-block">
						<div class="box">
						<div class="frame">
							<strong class="title">Country Information</strong>
							<span class="note"><?php
							$sep = '';
							foreach($activity->recipient_country AS $country) {
								echo  $sep . '<a href="'.get_option('home').'/?page_id=16&countries=' . $country->iso . '">' . $country->name . '</a>';
								$sep = ', ';
							}
							?></span>
						</div>
				</div>
				<div class="box">
						<div class="frame green">
								<strong class="title">Total Budget</strong>
								<span class="note"><?php echo $currency; ?><?php echo format_custom_number($activity->statistics->total_budget); ?></span>
						</div>
				</div>
				<div class="box">
						<div class="frame orange">
								<strong class="title">Total Disbursements</strong>
								<span class="note"><?php echo $currency; ?><?php
									
									echo format_custom_number($total_disbursments);
							?></span>
						</div>
				</div>
				<div class="box">
						<div class="frame brown">
								<strong class="title">Total Commitments</strong>
								<span class="note"><?php echo $currency; ?>N/A</span>
						</div>
				</div>
				</div>
				<!-- diagram-block -->
				<article class="details-box diagram-block" id="piechart">
					
				</article>
			</div>
			<?php if (have_posts()) : ?>	
			<?php while (have_posts()) : the_post(); ?>
			<!-- details-section -->
			<section class="details-section">
				<div class="row">
					<!-- details-box -->
					<div class="details-box">
						<div class="holder">
							<h2>Activity Information</h2>
							<!-- details-list -->
							<dl class="details-list">
								<dt>IATI Identifier</dt>
								<dd><?php echo $activity->iati_identifier; ?></dd>
								<?php if(!empty($activity->reporting_organisation->org_name)) {?>
									<dt>Reporting Organisation</dt>
									<dd><?php echo $activity->reporting_organisation->org_name; ?></dd>
								<?php } ?>
								<dt>Sector</dt>
								<dd><?php
									$sep = '';
									foreach($activity->activity_sectors AS $sector) {
										echo  $sep . '<a href="?page_id=16&sectors=' . $sector->code . '">' . $sector->name . '</a>';
										$sep = ', ';
									}
								?></dd>
								<dt>Sector code</dt>
								<dd><?php
									$sep = '';
									foreach($activity->activity_sectors AS $sector) {
										echo  $sep . $sector->code;
										$sep = ', ';
									}
								?></dd>
								<dt>Last updated</dt>
								<?php if(!empty($activity->date_updated)) {?>
									<dd><?php echo $activity->date_updated; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Start date planned</dt>
								<?php if(!empty($activity->start_planned)) {?>
									<dd><?php echo $activity->start_planned; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Start date actual</dt>
								<?php if(!empty($activity->start_actual)) {?>
									<dd><?php echo $activity->start_actual; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>End date planned</dt>
								<?php if(!empty($activity->end_planned)) {?>
									<dd><?php echo $activity->end_planned; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>End date actual</dt>
								<?php if(!empty($activity->end_actual)) {?>
									<dd><?php echo $activity->end_actual; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Collaboration type</dt>
								<?php if(!empty($activity->collaboration_type->name)) {?>
									<dd><?php echo $activity->collaboration_type->code; ?>. <?php echo $activity->collaboration_type->name; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Flow type</dt>
								<?php if(!empty($activity->default_flow_type->name)) {?>
									<dd><?php echo $activity->default_flow_type->name; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Aid type</dt>
								<?php if(!empty($activity->default_aid_type->name)) {?>
									<dd><?php echo $actiivity->default_aid_type->code?>. <?php echo $actiivity->default_aid_type->name?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Finance type</dt>
								<?php if(!empty($activity->default_finance_type->name)) {?>
									<dd><?php echo $activity->default_finance_type->name; ?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Tying status</dt>
								<?php if(!empty($activity->default_tied_status_type->name)) {?>
									<dd><?php $activity->default_tied_status_type->name?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
								<dt>Activity status</dt>
								<?php if(!empty($activity->activity_status->name)) {?>
									<dd><?php $activity->activity_status->name?></dd>
								<?php } else { ?>
									<dd><?php echo EMPTY_LABEL; ?></dd>
								<?php } ?>
							</dl>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- details-box -->
					<div class="details-box">
						<div class="holder">
							<h2>Participating Organisations</h2>
							<!-- details-list -->
							<?php echo EMPTY_LABEL; ?>
							<dl class="details-list" style="display: none;">
								<dt>Name</dt>
								<dd>Ministry of Foreign Affairs (DGIS)</dd>
								<dt>Type</dt>
								<dd>Government</dd>
								<dt>Organisation reference code</dt>
								<dd>NL-1</dd>
							</dl>
						</div>
					</div>
				</div>
				<div class="row">
					<!-- details-box -->
					<div class="details-box">
						<div class="holder">
							<h2>Commitments</h2>
							<!-- details-list -->
							<?php echo EMPTY_LABEL; ?>
							<dl class="details-list" style="display: none;">
								<dt>Activity</dt>
								<dd><?php echo $activity->title->default; ?></dd>
								<dt>Provider org</dt>
								<dd><?php echo $activity->reporting_organisation->org_name; ?></dd>
								<dt>Receiver org</dt>
								<dd>MINISTERIE VAN NATUURLIJKE HULPBRONNEN</dd>
								<dt>Value</dt>
								<dd>€ </dd>
								<dt>Transaction date</dt>
								<dd></dd>
							</dl>
						</div>
					</div>
				</div>
				
				<div class="row">
					<!-- details-box -->
					<div class="details-box">
						<div class="holder">
							<h2>Disbursements</h2>
							<!-- chart-block -->
							<div class="chart-block" id='barchart'>
							</div>
							<?php
									$disbursements = array();
									
									foreach($activity->activity_transactions AS $idx=>$at) {
											$disbursements[$at->transaction_date] = $at->value;
											$cur = '';
											if($at->currency=='USD') $cur = 'US$ ';
											if($at->currency=='GBP') $cur = '£ ';
											if($at->currency=='EUR') $cur = '€ ';
										echo "<!-- details-list -->
											<dl class=\"details-list\">
													<dt>Activity</dt>
													<dd>{$activity->titles[0]->title}</dd>
													<dt>Provider org</dt>
													<dd>{$activity->reporting_organisation->org_name}</dd>
													<dt>Value</dt>
													<dd>{$cur}".format_custom_number($at->value)."</dd>
													<dt>Transaction date</dt>
													<dd>{$at->transaction_date}</dd>
											</dl>";
										
									}
									ksort($disbursements);
							?>
						</div>
					</div>
				</div>
			</section>
			<?php endwhile; ?>
			<?php endif; ?>
		</section>
	</section>
	<script type="text/javascript" charset="utf-8">
		jQuery(document).ready(function() {
			var tmp = <?php echo json_encode($disbursements); ?>;
			var categories = [], data = [];
			data[0] = [];
			data[0]['data'] = [];
			data[0]['name'] = 'Disbursments';
			var cnt = 0;
			for(idx in tmp) {
				categories[cnt] = idx;				
				data[0]['data'][cnt] = parseFloat(tmp[idx]);
				cnt++;
			}
			chart1 = new Highcharts.Chart({
				 chart: {
					renderTo: 'piechart',
					type: 'pie',
					backgroundColor: "#EEEEEE"
				 },
				 credits: {
					enabled: false
				 },
				 title: {
					text: ' '
				 },
				 plotOptions: {
					pie: {
						allowPointSelect: true,
						cursor: 'pointer',
						dataLabels: {
							enabled: true,
							color: '#000000',
							connectorColor: '#000000',
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: <?php echo $currency; ?>'+ this.y;
							}	
						}
					}
				}, 
				 series: [{
					type: 'pie',
					name: 'Amount',
					data: [
						['Total Budget', parseFloat(<?php echo (!empty($activity->statistics->total_budget)?$activity->statistics->total_budget:0); ?>)],
						['Total Disbursements', parseFloat(<?php echo $total_disbursments; ?>)]
					]
				}] 
			  });
			chart2 = new Highcharts.Chart({
				 chart: {
					renderTo: 'barchart',
					type: 'column',
					backgroundColor: "#EEEEEE"
				 },
				 title: {
					text: 'Disbursments'
				 },
				 credits: {
					enabled: false
				 },
				 xAxis: {
					title: {
					   text: 'Date'
					},
					categories: categories
				 },
				 yAxis: {
					title: {
					   text: 'Amount'
					}
				 },
				 series: data
			  });
		});
	</script>
<?php get_footer(); ?>