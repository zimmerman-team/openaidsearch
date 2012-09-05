<?php get_header(); ?>
<section id="main">
	<?php if (have_posts()) : ?>
	<div class="post">
		<div class="title">
			<h1>Search Results</h1>
		</div>
	</div>
	<?php while (have_posts()) : the_post(); ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="title">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		</div>
		<div class="content">
			<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
		</div>
	</div>
	<?php endwhile; ?>
	
	<div class="navigation">
		<div class="next"><?php next_posts_link('Older Entries &raquo;') ?></div>
		<div class="prev"><?php previous_posts_link('&laquo; Newer Entries') ?></div>
	</div>
	
	<?php else : ?>
	<div class="post">
		<div class="title">
			<h2>No posts found.</h2>
		</div>
		<div class="content">
			<p> Try a different search?</p>
			<?php get_search_form(); ?>
		</div>
	</div>
	<?php endif; ?>	
</section>
<?php get_footer(); ?>