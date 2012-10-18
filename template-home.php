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
									<li><a style="font-size:48px; line-height:52px; color:#fff;" href="?page_id=20&id=NL-1-PPR-14399">Rabobank in Zambia</a></li>
									<li><a style="font-size:24px; line-height:27px; color:#9cf;" href="#">Ipsum adipiscing</a></li>
									<li><a style="font-size:39px; line-height:42px; color:#00468c;" href="#">Cras</a></li>
									<li><a style="font-size:14px; line-height:17px; color:#fff;" href="#">Cursus dolor fermentum</a></li>
									<li><a style="font-size:38px; line-height:42px; color:#00468c;" href="#">Publish your iati data?</a></li>
									<li><a style="font-size:52px; line-height:54px; color:#9cf;" href="#">Tellus etiam ornare</a></li>
									<li><a style="font-size:19px; line-height:23px; color:#fff;" href="#">Fusce ornare bibendum dolor</a></li>
									<li><a style="font-size:28px; line-height:32px; color:#00468c;" href="#">Vestibulum id ligula porta felis euismod semper.</a></li>
									<li><a style="font-size:35px; line-height:38px; color:#9cf;" href="#">Fermentum pellentesque vulputate</a></li>
									<li><a style="font-size:50px; line-height:52px; color:#00468c;" href="#">Risus euismod</a></li>
									<li><a style="font-size:35px; line-height:38px; color:#00468c;" href="#">Vehicula risus tristique magna</a></li>
									<li><a style="font-size:24px; line-height:27px; color:#fff;" href="#">Lorem parturient egestas magna</a></li>
									<li><a style="font-size:14px; line-height:17px; color:#fff;" href="#">Cursus dolor fermentum</a></li>
									<li><a style="font-size:24px; line-height:27px; color:#fff;" href="#">Elit egestas ultricies</a></li>
									<li><a style="font-size:28px; line-height:32px; color:#00468c;" href="#">Amet justo fermentum</a></li>
									<li><a style="font-size:19px; line-height:23px; color:#9cf;" href="#">Malesuada parturient</a></li>
									<li><a style="font-size:21px; line-height:24px; color:#00468c;" href="#">Adipiscing</a></li>
									<li><a style="font-size:14px; line-height:17px; color:#9cf;" href="#">Maecenas sed diam eget risus varius blandit sit amet non magna.</a></li>
									<li><a style="font-size:17px; line-height:20px; color:#00468c;" href="#">Quam amet malesuada</a></li>
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