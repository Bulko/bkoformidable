<?php
/**
 * Bkoformidabe
 *
 * Initialisation param
 * Set up data base & admin menu
 */

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @return String
 */
function bkoforminable_get_table_name()
{
	global $wpdb;
	return $wpdb->prefix . _BKOFORMIDABLE_TABLE_;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-11 )
 *
 * @return Bool
 */
function bkoforminable_install()
{
	global $wpdb;
	global $bkoforminable_db_version;
	$bkoforminable_db_version = '1.3';
	$table_name = bkoforminable_get_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		template_name tinytext,
		url varchar(55) DEFAULT '',
		content text,
		comment text,
		created_at datetime,
		updated_at datetime,
		deleted_at datetime,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	add_option( 'bkoforminable_db_version', $bkoforminable_db_version );
	return true;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-11 )
 *
 * @return Bool
 */
function bkoforminable_uninstall()
{
	global $wpdb;
	$table_name = bkoforminable_get_table_name();
	$sql = "DROP TABLE $table_name;";
	$wpdb->query( $sql );
	delete_option( 'bkoforminable_db_version' );
	return true;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-11 )
 *
 * @return Bool
 */
function bkoforminable_menu() {
	add_menu_page(
		__( 'Contact', 'bkoforminable' ),
		'Leads',
		'bkoforminable',
		'bkoforminableadmin.php',
		'bkoforminable_admin',
		'dashicons-format-status',
		20
	);
	return true;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @return Bool
 */
function bkoforminable_set_cap()
{
	global $wp_roles;
	// administrator
	$wp_roles->add_cap('administrator','bkoforminable');
	$wp_roles->add_cap('administrator','bkoforminable_export');
	$wp_roles->add_cap('administrator','bkoforminable_edit');
	$wp_roles->add_cap('administrator','bkoforminable_root');
	// editor
	$wp_roles->add_cap('editor','bkoforminable');
	$wp_roles->add_cap('editor','bkoforminable_export');
	$wp_roles->add_cap('editor','bkoforminable_edit');
	$wp_roles->add_cap('editor','bkoforminable_root');
	// author
	$wp_roles->add_cap('author','bkoforminable');
	$wp_roles->add_cap('author','bkoforminable_export');
	$wp_roles->add_cap('author','bkoforminable_edit');
	// contributor
	$wp_roles->add_cap('contributor','bkoforminable');
	$wp_roles->add_cap('contributor','bkoforminable_edit');
	return true;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @param Mixed $hook
 * @return Void or Null
 */
function bkoformidable_enqueue( $hook )
{
	if ('toplevel_page_bkoforminableadmin' !== $hook) {
		return;
	}
	wp_deregister_script('jquery');
	wp_enqueue_style('datatablescss', 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css');
	wp_enqueue_style('bkoformidablecss', plugin_dir_url( _BKOFORMIDABLE_FILE_ ) . 'asset/admin.css');
	wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.3.1.js' );
	wp_enqueue_script('datatablesjs', 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', ["jquery"]);
	wp_enqueue_script('bkoforminablejs', plugin_dir_url( _BKOFORMIDABLE_FILE_ ) . 'asset/admin.js', ["jquery"]);
}

register_activation_hook( _BKOFORMIDABLE_FILE_, 'bkoforminable_install' );
register_deactivation_hook( _BKOFORMIDABLE_FILE_, 'bkoforminable_uninstall' );

add_action( 'after_setup_theme', 'bkoforminable_set_cap' );
add_action( 'admin_menu', 'bkoforminable_menu' );
add_action( 'admin_enqueue_scripts', 'bkoformidable_enqueue' );
