<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

defined('DEFAULT_DISTANCE')    OR define('DEFAULT_DISTANCE', 10);
defined('MAX_DISTANCE')        OR define('MAX_DISTANCE', 255);


$http = (isset($_SERVER['HTTPS']) ? 'https' : 'http');
/* Globals */
defined('ASSETS_PATH')              OR define('ASSETS_PATH', $http.'://'.$_SERVER['HTTP_HOST'].((ENVIRONMENT !== 'production') ? '/eapp/assets/' : '/assets/')); // The assets folder of the application
defined('ASSETS_DIR_PATH')          OR define('ASSETS_DIR_PATH', $_SERVER['DOCUMENT_ROOT'].((ENVIRONMENT !== 'production') ? '/eapp/assets/' : '/assets/')); // The assets folder of the application
defined('STORE_LOGO_WIDTH')         OR define('STORE_LOGO_WIDTH', 1024); // highest automatically-assigned error code
defined('STORE_LOGO_HEIGHT')        OR define('STORE_LOGO_HEIGHT', 720); // highest automatically-assigned error code

defined('POST_TYPE_STAT')           OR define('POST_TYPE_STAT', 1);
defined('COMPANY_SUBSCRIPTION')     OR define('COMPANY_SUBSCRIPTION', 10);
defined('STAT_TYPE_CLICK')          OR define('STAT_TYPE_CLICK', 0);
defined('STAT_TYPE_ADD_TO_CART')    OR define('STAT_TYPE_ADD_TO_CART', 1);
defined('STAT_TYPE_ADD_TO_LIST')    OR define('STAT_TYPE_ADD_TO_LIST', 2);
defined('STAT_TYPE_SEARCH')         OR define('STAT_TYPE_SEARCH', 3);

defined('CONNECTION_TYPE')          OR define('CONNECTION_TYPE', $http);
defined('OtIPRIX_ADDRESS')          OR define('OtIPRIX_ADDRESS', '550 Avenue Saint-Dominique, Saint-Hyacinthe, J2S 5M6');



/* Database Tables */
defined('CHAIN_STORE_TABLE')            OR define('CHAIN_STORE_TABLE', 'eapp_chain_store'); 
defined('PRODUCT_TABLE')                OR define('PRODUCT_TABLE', 'eapp_product'); 
defined('STORE_PRODUCT_TABLE')          OR define('STORE_PRODUCT_TABLE', 'eapp_store_product');
defined('CATEGORY_TABLE')               OR define('CATEGORY_TABLE', 'eapp_product_category'); 
defined('SUB_CATEGORY_TABLE')           OR define('SUB_CATEGORY_TABLE', 'eapp_product_subcategory');
defined('CHAIN_TABLE')                  OR define('CHAIN_TABLE', 'eapp_chain');
defined('UNITS_TABLE')                  OR define('UNITS_TABLE', 'eapp_units');
defined('BRANDS_TABLE')                 OR define('BRANDS_TABLE', 'eapp_brands');
defined('COMPAREUNITS_TABLE')           OR define('COMPAREUNITS_TABLE', 'eapp_compareunits');
defined('USER_CHAIN_STORE_TABLE')       OR define('USER_CHAIN_STORE_TABLE', 'eapp_user_chain_store');
defined('USER_ACCOUNT_TABLE')           OR define('USER_ACCOUNT_TABLE', 'eapp_user_account');
defined('USER_PROFILE_TABLE')           OR define('USER_PROFILE_TABLE', 'eapp_user_profile');
defined('USER_FAVORITE_STORE_TABLE')    OR define('USER_FAVORITE_STORE_TABLE', 'eapp_user_favorite_store');
defined('USER_GROCERY_LIST_TABLE')      OR define('USER_GROCERY_LIST_TABLE', 'eapp_user_grocery_list');
defined('PRODUCT_BRAND_TABLE')          OR define('PRODUCT_BRAND_TABLE', 'eapp_product_brand');
defined('BLOG_POSTS')                   OR define('BLOG_POSTS', 'eapp_blog_posts');
defined('BLOG_POSTS_COMMENTS')          OR define('BLOG_POSTS_COMMENTS', 'eapp_blog_posts_comments');
defined('BLOG_POSTS_LIKES')             OR define('BLOG_POSTS_LIKES', 'eapp_blog_posts_likes');
defined('USER_OPTIMIZATION_TABLE')      OR define('USER_OPTIMIZATION_TABLE', 'eapp_user_optimization');
defined('CONTACTS_TABLE')               OR define('CONTACTS_TABLE', 'eapp_contacts');
defined('SECURITY_QUESTIONS')           OR define('SECURITY_QUESTIONS', 'eapp_security_question');
defined('UNIT_CONVERSION')              OR define('UNIT_CONVERSION', 'otiprix_unit_compareunit');
defined('PRODUCT_UNIT_CONVERSION')      OR define('PRODUCT_UNIT_CONVERSION', 'otiprix_product_unit_compareunit');
defined('PRODUCT_STATS')                OR define('PRODUCT_STATS', 'otiprix_product_statistics');
defined('PRODUCT_OPTIMIZATION_STATS')   OR define('PRODUCT_OPTIMIZATION_STATS', 'otiprix_product_optimization_statstics');
defined('CHAIN_STATS')                  OR define('CHAIN_STATS', 'otiprix_chain_statistics');
defined('CHAIN_VISITS')                 OR define('CHAIN_VISITS', 'otiprix_chain_visits');
defined('NEWSLETTER_SUBSCRIPTIONS')     OR define('NEWSLETTER_SUBSCRIPTIONS', 'otiprix_newsletter_subscriptions');
defined('COMPANY_TABLE')                OR define('COMPANY_TABLE', 'eapp_company');
defined('COMPANY_SUBSCRIPTIONS_TABLE')  OR define('COMPANY_SUBSCRIPTIONS_TABLE', 'eapp_company_subscriptions');











