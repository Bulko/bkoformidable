<?php
/**
 * Bkoformidabe
 *
 * short code generation
 * Param + Tpl + asset link (css & js)
 */


/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-11 )
 *
 * @param String $name
 * @return String
 */
function getTpl( $name )
{
	$path = plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . "tpl/" . $name . ".html";
	if ( file_exists( $path ) )
	{
		return file_get_contents( $path );
	}
	return "<hr />{Error form " . $name . " not found}<hr />";
}

/**
 * [bartag formName="foo-value"]
 *
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-11 )
 *
 * @param String $atts
 * @return String
 */
function bkoFormidableTag( $atts )
{
	wp_enqueue_script('bkoforminablejs', plugin_dir_url( _BKOFORMIDABLE_FILE_ ) . 'asset/front.js', ["jquery"]);
	wp_localize_script( 'bkoforminablejs', 'bkoFormidable',
		[
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'root' => site_url(),
			'home' => is_front_page()
		]
	);
	$a = shortcode_atts(
		[
			'formName' => 'contact',
			'bar' => 'something else',
		],
	$atts );

	$data = getTpl( $a['formName'] );
	return $data;
}
add_shortcode( _BKOFORMIDABLE_TAG_NAME_, 'bkoFormidableTag' );
