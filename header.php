<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php wp_title(' | ', true, 'right'); ?><?php bloginfo('name'); ?></title>
	<link media="all" rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/all.css" />
	<link media="all" rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/style.css" />
	<?php wp_enqueue_script("jquery"); ?>
	<?php wp_head(); ?>
	<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/js/jquery.main.js?theme_path=<?php bloginfo('template_url'); ?>&blog_name=<?php bloginfo('name'); ?>&baseurl=<?php echo get_option('home'); ?>"></script>
	<?php if(is_page(20)) {?>
		<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
	<?php } ?>
	<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/css/ie.css" media="screen"/><![endif]-->
	<?php if ( is_singular() ) wp_enqueue_script( 'theme-comment-reply', get_bloginfo('template_url')."/js/comment-reply.js" ); ?>
</head>
<body>
	<div id="wrapper">
		<div class="w1">
			<div class="w2">
				<div class="w3">
					<!-- header -->
					<header id="header">
						<!-- header-holder -->
						<div class="header-holder">
							<!-- logo -->
							<h1 class="logo"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
							<!-- nav-block -->
							<nav class="nav-block">								
								<!-- social-network -->
								<ul class="social-network">
									<li class="facebook"><span class='st_facebook' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span></li>
									<li class="twitter"><span class='st_twitter' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span></li>
									<li class="linkedin"><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink(); ?>&title=<?php the_title(); ?>">linkedin</a></li>
									<li class="email"><span class='st_email' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span></li>
								</ul>
								<!-- add navigation -->
								<?php
								wp_nav_menu( array('container' => false,
									 'theme_location' => 'header_menu',
									 'menu_class' => 'add-nav'
									 ) );
								?>
							</nav>
						</div>
						<!-- main navigation -->
						<nav id="nav">
					<?php

					wp_nav_menu( array('container' => false,
						 'theme_location' => 'primary',
						 'menu_class' => 'navigation',
						 ) );
					?>
						</nav>
					</header>
