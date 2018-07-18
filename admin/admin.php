<?php

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @return String
 */
function bkoforminable_adminHeader()
{
	$directories = glob( plugin_dir_path( _BKOFORMIDABLE_FILE_ ) . 'tpl/*' );
	$templates = [];
	$npTemplates = count( $directories );
	echo '<div class="wrap bkoforminable">';
	echo "<h1>" . _BKOFORMIDABLE_TITLE_ . "</h1>";
	print_r( $_POST );
	if ( $npTemplates > 1 )
	{
		echo "<div class='wp-filter'>";
	}

	for ( $i = count( $directories ) -1; $i > -1; $i -- )
	{
		$expld =
			ucfirst(
				substr(
					end(
						explode( "/", $directories[ $i ] )
					)
					, 0, -5
				)
			);
		array_push( $templates, $expld );
		if ( $npTemplates > 1 && $_GET["tpl"] === $expld )
		{
			echo "<a class='button active elect-mode-toggle-button' title='" . $expld . "' href='?page=bkoforminableadmin.php&tpl=" . $expld . " ' >" . $expld . "</a>";
		}
		elseif ( $npTemplates > 1 )
		{
			echo "<a class='button elect-mode-toggle-button' title='" . $expld . "' href='?page=bkoforminableadmin.php&tpl=" . $expld . " ' >" . $expld . "</a>";
		}
	}
	if ( $npTemplates > 1 )
	{
		echo "</div>";
	}
	$activeTpl = $templates[0];
	if ( !empty( $_GET["tpl"] ) && in_array( $_GET["tpl"], $templates ) )
	{
		$activeTpl = $_GET["tpl"];
	}
	return $activeTpl;
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @param String $tplName
 * @return Array
 */
function bkoforminable_getData( $tplName )
{
	global $wpdb;
	$sql = "SELECT * FROM " .
	bkoforminable_get_table_name() .
	" WHERE `template_name` = '" . $tplName . "'" .
	" AND `deleted_at` IS NULL" .
	" ORDER BY `id` DESC" .
	";";
	return $wpdb->get_results( $sql );
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-17 )
 *
 * @param Mixed $data
 * @return Array
 */
function bkoforminable_decodes( $data )
{
	return (array) json_decode(
		trim(
			preg_replace(
				'/\s\s+|\_/',
				' ',
				nl2br( $data )
			)
		)
	);
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @param array $data
 * @return Boolean
 */
function bkoforminable_displayTable( $data = [] )
{
	if ( empty( $data ) || empty( $data[0] ) )
	{
		return false;
	}
	$size = sizeOf( $data ) - 1;
	$key = array_keys( (array) $data[ 0 ] );
	$headerContent = bkoforminable_decodes( $data[ 0 ]->content );
	$jsonKey = array_keys( $headerContent );
	echo "<div class='dataTableContainer'>";
	echo "<a href='' class='downloadCsv button'>Télécharger un CSV</a>";
	echo " <a href='' class='reloadCsv'>Régénérer le CSV (" . date_i18n( get_option( 'time_format' ), time() ) . ")</a>";
	echo "<table class='datatables'>";
	echo "<thead>";
	echo "<tr>";
	echo "<th class='haveDropDown'> id </th>";
	echo "<th> date </th>";
	for ( $i = 0; $i < sizeOf( $jsonKey ) ; $i++)
	{
		echo "<th>" . $jsonKey[$i] . "</th>";
	}
	echo "<th> Commentaire </th>";
	echo "</tr>";
	echo "</thead>";
	echo "<tbody>";
	for ( $i = $size; $i > -1 ; $i-- )
	{
		echo "<tr>";
		echo "<td>" . $data[$i]->id . "</td>";
		echo "<td>" .  date_i18n( get_option( 'date_format' ), strtotime( $data[$i]->created_at ) ) . "</td>";
		$contentData = bkoforminable_decodes( $data[$i]->content );
		for ( $i2 = 0; $i2 < sizeOf( $jsonKey ) ; $i2++ )
		{
			echo "<td>" . $contentData[ $jsonKey[$i2] ] . "</td>";
		}
		echo "<td>" .
				"<div class='placeholder'>" .
					"<pre class='comment'>" .
						$data[$i]->comment .
					"</pre>" .
					"<span class='toggle-form'>✎</span>" .
				"</div>" .
				"<form class='form' method='post'>" .
					"<input type='hidden' name='id' value='" . $data[$i]->id . "' />" .
					"<textarea name='comment' class='comment-field'>" . $data[$i]->comment . "</textarea>" .
					"<input type='submit' class='submit' value='💾' />" .
				"</form>" .
			"</td>";
		echo "</tr>";
	}
	echo "</tbody>";
	echo "<tfoot>";
	echo "<tr>";
	echo "<th class='haveDropDown'> id </th>";
	echo "<th> date </th>";
	for ( $i = 0; $i < sizeOf( $jsonKey ) ; $i++)
	{
		echo "<th class='haveDropDown'>" . $jsonKey[$i] . "</th>";
	}
	echo "<th> Commentaire </th>";
	echo "</tr>";
	echo "</tfoot>";
	echo "</table>";
	echo "</div>";
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @return [type]
 */
function bkoforminable_displayFooter()
{
	echo "<hr />";
	echo "</div>";
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-13 )
 *
 * @return Void
 */
function bkoforminable_admin()
{
	if ( !current_user_can( 'bkoforminable' ) )
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$activeTpl = bkoforminable_adminHeader();
	$data = bkoforminable_getData( $activeTpl );
	bkoforminable_displayTable( $data );
	bkoforminable_displayFooter();
}
