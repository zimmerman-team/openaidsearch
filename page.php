<?php get_header(); ?>
	<section id="main">
		<!-- block -->
		<?php if(get_post_meta(get_the_ID(), 'intro-section', true)): ?>
		<section class="block intro-section intro-box">
			<div class="holder">
				<?php echo get_post_meta(get_the_ID(), 'intro-section', true); ?>
			</div>
		</section>
		<?php endif; ?>
		<!-- content -->
		<section id="content">
			<div class="content-block">
			<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post();?>				
				<header class="heading">
					<h2><?php the_title(); ?></h2>
					<?php the_content(); ?>
				</header>
			<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>				
			</div>
		</section>
	</section>	
<?php get_footer(); ?>