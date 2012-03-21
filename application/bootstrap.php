<?php defined('SYSPATH') or die('No direct script access.');

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

/**
 * Set the default time zone.
 */
date_default_timezone_set('UTC');

/**
 * Set the default locale.
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

// -- Configuration and initialization -----------------------------------------

/**
 * Set the default language
 */
I18n::lang('en-us');

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
if (isset($_SERVER['KOHANA_ENV']))
{
	Kohana::$environment = constant('Kohana::'.strtoupper($_SERVER['KOHANA_ENV']));
}

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => '',
	'cache_dir' => APPPATH.'/cache',
	'caching' => FALSE,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth'         => MODPATH.'auth',       // Basic authentication
	'orm'          => MODPATH.'orm',        // Object Relationship Mapping
	'riverid'      => MODPATH.'riverid',
	'cache'        => MODPATH.'cache',      // Caching with multiple backends
	'database'     => MODPATH.'database',   // Database access
	'image'        => MODPATH.'image',      // Image manipulation
	'pagination'   => MODPATH.'pagination', // Pagination
	'themes/default' => THEMEPATH.'default', // Themes
	));


/**
 * Activate Enabled Plugins
 */
Swiftriver_Plugins::load();

// Add the current default theme to the list of modules
$theme = ORM::factory('setting')->where('key', '=', 'site_theme')->find();

if ($theme->loaded() AND !empty($theme->value) AND $theme->value != "default")
{
	Kohana::modules(array_merge(
		array('themes/'.$theme->value => THEMEPATH.$theme->value),
		Kohana::modules()
	));
}

// Clean up
unset($active_plugins, $theme);
	

Cookie::$salt = 'cZjO0Lgfv7QrRGiG3XZJZ7fXuPz0vfcL';


/**
 * Swiftriver Password Reset Route
 */	
Route::set('login_reset', 'login/reset/<id>/<token>')
	->defaults(array(
		'controller' => 'login',
		'action'     => 'reset',
	));

/**
 * Swiftriver Account Create Route
 */	
Route::set('login_create', 'login/create/<email>/<token>',
    array(
        'email' => '[^/]++'
    ))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'reset',
	));

/**
 * Swiftriver Change Email Route
 */	
Route::set('login_changeemail', 'login/changeemail/<id>/<email>/<token>',
    array(
        'email' => '[^/]++'
    ))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'changeemail',
	));


/**
 * Swiftriver Login Route
 */	
Route::set('login', 'login(/<action>(/<id>))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'login',
		'action'     => 'index',
	));

/**
 * Swiftriver Droplet Processing Route
 */	
Route::set('process', 'process')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'process',
		'directory'  => 'crawler'
	));	


/**
 * Swiftriver Crawl Route
 */	
Route::set('crawler', 'crawler(/<id>(/<channel>))')
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'crawler'
	));	
	
/**
 * API Route
 */
Route::set('api', 'api(/<controller>)')
	->defaults(array(
		'action'     => 'api'
	));	
	
/**
 * Swiftriver Media Router (JS/CSS)
 */	
Route::set('media_js', 'media/js')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'js',
	));
	
Route::set('media_css', 'media/css')
	->defaults(array(
		'controller' => 'media',
		'action'     => 'css',
	));	 
	
Route::set('media', 'media(/<file>)', array('file' => '.+'))
    ->defaults(array(
    'controller'    => 'media',
    'action'        => 'index',
    'file'          => NULL,
));



/**
 * Swiftriver Welcome Route
 */	
Route::set('welcome', 'welcome(/<action>(/<id>))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));

/**
 * Swiftriver Settings Route
 */	
Route::set('settings', 'settings(/<controller>(/<action>(/<id>)))', array('id' => '\d+'))
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'settings'
	));
	

/**
 * Account Registration Route
 */
Route::set('register', 'register')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'register'
	));	

	
/**
 * Account Route
 */
Route::set('account_rivers', '<account>/rivers')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'rivers'
	));	
	
Route::set('account_river_new', '<account>/river/new')
	->defaults(array(
		'controller' => 'river',
		'action'     => 'new'
	));

Route::set('account_buckets', '<account>/buckets')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'buckets'
	));	

Route::set('account_bucket_new', '<account>/bucket/new')
	->defaults(array(
		'controller' => 'bucket',
		'action'     => 'new'
	));	


Route::set('account_settings', '<account>/settings')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'settings'
	));	

Route::set('account_settings_ajax', '<account>/ajax_settings')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'ajax_settings'
	));	

/**
 * Trends
 */
Route::set('account_trend', '<account>/<context>/<name>/trend/<controller>(/<action>(/<id>))',
	array(
		'context' => '(river|bucket)',
		'id' => '\d+'
	))
	->defaults(array(
		'controller' => 'main',
		'action'     => 'index',
		'directory'  => 'trend'
	));
	

/**
 * Rivers and Buckets
 */
Route::set('account', '<account>(/<controller>/<name>(/<action>(/<id>(/<id2>))))',
	array(
		'controller' => '(user|river|bucket)',
		'id' => '\d+',
		'id2' => '\d+'
	))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'index'
	));





/**
 * Swiftriver Default Route
 */	
Route::set('default', '(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'swiftriver',
		'action'     => 'index',
		'id' => '\d+'
	));
