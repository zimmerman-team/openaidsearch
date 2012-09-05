<?php
/*
Template Name: Posts Template
*/
?>
<?php get_header(); ?>
	<!-- main -->
	<section id="main">
		<!-- block -->
		<section class="block intro-section">
			<div class="holder">
				<?php echo get_post_meta(get_the_ID(), 'intro-section', true); ?>
			</div>
		</section>
		<!-- content -->
		<section id="content">
			<?php	query_posts(array('category_name' => get_post_meta(get_the_ID(), 'category', true))); ?>
			<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post();?>
			<div class="content-block">
				<header class="heading">
					<h2><?php the_title(); ?></h2>
					<div class="text-holder">
						<?php the_content(); ?>
					</div>
				</header>
				<?php
				$images = get_children( 'post_parent='.get_the_ID().'&post_type=attachment&post_mime_type=image&orderby=menu_order&order=ASC' );
				if(is_array($images)):
				echo '<!-- image-block -->
				<div class="image-block">';
				foreach($images as $image): 
					 echo wp_get_attachment_image( $image->ID, 'thumbnail_940_300');
				endforeach;
				echo '</div>';
				endif;
				?>

			</div>
			<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</section>
	</section>
<?php get_footer(); ?>