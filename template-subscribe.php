<?php
/*
Template Name: Subscribe Template
*/
?>
<?php get_header(); ?>
	<section id="main">
		<!-- content -->
		<section id="content">
			<div class="content-block">
				<header class="heading">
					<h2><?php the_title(); ?></h2>
					<!-- newsletter-form -->
					<div class="newsletter-form">
						<!-- newsletter-form -->
						<form action="#" class="newsletter-form">
							<fieldset>
								<div class="row">
									<label for="email1-field">* Email</label>
									<input class="text" id="email1-field" type="text" value="" />
								</div>
								<div class="row">
									<label for="fname-field">First name</label>
									<input class="text" id="fname-field" type="text" value="" />
								</div>
								<div class="row">
									<label for="lname-field">Last name</label>
									<input class="text" id="lname-field" type="text" value="" />
								</div>
								<strong class="req">* Required field</strong>
								<div class="radio-block">
									<input id="radio-sign-in" class="radio" name="radio-sign" type="radio" />
									<label for="radio-sign-in">sign in</label>
									<input id="radio-sign-out" class="radio" name="radio-sign" type="radio" />
									<label for="radio-sign-out">sign out</label>
								</div>
								<input class="btn-submit" type="submit" value="submit" />
							</fieldset>
						</form>
					</div>
				</header>
			</div>
		</section>
	</section>	
<?php get_footer(); ?>