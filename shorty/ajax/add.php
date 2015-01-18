<?php
/**
* @package shorty an ownCloud url shortener plugin
* @category internet
* @author Christian Reiner
* @copyright 2011-2015 Christian Reiner <foss@christian-reiner.info>
* @license GNU AFFERO GENERAL PUBLIC LICENSE (AGPL)
* @link information http://apps.owncloud.com/content/show.php/Shorty?content=150401
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the license, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.
* If not, see <http://www.gnu.org/licenses/>.
*
*/

/**
 * @file ajax/add.php
 * @brief Ajax method to add a new shorty defined by request arguments
 * @param string title: Human readable title of the shorty
 * @param url target: Remote target url meant to be shortened
 * @param date until: Date until when the created shorty is valid and usable
 * @param string notes: Any additional information in text form
 * @param url favicon: Reference to the shortcut icon used in target url
 * @return json: success/error state indicator
 * @return json: Associative array of attributes of the generated shorty
 * @return json: Human readable message
 * @author Christian Reiner
 */

// swallow any accidential output generated by php notices and stuff to preserve a clean JSON reply structure
OC_Shorty_Tools::ob_control ( TRUE );

//no apps or filesystem
$RUNTIME_NOSETUPFS = true;

// Sanity checks
OCP\JSON::callCheck ( );
OCP\JSON::checkLoggedIn ( );
OCP\JSON::checkAppEnabled ( 'shorty' );

try
{
	$p_id      = OC_Shorty_Tools::shorty_id ( );
	$p_status  = OC_Shorty_Type::req_argument ( 'status',  OC_Shorty_Type::STATUS, FALSE );
	$p_title   = OC_Shorty_Type::req_argument ( 'title',   OC_Shorty_Type::STRING, FALSE );
	$p_target  = OC_Shorty_Type::req_argument ( 'target',  OC_Shorty_Type::URL,    TRUE  );
	$p_until   = OC_Shorty_Type::req_argument ( 'until',   OC_Shorty_Type::DATE,   FALSE );
	$p_notes   = OC_Shorty_Type::req_argument ( 'notes',   OC_Shorty_Type::STRING, FALSE );
	$p_favicon = OC_Shorty_Type::req_argument ( 'favicon', OC_Shorty_Type::URL,    FALSE );
	// register shorty at backend
	$p_source = OC_Shorty_Backend::registerUrl ( $p_id );
	// fallback title: choose hostname if no title is specified
	$p_title = $p_title ? trim($p_title) : parse_url($p_target,PHP_URL_HOST);
	// insert new shorty into our database
	$param = array
	(
		':user'    => OCP\User::getUser(),
		':id'      => $p_id,
		':status'  => $p_status  ?        $p_status          : '',
		':title'   => $p_title   ? substr($p_title,  0,1024) : '',
		':favicon' => $p_favicon ? substr($p_favicon,0,1024) : '',
		':source'  => $p_source  ?        $p_source          : '',
		':target'  => $p_target  ? substr($p_target, 0,4096) : '',
		':notes'   => $p_notes   ? substr($p_notes,  0,4096) : '',
		':until'   => $p_until   ?        $p_until           : null,
	);
	$query = OCP\DB::prepare ( OC_Shorty_Query::URL_INSERT );
	$query->execute ( $param );

	// read new entry for feedback
	$param = array
	(
		':user' => OCP\User::getUser(),
		':id'   => $p_id,
	);
	$query = OCP\DB::prepare ( OC_Shorty_Query::URL_VERIFY );
	$entries = $query->execute($param)->FetchAll();
	if (  (1==count($entries))
		&&(isset($entries[0]['id']))
		&&($p_id==$entries[0]['id']) )
		$entries[0]['relay']=OC_Shorty_Tools::relayUrl ( $entries[0]['id'] );
	else
		throw new OC_Shorty_Exception ( "failed to verify stored shorty with id '%1s'", array($p_id) );

	// swallow any accidential output generated by php notices and stuff to preserve a clean JSON reply structure
	OC_Shorty_Tools::ob_control ( FALSE );
	OCP\Util::writeLog( 'shorty', sprintf("Created Shorty '%s' for target url '%s'",$p_source,$p_target), OCP\Util::INFO );
	OCP\JSON::success ( array (
		'data'    => $entries[0],
		'level'   => 'info',
		'message' => OC_Shorty_L10n::t("Url shortened to: %s",$p_source) ) );
} catch ( Exception $e ) { OC_Shorty_Exception::JSONerror($e); }
