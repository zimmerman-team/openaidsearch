			
		<!-- footer -->
		<div id="footer" style="display: block;">
			<div class="footer-holder">
				<div class="footer-frame">
					<div class="footer-block">
                                            <ul class="partner-list">
        <li><a href="http://akvo.org/"><img src="<?php bloginfo('url');?>/wp-content/themes/oipa3/images/logo_akvo.png" width="169" height="66" alt="akvo.org See it happen"></a></li>
        <li><a href="http://www.zimmermanzimmerman.nl/"><img src="<?php bloginfo('url');?>/wp-content/themes/oipa3/images/logo_zz.png" width="218" height="63" alt="image"></a></li>
        <li><a href="http://iatiregistry.org/"><img src="<?php bloginfo('url');?>/wp-content/themes/oipa3/images/logo_iati.png" width="225" height="72" alt="IATI International Aid Transparency Initiative"></a></li>
        
</ul>
					
						<?php if(is_active_sidebar('partners')): ?>
						<!-- partner-block -->
						<div class="partner-block">
							<?php //dynamic_sidebar('partners'); ?>
                                                    	</div>
						<?php endif; ?>
						<!-- footer-row -->
						<div class="footer-row">
							<!-- footer navigation -->
							<nav class="footer-nav">
								<?php
//								wp_nav_menu( array('container' => false,
//									 'theme_location' => 'footer_menu',
//									 'menu_class' => 'navigation',
//									 ) );
								?>
							</nav>
							<?php if(is_active_sidebar('copyright')): ?>
								<!-- copyright -->
								<span class="copyright"><?php dynamic_sidebar('copyright'); ?></span>
							<?php endif; ?> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
        <?php if(is_page_template('template-general.php') || is_page_template('template-contact.php')): ?>
	<!-- lightbox -->
	<div class="lightbox" id="popup1">
		<div class="holder">
			<header class="headline">
				<h1>Yes, let’s talk about publishing aid transparency!</h1>
				<p>Are you interested in publishing your IATI set? Please reach out and one of our OIPA team members will get back to you swiftly!</p>
			</header>
			<!-- profile-form -->
                        <?php echo do_shortcode('[contact-form-7 id="47" title="Contact form 1"]'); ?>
		</div>
	</div>        
        <?php endif; ?>
	
        <?php if(is_page_template('template-explore.php') || is_page_template('template-pdetails.php')): ?>
			<?php echo wp_generate_filter_popup('organisation'); ?>
			<?php echo wp_generate_filter_popup('country'); ?>
			<?php echo wp_generate_filter_popup('region'); ?>
			<?php echo wp_generate_filter_popup('sector'); ?>
        <?php endif; ?>	
	
	<?php wp_footer(); ?>
</body>
</html>