<?php $sq = get_search_query() ? get_search_query() : 'Enter search terms&hellip;'; ?>
<!-- search form -->
<form method="get" class="search-form" id="searchform" action="<?php bloginfo('url'); ?>/explore/" >
	<fieldset>
		<div class="holder">
			<label for="search-field">Explore global development aid projects</label>
			<div class="row">
				<input class="text" name="query" id="search-field" type="text" value="<?php echo trim( $_REQUEST['query'] ); ?>" />
				<input class="btn-submit" type="button" value="search" />
			</div>
		</div>
	</fieldset>
</form>