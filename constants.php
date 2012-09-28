<?php
// Tag Cloud
define('TAG_COUNT', 11);
define('TAG_smallest', 17);
define('TAG_largest', 52);
define('TAG_number', 10);
define('TAG_color_1', 18);
define('TAG_color_2', 40);

define( 'SEARCH_URL', 'http://oipa.openaidsearch.org/api/v2/');
define( 'EMPTY_LABEL', 'No information available');
// Categories
define('PROJECT', 'project');

$_DEFAULT_ORGANISATION_ID = '';
$_PER_PAGE = 20;
$_RELAOD_FILTERS_TIMEOUT = 24*60*60; //24 hours

static $_REGION_CHOICES = array();
static $_SECTOR_CHOICES = array();
static $_COUNTRY_ISO_MAP = array();
static $_COUNTRY_BUDGETS = array();

$_BUDGET_CHOICES = array(
'0' => '> 0',
'10000' => '10.000',
'50000' => '50.000',
'100000' => '100.000',
'500000' => '500.000',
'1000000' => '1.000.000',
'5000000' => '5.000.000',
'10000000' => '10.000.000',
);


?>