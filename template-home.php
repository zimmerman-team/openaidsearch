<?php
/*
Template Name: Home Template
*/
?>
<?php get_header(); ?>
        
	<!-- main -->
	<section id="main">
		<?php get_search_form(); ?>
            <section class="block">
                <div class="holder">
                    <!-- tagcloud -->
                       <ul class="tagcloud">
                        <li><a style="padding-left: 200px;font-size:16px; line-height:18px; color:#fff;" target="_blank" href="http://beta.openaidsearch.org/explore/?query&sectors=22040%7CCA">ICT projects around the world</a></li>
                        <li><a style="padding-right: 100px;font-size:22px; line-height:26px; color:#2E2C75;" target="_blank" href="http://beta.openaidsearch.org/explore/?sectors=15153">Media and free flow of information</a></li>
                        <li><a style="font-size:26px; line-height:30px; color:#8c6800;" target="_blank" href="http://beta.openaidsearch.org/explore/?query&regions=589&sectors=15220">Civilian peace-building & conflict prevention in the Middle East</a></li>
                        <li><a style="font-size:24px; line-height:26px; color:#fff;" target="_blank" href="http://beta.openaidsearch.org/explore/?query&organisations=41120%7C41114%7C41AAA">The UNDP, UN-Habitat and UNOPS clusters</a></li>
                        <li><a style="padding-left: 120px;font-size:38px; line-height:42px; color:#00468c;" href="http://beta.openaidsearch.org/explore">FIND +75.000 GLOBAL DEVELOPMENT PROJECTS</a></li>
                        <li><a style="padding-left: 80px;padding-right: 120px;font-size:26px; line-height:30px; color:#fff;" target="_blank" href="http://beta.openaidsearch.org/explore/?query&countries=AF">Aid project in Afghanistan</a></li>
                        <li><a style="padding-left: 100px;font-size:19px; line-height:23px; color:#2E2C75;"target="_blank" href="http://beta.openaidsearch.org/explore/?query&sectors=23068">Wind power projects around the world</a></li>
                        <li><a style="padding-right: 20px;font-size:30px; line-height:32px; color:#8c6800;" target="_blank" href="http://beta.openaidsearch.org/explore-detail/?id=44000-P120234">US$50M for cultural heritage conservation in China</a></li>
                        <li><a style="font-size:18px; padding-left:200px; line-height:22px; color:#fff;" href="http://beta.openaidsearch.org/explore/?query&sectors=11130">Training of teachers</a></li>
                        
                    </ul>
                </div>
            </section>
		<!-- block -->
<!--		<section class="block">
			<div class="holder">
				 tagcloud 
				<?php
//				$tagcloud = wp_tag_cloud_custom('number=0&format=array&orderby=name&order=ASC');
//                                
//				echo '<ul class="tagcloud">';
//				for($i=0; $i<TAG_COUNT; $i++){
//					echo '<li>'.$tagcloud[$i].'</li>';					
//				}								
//				echo '</ul>';
				?>
			</div>
		</section>-->
                
		<!-- three-columns temp hide -->
		<section class="three-columns">

			<?php	query_posts(array('showposts' => 3, 'post__in'=>get_option('sticky_posts') )); ?>
			<?php if (have_posts()) : ?>
			<div class="holder">
				<?php
				$i = 0;
				while (have_posts()) : the_post();?>
				<?php if($i<3): ?>
					<article class="column">
						<?php $link = get_post_meta(get_the_ID(), 'link', 1) ? str_replace('[site-url]', get_bloginfo('url'),get_post_meta(get_the_ID(), 'link', 1)) : get_permalink()?>
						<h2><a href="<?php echo $link; ?>"><?php the_title(); ?></a></h2>
						<div class="image-holder">
							<a href="<?php echo $link; ?>"><?php the_post_thumbnail( 'thumbnail_260_120' ); ?></a>
						</div>
						<div class="text-block">
							<a href="<?php echo $link; ?>">
								<?php the_content(); ?>
								<span class="more">More..</span>
							</a>
						</div>
					</article>
				<?php endif; $i++; ?>
				<?php endwhile; ?>
			</div>
			<?php endif; ?>
			
			
			<!-- form-block  temp hide -->
<!--			<div class="form-block">
				<h2>OIPA newsletter</h2>
					 
					<form action="#" class="newsletter-form">
						<fieldset>
							<div class="row">
								<label for="email-field">* Email</label>
								<div class="box">
									<input class="text" id="email-field" type="text" value="" />
								</div>
							</div>
							<div class="row">
								<label for="fname-field">First name</label>
								<div class="box">
									<input class="text" id="fname-field" type="text" value="" />
								</div>
							</div>
							<div class="row">
								<label for="lname-field">Last name</label>
								<div class="box">
									<input class="text" id="lname-field" type="text" value="" />
								</div>
							</div>
							<strong class="req">* Required field</strong>
							<div class="radio-block">
								<input id="radio-signin2" class="radio" name="radio-sign" type="radio" />
								<label for="radio-signin2">sign in</label>
								<input id="radio-signout2" class="radio" name="radio-sign" type="radio" />
								<label for="radio-signout2">sign out</label>
							</div>
							<input class="btn-submit" type="submit" value="submit" />
						</fieldset>
					</form>
			</div>-->
		</section>
	</section>
	
	
	

<?php get_footer(); ?>
