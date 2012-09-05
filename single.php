<?php get_header(); ?>

<section id="main">
	<?php if (have_posts()) : ?>	
	<?php while (have_posts()) : the_post(); ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="title">
			<h1><?php the_title(); ?></h1>
			<p class="info"><strong class="date"><?php the_time('F jS, Y') ?></strong> by <?php the_author(); ?></p>
		</div>
		<div class="content">
			<?php the_content(); ?>
		</div>
		<div class="meta">
			<ul>
				<li>Posted in <?php the_category(', ') ?></li>
				<li><?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?></li>
				<?php the_tags('<li>Tags: ', ', ', '</li>'); ?>
			</ul>
		</div>
	</div>	
	<?php comments_template(); ?>
	<?php endwhile; ?>
	
	<div class="navigation">
		<div class="next"><?php previous_post_link('%link &raquo;') ?></div>
		<div class="prev"><?php next_post_link('&laquo; %link') ?></div>
	</div>
	
	<?php else : ?>
	<div class="post">
		<div class="title">
			<h1>Not Found</h1>
		</div>
		<div class="content">
			<p>Sorry, but you are looking for something that isn't here.</p>
		</div>
	</div>
	<?php endif; ?>	
</section>
<?php get_footer(); ?>