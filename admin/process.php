<?php
/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-16 )
 *
 * @return Bool
 */
function bkoFormidableSubmitForm()
{
	$data = $_GET;
	if ( isset( $data["honeyyyyyyyyyy"] ) && empty( $data["honeyyyyyyyyyy"] ) )
	{
		$callBack["db"] = bkoFormidableSaveForm( $data );
		$callBack["email"] = bkoFormidableSendMail( $data );
		echo json_encode( $callBack );
	}
	else
	{
		header("HTTP/1.1 403 Forbidden");
		echo "{'error': 'bot detected'}";
	}
	die();
}
/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-18 )
 *
 * @return Bool
 */
function bkoFormidableSubmitCommentForm()
{
	$data = $_GET;
	$callBack["db"] = bkoFormidableSaveForm( $data );
	echo json_encode( $callBack );
	die();
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-17 )
 *
 * @param array $data
 * @return Bool
 */
function bkoFormidableSaveForm( $data = [] )
{
	global $wpdb;
	$table_name = bkoforminable_get_table_name();
	$tpl = $data['templateName'];
	if ( isset( $data['id'] ) )
	{
		$sql = "UPDATE  $table_name SET" .
		"`comment` = '" . $data['comment'] . "'" .
		"WHERE `id` = " . $data['id'] . ";";
	}
	else
	{
		if ( isset( $data['templateName'] ) )
		{
			unset( $data['templateName'] );
		}
		if ( isset( $data['honeyyyyyyyyyy'] ) )
		{
			unset( $data['honeyyyyyyyyyy'] );
		}
		if ( isset( $data['successMessage'] ) )
		{
			unset( $data['successMessage'] );
		}
		$sql = "INSERT INTO $table_name (" .
		"`template_name`," .
		"`url`," .
		"`content`," .
		"`created_at`)" .
		"VALUES (" .
			"'" . $tpl . "'," .
			"'" . $_SERVER["HTTP_REFERER"] . "'," .
			"'" . json_encode( $data, JSON_UNESCAPED_UNICODE ) . "'," .
			"'" . date( 'Y-m-d h:i:s', time() ) . "'" .
		");";
	}

	return $wpdb->query( $sql );
}

/**
 * @author Golga <r-ro@bulko.net>
 * @since 1.0.0 ( 2018-07-17 )
 *
 * @param array $data
 * @return Bool
 */
function bkoFormidableSendMail( $data = [] )
{
	if ( isset( _BKOFORMIDABLE_MAILTO_[0] ) && isset( $data["email"] ) )
	{
		$to = _BKOFORMIDABLE_MAILTO_[0];
		$subject = _BKOFORMIDABLE_MAIL_SUBJECT_;
		$body = 'The email body content';
		$headers = [
			'From: ' . _BKOFORMIDABLE_MAIL_FROM_,
			'Content-Type: text/html; charset=UTF-8',
		];
		for ( $i=1; $i < sizeof( _BKOFORMIDABLE_MAILTO_ ); $i++ )
		{
			$headers[] = 'Cc: ' . _BKOFORMIDABLE_MAILTO_[$i];
		}
		return wp_mail( $to, $subject, $body, $headers, [] );
	}
	return false;
}

add_action( 'wp_ajax_bkoFormidableSubmitForm',"bkoFormidableSubmitForm", 10 );
add_action( 'wp_ajax_bkoFormidableSubmitCommentForm',"bkoFormidableSubmitCommentForm", 10 );
?>
