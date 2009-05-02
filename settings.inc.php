<?php
// Syntax: settingname = value
// // for comments
// whitespaces before and after key or value (line or =) will be ignored
// 1 for true, 0 for false

// Debug? - This should be turned off for production sites.
$debug = false;

// url to the main directory
$url	= '.';
$theme	= 'default';
$defaultLanguage = 'en';

// interface may be: ice, (dbus may be added at a later time, or probably not)
$dbInterface_type		= 'ice';
$dbInterface_address	= 'Meta:tcp -h 127.0.0.1 -p 6502';

// db type for Interface functionality
// (does not have anything to do with mumble/murmur)
// specify one of the following: filesystem
// later, the following will be implemented: (TODO: add mysql, psql, sqlite)
$dbType		= 'filesystem';

// not necessary for dbType filesystem, but for mysql, psql etc:
$db_username    = '';
$db_password    = '';
$db_database    = '';
$db_tableprefix = '';

$site_title = 'Mumble Interface';
$site_description='Mumble Interface to register on a mumble server and edit your account as well as upload a user-texture for the overlay.';
$site_keywords='mumble,murmur,web-interface,registration,account,management,voip';

// For Each Server set:
//   server_<serverid>_name              = 
//   server_<serverid>_allowlogin        = 
//   server_<serverid>_allowregistration = 
//   server_<serverid>_forcemail         = 
//   server_<serverid>_authbymail        = 
// forceemail: force to enter a mail address. This is always true if authbymail is true.
// authbymail: account has to be activated with a code sent to the mail address
// The default virtual server has the id 1
// Neither allowing login nor registration will hide it from the interface. You can then only see it from the admin section.
$servers = array();

$servers[1]['name']              = 'my custom server';
$servers[1]['allowlogin']        = false;
$servers[1]['allowregistration'] = false;
$servers[1]['forcemail']         = true;
$servers[1]['authbymail']        = true;

?>