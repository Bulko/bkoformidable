<?php
/*
Plugin Name: Renovart devis & contacts
Depends:
Provides: Bkoformidabe
Plugin URI:
Description: Options de formulaire avancé pour le sire Renovart.
Version: 1.0.0
Author: Bulko
Author URI: http://www.bulko.net/
License: http://www.wtfpl.net/
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) )
{
	wp_die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
define( '_BKOFORMIDABLE_TAG_NAME_', 'renovartform' );
define( '_BKOFORMIDABLE_TABLE_', 'bkoformidable' );
define( '_BKOFORMIDABLE_FILE_', __FILE__ );
define( '_BKOFORMIDABLE_TITLE_', "Renovart devis & contacts" );
define( '_BKOFORMIDABLE_MAIL_FROM_', 'Contact Renovart <noreply@renovart-ouvertures.fr>' );
define( '_BKOFORMIDABLE_MAIL_SUBJECT_', "Renovart devis & contacts" );
const _BKOFORMIDABLE_MAILTO_ = ['r-ro@bulko.net'];
// const _BKOFORMIDABLE_MAILTO_ = ['c.cevaer@c2r-sa.com', 's.broudiscou@c2r-sa.com'];

//include required wp mod
require_once( ABSPATH . '/wp-config.php' );

// INIT
require_once(  plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . 'init/ini.php' );
require_once(  plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . 'init/shortCode.php' );

require_once(  plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . 'admin/process.php' );
require_once(  plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . 'admin/admin.php' );

