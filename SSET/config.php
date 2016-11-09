<?php
// ===========================
// DEFINE SYSTEM WIDE SETTINGS
// ===========================

// Version
define("version", 2.02);                                                   // Version number

// Domain
define("domainProtocol", "https://");
define("domain", domainProtocol.$_SERVER['HTTP_HOST']);                   // Web server base domain

// Paths & filenames
define('tracker_filename','secure');                                      // Tracker filename
define('tracker_variable','secure_id');                                   // Tracker querystring var key
define('base_url','https://localhost/');                           // Tracker base URL

// Mode & reporting
define('runmode','live');                                                 // Runmode, can be 'live' or 'live', uses different DB creds
define('errorreporting',"log");                                           // Error reporting, can be 'screen', 'log' or 'all', only happens in 'live' runmode

define('redirect_on_error','http://www.google.ca');                       // URL to redirect to on errors
define('redirect_on_success','http://www.google.com');                    // URL to redirect to on success


//You will need this to sign up the initial admin,  use something like the below to generate a random string
// head /dev/urandom | tr -dc A-Za-z0-9 | head -c 18
define('admin_token', "THIS_SHOULD_BE_CHANGED");

define("csrf_site_salt", "P1z#UU6syG2UPVf4");

// Set our default timezone and supress warning with @
@date_default_timezone_set(date_default_timezone_get());
?>
