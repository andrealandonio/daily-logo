<?php
/***************************************************
 DEFINITION AND CONSTANTS
 ***************************************************/

define( 'DLP_VERSION', '2.1.5' );
define( 'DLP_DB_VERSION', '2.0' );
define( 'DLP_PREFIX', 'daily_logo' );
define( 'DLP_DB_TABLE', 'daily_logo' );
define( 'DLP_OPTION_DATA', 'daily_logo_data' );
define( 'DLP_OPTION_SETTINGS', 'daily_logo_settings' );
define( 'DLP_NONCE', 'daily_logo_nonce' );
define( 'DLP_MENU', 'daily_logo_menu' );
define( 'DLP_MENU_SETTINGS', 'daily_logo_menu_settings' );
define( 'DLP_TEMPLATE_DEFAULT', '<a href="##LINK##" title="##NAME##" target="##TARGET##" class="##CLASS##">[?]##HAS_IMAGE##[?]<img src="##IMAGE##" alt="##NAME##" />[:]##NAME##[;]</a>' );
define( 'DLP_ALTERNATIVE_TEMPLATE_DEFAULT', '<a href="##LINK##" title="##NAME##" target="##TARGET##" class="##CLASS##">[?]##HAS_IMAGE##[?]<img src="##IMAGE##" alt="##NAME##" />[:]##NAME##[;]</a>' );
define( 'DLP_STANDARD_TEMPLATE_DEFAULT', '<h1 class="site-title"><a href="/" rel="home">SITE NAME</a></h1>' );
define( 'DLP_STANDARD_ALTERNATIVE_TEMPLATE_DEFAULT', '<h1 class="site-title"><a href="/" rel="home">SITE NAME</a></h1>' );
define( 'DLP_PAGINATION', 20 );

// Defaults
define( 'DLP_DEFAULT_START_DATE_HOUR', 0 );
define( 'DLP_DEFAULT_START_DATE_MINUTE', 0 );
define( 'DLP_DEFAULT_END_DATE_HOUR', 23 );
define( 'DLP_DEFAULT_END_DATE_MINUTE', 59 );
