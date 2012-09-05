<?php get_header(); ?>

<section id="main">
	<?php if (have_posts()) : ?>
	
	<div class="post">
		<div class="title">
			<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
			<?php /* If this is a category archive */ if (is_category()) { ?>
			<h1>Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h1>
			<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
			<h1>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h1>
			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
			<h1>Archive for <?php the_time('F jS, Y'); ?></h1>
			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<h1>Archive for <?php the_time('F, Y'); ?></h1>
			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<h1>Archive for <?php the_time('Y'); ?></h1>
			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
			<h1>Author Archive</h1>
			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<h1>Blog Archives</h1>
			<?php } ?>
		</div>
	</div>

	<?php while (have_posts()) : the_post(); ?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="title">
			<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo strip_tags(get_the_title()); ?></a></h2>
			<p class="info"><strong class="date"><?php the_time('F jS, Y') ?></strong> by <?php the_author(); ?></p>
		</div>
		<div class="content">
			<?php the_content('Read the rest of this entry &raquo;'); ?>
		</div>
		<div class="meta">
			<ul>
				<li>Posted in <?php the_category(', ') ?></li>
				<li><?php comments_popup_link('No Comments', '1 Comment', '% Comments'); ?></li>
				<?php the_tags('<li>Tags: ', ', ', '</li>'); ?>
			</ul>
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
			<h1>Not Found</h1>
		</div>
		<div class="content">
			<p>Sorry, but you are looking for something that isn't here.</p>
		</div>
	</div>
	<?php endif; ?>
	
</section>

<?php get_footer(); ?>
