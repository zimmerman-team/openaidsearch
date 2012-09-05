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
			    <?php echo filter_template_url(get_post_meta(get_the_ID(), 'Header Image', true)); ?>
			</div>
			<?php endif; ?>
			<?php $activities = wp_generate_results_html($meta); ?>
			<!-- nav-section -->
			<nav class="nav-section">
				<strong class="title">All <mark><?php echo $meta->total_count; ?></mark> projects</strong>
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
			<div class="two-columns">
				<!-- aside -->
				<aside class="aside">
					<!-- buttons-list -->
					<ul class="buttons-list">
						<li><a href="?page_id=16">Reset Filters</a></li>
						<li><a href="#">CSV</a></li>
						<li><a href="#">PDF</a></li>
					</ul>
					<!-- filter-form -->
					<form action="#" class="filter-form" id="filter-form">
						<fieldset>
							<div class="aside-block">
								<h2>IATI sets</h2>
								<menu>
									<li>
										<input id="check-set1" class="check" type="checkbox" />
										<label for="check-set1">World Bank</label>
									</li>
									<li>
										<input id="check-set2" class="check" type="checkbox" />
										<label for="check-set2">African Developm...</label>
									</li>
									<li>
										<input id="check-set3" class="check" type="checkbox" />
										<label for="check-set3">Asian Developme...</label>
									</li>
									<li>
										<input id="check-set4" class="check" type="checkbox" />
										<label for="check-set4">Inter-American D...</label>
									</li>
								</menu>
								<a href="#popup" class="more open-popup">More..</a>
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
						<ul class="paging" id="paging">
							<li class="link-prev"><a href="javascript:#">previous</a></li>
							<li><strong id="cur_page">1</strong></li>
							<li><a href="javascript:#">2</a></li>
							<li><a href="javascript:#">3</a></li>
							<li><a href="javascript:#">4</a></li>
							<li><a href="javascript:#">5</a></li>
							<li><a href="javascript:#">6</a></li>
							<li><a href="javascript:#">7</a></li>
							<li><a href="javascript:#">8</a></li>
							<li><a href="javascript:#">9</a></li>
							<li><a href="javascript:#">10</a></li>
							<li class="link-next"><a href="javascript:#">next</a></li>
						</ul>
					</div>
				</div>
			</div>
	</section>
<?php get_footer(); ?>