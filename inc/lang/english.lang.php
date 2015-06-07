<?php if(!defined('GX_LIB')) die("Direct Access Not Allowed!");
/*
*    GeniXCMS - Content Management System
*    ============================================================
*    Build          : 20140925
*    Version        : 0.0.1 pre
*    Developed By   : Puguh Wijayanto (www.metalgenix.com)
*    License        : MIT License
*    ------------------------------------------------------------
*    filename : english.lang.php
*    version : 0.0.1 pre
*    build : 20140928
*/

/**
* @author Puguh Wijayanto
* @since 0.0.1-pre
*/

/**
* USER Account RELATED Translations
* @since 0.0.1-pre
*/
define('SIGN_IN', 'Sign In');
define('PASS_NOT_MATCH', 'Password Didn\'t Match With Our Records. Please check Your password and try again.');
define('NO_USER', 'Username is incorrect, No such user available. Please check your username.');
define('LOGIN_TITLE', 'Member Login');
define('USERNAME', 'Username');
define('PASSWORD', 'Password');
define('RETYPE_PASSWORD', 'Retype Password');
define('FORGOT_PASS', 'Forgot Password');
define('FILLIN_USERNAME', 'Fill in your Username');
define('FILLIN_PASSWORD', 'Fill in your Password');
define('ACOUNT_NOT_ACTIVE', 'Your Account is not active. Please activate it first. Check your email for the activation link.');
define('ACOUNT_NOT_ACTIVE_BLOCK', 'Your Account is not active. Please contact Support for this problems.');
define('PASSWORD_SENT_NOTIF', 'Your new Password just sent to your email account. Please check your email.');
define('REQUEST_PASS', 'Request Password');
define('TITLE_CANNOT_EMPTY','Title cannot be empty.');
define('CATEGORY_CANNOT_EMPTY','Category cannot be empty.');
define('USERID_CANNOT_EMPTY','Username cannot be empty.');
define('PASS1_CANNOT_EMPTY','Password 1 cannot be empty.');
define('PASS2_CANNOT_EMPTY','Password 2 cannot be empty.');
define('USER', 'User');

/** Token */
define('TOKEN_NOT_EXIST', 'Token not exist, or your time has expired. Please refresh your browser to get a new token.');

/** Themes & Modules Related */
define('THEME_ACTIVATED', 'Themes activated.');
define('THEME_DEACTIVATED', 'Themes deactivated.');
define('THEME_REMOVED', 'Themes removed.');
define('NOFILE_UPLOADED','No Files Uploaded.');
define('MODULES_DELETED','Modules Deleted.');
define('MODULES_DEACTIVATED','Modules deactivated.');
define('MODULES_ACTIVATED','Modules activated.');

/** Menu Related */
define('MENUID_CANNOT_EMPTY','MenuID cannot be empty.');
define('MENUNAME_CANNOT_EMPTY','Menu Name cannot be empty.');
define('MENU', 'Menu');


/** Admin Dashboard Related */
define('DASHBOARD', 'Dashboard');
define('POSTS', 'Posts');
define('CATEGORIES', 'Categories');
define('PAGES', 'Pages');
define('USERS', 'Users');
define('MENUS', 'Menus');
define('THEMES', 'Themes');
define('MODULES', 'Modules');
define('SETTINGS', 'Settings');
define('LOGOUT', 'Log Out');

define('ADD_NEW_POST', 'Add New Post');
define('ADD_NEW_PAGE', 'Add New Page');

/** 
* Dashboard 
*
* @author Puguh Wijayanto
* @since 0.0.1-pre
*
* @author Vakho Daneila
* @since 0.0.3-patch
*/
define('LATEST_POST', 'Latest Post');
define('STATISTIC', 'Statistic');
define('TOTAL_POST', 'Total Post');
define('TOTAL_PAGE', 'Total Page');
define('TOTAL_CAT', 'Total Categories');
define('TOTAL_USER', 'Total Users');

/** Posts & Pages Related */
define('FIND_POSTS', 'Find Posts');
define('FIND_PAGES', 'Find Pages');
define('SEARCH_PAGES', 'Search Pages');
define('SEARCH_POSTS', 'Search Posts');

define('PUBLISHED', 'Published');
define('UNPUBLISHED', 'Unpublished');
define('PUBLISH', 'Publish');
define('UNPUBLISH', 'Unpublish');

define('ID', 'ID');
define('TITLE', 'Title');
define('CATEGORY', 'Category');
define('DATE', 'Date');
define('STATUS', 'Status');
define('ACTION', 'Action');
define('ALL', 'All');

define('OPTIONS', 'Options');
define('CONTENT', 'Content');
define('POST_DATE', 'Post Date');
define('POST', 'Post');
define('PAGE', 'Page');

define('DELETE', 'Delete');
define('EDIT', 'Edit');

define('NO_POST_FOUND', 'No Post Found');
define('NO_PAGE_FOUND', 'No Page Found');

define('SUBMIT', 'Submit');
define('CLOSE', 'Close');
define('CANCEL', 'Cancel');

define('ADD_CATEGORY', 'Add Category');
define('PARENTS', 'Parent');
define('CATEGORY_NAME', 'Category Name');

define('PUBLISHED_LOWER', 'published');
define('UNPUBLISHED_LOWER', 'unpublished');
define('LEFT_IT_BLANK_NOW_DATE', 'left it blank to make it now');

/** User Related */
define('ADD_USER', 'Add User');
define('FIND_USER', 'Find user');
define('SEARCH_USER', 'Search user');
define('ACTIVE', 'Active');
define('INACTIVE', 'Inactive');
define('ACTIVATE', 'activate');
define('DEACTIVATE', 'deactivate');
define('EMAIL', 'Email');
define('GROUP', 'Group');
define('JOIN_DATE', 'Join Date');
define('ADMINISTRATOR', 'Administrator');
define('AUTHOR', 'Author');
define('GENERAL_MEMBER', 'General Member');

define('DELETE_CONFIRM', 'Are you sure you want to delete this?');

/** Menu Related */
define('MENU_NAME_CANNOT_EMPTY', 'Menu Name Cannot be Empty');
define('MENU_TYPE_CANNOT_EMPTY', 'Menu Type Cannot be Empty');

define('ADD_MENU', 'Add Menu');
define('MENU_ID', 'Menu ID');
define('MENU_ID_DESC', 'ID of the menu, eg. <code>mainmenu</code>');
define('MENU_NAME', 'Menu Name');
define('MENU_NAME_DESC', 'Name of the menu');
define('MENU_CLASS', 'Menu CLass');
define('MENU_CLASS_DESC', 'Class Style of the menu. <code>.class</code> means menu class is <em>class</em>');
define('MENU_ITEMS', 'Menu Items');
define('ADD_MENU_ITEM', 'Add Menu Item');


define('MENU_PARENT', 'Parent Menu');
define('MENU_TYPE', 'Menu Type');
define('MENU_CUSTOM_LINK', 'Custom Link');
define('MENU_EDIT', 'Edit Menu');
define('MENU_ADD_ITEM', 'Add Item');

define('MENU_ID_DESCR', 'Your Menu ID, eg. <code>mainmenu</code> ');
define('MENU_NAME_DESCR', 'Your Menu Name');
define('MENU_CLASS_DESCR', 'Class Style of the menu, <code>.class</code> means menu class is <em>class</em>');
define('MENU_PAGE_DESCR', 'Choose This if you want menu for Pages. Pick Page Name');
define('MENU_CATEGORIES_DESCR', 'Choose This if you want menu for Categories. Pick Category Name');
define('MENU_MODULES_DESCR', 'Choose This if you want menu for Modules(Mod). Pick Module Name');
define('MENU_CUSTOM_LINK_DESCR', 'Choose This if you want menu with Custom Link Categories. Insert Custom Link');
define('MENU_PARENT_DESCR', 'Choose Parent Menu');


/** Themes Related */
define('UPLOAD_THEMES', 'Upload Themes');
define('ACTIVE_THEME', 'Active Themes');
define('AVAILABLE_THEME', 'Available Themes');
define('NO_THEMES_FOUND', 'No Themes Found');
define('INSTALL_THEME', 'Install Themes');
define('BROWSE_THEMES', 'Browse Themes');
define('BROWSE_THEME_DESC', 'choose the theme file. in zip compression');

/** Modules Related */
define('UPLOAD_MODULES', 'Upload Modules');
define('ACTIVE_MODULE', 'Active Modules');
define('NO_MODULES_FOUND', 'No Modules Found');
define('INSTALL_MODULE', 'Install Modules');
define('BROWSE_MODULES', 'Browse Modules');
define('BROWSE_MODULES_DESC', 'choose the module file. in zip compression');

define('NAME', 'Name');
define('DESC', 'Description');

define('VERSION', 'Version');
define('LICENSE', 'License');

/** Settings Related */
define('CHANGE', 'Change');
define('GENERAL', 'General');
define('LOCALIZATION', 'Localization');
define('SOCIAL', 'Social');
define('LOGO', 'Logo');
define('LIBRARY', 'Library');
define('PAYMENT', 'Payment');

define('WEBSITE_DETAIL', 'Website Detail');
define('WEBSITE_NAME', 'Website Name');
define('WEBSITE_NAME_DESC', 'your website name');
define('WEBSITE_SLOGAN', 'Website Slogan');
define('WEBSITE_SLOGAN_DESC', 'your website slogan');
define('WEBSITE_DOMAIN', 'Website Domain');
define('WEBSITE_DOMAIN_DESC', 'Your Domain, eg: example.org');
define('WEBSITE_URI', 'Website URL');
define('WEBSITE_URI_DESC', 'Your Website URL, eg: http://www.example.org');
define('WEBSITE_KEYWORDS', 'Website Keywords');
define('WEBSITE_KEYWORDS_DESC', 'Your Website Keywords, type your website main keywords.');
define('WEBSITE_DESCRIPTION', 'Website Description');
define('WEBSITE_DESCRIPTION_DESC', 'Your Website Description, describe your website.');
define('WEBSITE_EMAIL', 'Website E-mail');
define('WEBSITE_EMAIL_DESCR', 'Website E-mail');

define('COUNTRY', 'Country');
define('COUNTRY_DESC', 'Your Website Country.');
define('TIMEZONE', 'Timezone');
define('TIMEZONE_DESC', 'Your Website Timezone.');
define('WEBSITE_LANG', 'Website Language');
define('WEBSITE_LANG_DESC', 'Your Website System Language.');
define('CHARSET', 'Default Charset');
define('CHARSET_DESC', 'Your Website Charset/Encoding.');



/** 
* @author Vakho Daneila
* @since 0.0.3-patch
*/
// Settings E-Mail

define('SETTINGS_EMAIL_SETTINGS', 'Email Settings');
define('SETTINGS_EMAIL_MAIL', 'Mail/SMTP');
define('SETTINGS_EMAIL_SMTP', 'SMTP Port');
define('SETTINGS_EMAIL_MAILSRV', 'SMTP MailServer');
define('SETTINGS_EMAIL_SMTP_USR', 'SMTP Username');
define('SETTINGS_EMAIL_SMTP_PWD', 'SMTP Password');

define('SETTINGS_EMAIL_MAIL_DESCR', 'Choose using Mail or SMTP');
define('SETTINGS_EMAIL_SMTP_DESCR', 'Fill in with the SMTP Port Number');
define('SETTINGS_EMAIL_MAILSRV_DESCR', 'Your mailserver, eg: mail.example.org. This will used when using SMTP');
define('SETTINGS_EMAIL_SMTP_USR_DESCR', 'our SMTP Username, eg: user@example.org. This will used when using SMTP');
define('SETTINGS_EMAIL_SMTP_PWD_DESCR', 'Your SMTP Password. This will used when using SMTP');

// Settings Social

define('SETTINGS_SOCIAL', 'Social Networking');
define('SETTINGS_SOCIAL_FBACC', 'Facebook Account');
define('SETTINGS_SOCIAL_FBPAGE', 'Facebook Page');
define('SETTINGS_SOCIAL_TWITTER', 'Twitter Account');
define('SETTINGS_SOCIAL_LINKEDIN', 'LinkedIn Account');

define('SETTINGS_SOCIAL_FBACC_DESCR', 'Your Facebook Account');
define('SETTINGS_SOCIAL_FBPAGE_DESCR', 'Your Facebook Page');
define('SETTINGS_SOCIAL_TWITTER_DESCR', 'Your Twitter Account');
define('SETTINGS_SOCIAL_LINKEDIN_DESCR', 'Your LinkedIn Account');

// Settings Logo

define('SETTINGS_LOGO', 'Website Logo');
define('SETTINGS_LOGO_CURRENT', 'Current Logo');
define('SETTINGS_LOGO_BROWSE', 'Browse Image Logo');
define('SETTINGS_LOGO_URL', 'Use Image URL');
define('SETTINGS_LOGO_FAVICON', 'Website Favicon');

define('SETTINGS_LOGO_CURRENT_DESCR', 'Your Website Logo');
define('SETTINGS_LOGO_BROWSE_DESCR', 'Browse images if You want to upload your logo.');
define('SETTINGS_LOGO_URL_DESCR', 'Your Website Logo URL');
define('SETTINGS_LOGO_FAVICON_DESCR', 'Your Website Favicon URL');

// Settings Library

define('SETTINGS_LIBRARY', 'Enable or Disable Library');
define('SETTINGS_LIBRARY_JQUERY', 'Enable JQuery');
define('SETTINGS_LIBRARY_BOOTSTRAP', 'Enable Bootstrap');
define('SETTINGS_LIBRARY_FAWESOME', 'Enable Fontawesome');
define('SETTINGS_LIBRARY_EDITOR', 'Enable Editor');
define('SETTINGS_LIBRARY_SUMMERNOTE', 'Summer Note');
define('SETTINGS_LIBRARY_BVALIDATOR', 'Enable Bootstrap Validator');
define('SETTINGS_LIBRARY_CDN', 'CDN');
define('SETTINGS_LIBRARY_LOCAL', 'LOCAL');

define('SETTINGS_LIBRARY_JQUERY_DESCR', 'Check this if you want to use Jquery. Fill the version of Jquery. Default version is 1.11.11');
define('SETTINGS_LIBRARY_BOOTSTRAP_DESCR', 'Check this if you want to use Bootstrap. Bootstrap Version is not available, left it blank');
define('SETTINGS_LIBRARY_FAWESOME_DESCR', 'Check this if you want to use Fontawesome. Fontawesome Version is not available, left it blank');
define('SETTINGS_LIBRARY_EDITOR_DESCR', 'Check this if you want to use Editor. Editor Version is not available, left it blank');
define('SETTINGS_LIBRARY_BVALIDATOR_DESCR', 'Check this if you want to use Bootstrap Validator. Bootstrap Validator Version is not available, left it blank');

// Settings Posts

define('SETTINGS_POSTS', 'Posts Config');
define('SETTINGS_POSTS_PERPAGE', 'Post per Page');
define('SETTINGS_POSTS_PAGINATION', 'Pagination Type');
define('SETTINGS_POSTS_PAGINATION_NUMBER', 'Number');
define('SETTINGS_POSTS_PAGINATION_PAGER', 'Pager');
define('SETTINGS_POSTS_PINGER', 'Pinger');
define('SETTINGS_POSTS_PINGER_HTTP', 'http://');

define('SETTINGS_POSTS_PERPAGE_DESCR', 'Number of Posts to show per page. ');
define('SETTINGS_POSTS_PAGINATION_DESCR', 'Default Type of Pagination. Number :');
define('SETTINGS_POSTS_PINGER_DESCR', 'Set the Pinger of Search Engine. Use {{domain}} for your domain');

// Settings Payment

define('SETTINGS_PAYMENT', 'Payment');
define('SETTINGS_PAYMENT_PAYPAL_CONF', 'PayPal Configuration');
define('SETTINGS_PAYMENT_PAYPAL_CSYMB', 'Currency Symbol');
define('SETTINGS_PAYMENT_SENDBOX', 'Enable Sandbox');
define('SETTINGS_PAYMENT_SENDBOX_EN', 'Enable Sandbox?');
define('SETTINGS_PAYMENT_PAYPALAPI_USR', 'PayPal API Username');
define('SETTINGS_PAYMENT_PAYPALAPI_PWD', 'PayPal API Password');
define('SETTINGS_PAYMENT_PAYPALAPI_SIGN', 'PayPal Signature1');
define('SETTINGS_PAYMENT_ALERT', 'Attention, please fill these API Credentials from Your PayPal Account website. See the documentations at  
                          <a href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/" target="_blank">
                              https://developer.paypal.com');

define('SETTINGS_PAYMENT_PAYPAL_CSYMB_DESCR', 'Pick a Currency, default is USD');
define('SETTINGS_PAYMENT_SENDBOX_EN_DESCR', 'Enable Sandbox');
define('SETTINGS_PAYMENT_PAYPALAPI_USR_DESCR', 'Your PayPal API Username');
define('SETTINGS_PAYMENT_PAYPALAPI_PWD_DESCR', 'Your API Password');
define('SETTINGS_PAYMENT_PAYPALAPI_SIGN_DESCR', 'Your PayPal Signature');

// Registration

define('REG_FORM', 'Register');
define('REG_ALREADY_HAVE_ACC', 'Already Have an Account ? Login Now!');
define('REG_ALREADY_REGISTERED_ACC', 'You are Already Registered and Logged In!');
define('REG_USERNAME_REQUIRED', 'The Username is required and cannot be empty');
define('REG_ACTIVATE_ACCOUNT', 'Thank You for Registering with Us. Please Activate Your Account to login');
define('REG_CANT_CREATE_ACCOUNT', 'We can not create your account');
define('REG_ACTIVATE_ACCOUNT_MAIL', 'Thank You for Registering with Us. Please activate your account by clicking this link :');  // Can not make this to work (activating via mail)
define('REG_ACCOUNT_ACTIVATED', 'Your Account activated successfully. You can now Login with your Username and Password.');
define('REG_ACTIVATION_FAILED', 'Activation Failed.');
define('REG_ACTIVATION_FAILED_CODE', 'Activation Failed. No such code, or maybe already activated.');

// Control MSG's

define('MSG_CATEGORY_ADDED', 'Category Added');
define('MSG_CATEGORY_REMOVED', 'Category Removed');
define('MSG_CATEGORY_UPDATED', 'Category Updated');
define('MSG_CATEGORY_DELETE', 'Are you sure you want to delete this item?');

define('MSG_POST_ADDED', 'Added Successfully');
define('MSG_POST_UPDATED', 'Updated Successfully');
define('MSG_POST_REMOVED', 'Removed Successfully');      // Needs Some Space :) Please Check It (PostPostname Removed Successfully)
define('MSG_POST_DELETE', 'Are you sure you want to delete this item?');

define('MSG_PAGE_ADDED', 'Added Successfully');
define('MSG_PAGE_UPDATED', 'Updated Successfully');
define('MSG_PAGE_REMOVED', 'Removed Successfully');        // Needs Some Space :) Please Check It
define('MSG_PAGE_DELETE', 'Are you sure you want to delete this item?');

define('MSG_USER_ADDED', 'Added Successfully');  
define('MSG_USER_UPDATED', 'Updated Successfully');  
define('MSG_USER_REMOVED', 'Removed Successfully');  
define('MSG_USER_EXIST', 'User Exist! Choose Another Username'); 
define('MSG_USER_EMAIL_EXIST', 'Email Already Used. Please Use Another E-Mail:'); 
define('MSG_USER_ACTIVATED', 'Activated Successfully.'); 
define('MSG_USER_DEACTIVATED', 'Deactivated Successfully.'); 
define('MSG_USER_ACTIVATION_FAIL', 'Activation fail.');
define('MSG_USER_DEACTIVATION_FAIL', 'Deactivation fail.');
define('MSG_USER_PWD_MISMATCH', 'Password Did Not Match, Retype Your Password Again');
define('MSG_USER_NO_ID_SELECTED', 'No ID Selected');
define('MSG_USER_DELETE', 'Are you sure you want to delete this item?');

define('MSG_USER_LOGGED_IN', 'You Are Logged In Now');
define('MSG_USER_ALREADY_LOGGED', 'You Are Already Logged In');
define('MSG_ACESS_NOT_ALLOWED', 'Not Allowed1 !!');
define('MSG_NO_ACCESS', 'You do not have Access to this page. Maybe You want to go to ');

define('MSG_THEME_INSTALLED', 'Theme Installed Successfully.');
define('MSG_THEME_IS_ACTIVE', 'Theme is Active. Please deactivate first.');
define('MSG_THEME_NOT_REMOVED', 'Theme Cannot removed. Please check if You had permission to remove the files.');
define('MSG_THEME_CANT_EXTRACT', 'Cannot extract files.');
define('MSG_MOD_INSTALLED', 'Module Installed Successfully.');
define('MSG_MOD_CANT_EXTRACT', 'Cannot extract files.');
define('MSG_SETTINGS_SAVED', 'New Settings Saved Successfully.');

// Errors

define('MSG_DATABASE_ERROR', 'Database Error!');
define('MSG_DATABASE_ERROR_DESCR', 'Something went wrong with the Database.');
define('MSG_UNKNOWN_ERROR', 'Unknown Error Occurred!');

// Other Functions

define('REMOVE', 'remove');
