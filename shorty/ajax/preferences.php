<?php
/**
* @package shorty an ownCloud url shortener plugin
* @category internet
* @author Christian Reiner
* @copyright 2011-2015 Christian Reiner <foss@christian-reiner.info>
* @license GNU Affero General Public license (AGPL)
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
 * @file ajax/preferences.php
 * @brief Ajax method to store one or more personal preferences
 * @param string $backend-type: Identifier of chosen backend type
 * @param string $backend-static-base: Url to use as a base when the static backend is active
 * @param string $backend-google-key: Personal authentication key to use when the google backend is active
 * @param string $backend-bitly-key: Personal authentication key to use when the bit.li backend is active
 * @param string $backend-bitly-user: Personal authentication user to use when the bit.li backend is active
 * @param string $sms-control: Controls wether a 'send as sms' action should be offered is the sharing dialog
 * @param string $verbosity-control: A severity threshold controlling what messages will be displayed to the user
 * @param string $verbosity-timeout: An oiptional time span after which shown messages will be hidden again automatically
 * @param string $list-sort-code: Two character sorting key controlling the active sorting of shorty lists
 * @return json: success/error state indicator
 * @return json: Associative array holding the stored values by their key
 * @return json: Human readable message describing the result
 * @author Christian Reiner
 */

namespace OCA\Shorty;

// swallow any accidental output generated by php notices and stuff to preserve a clean JSON reply structure
Tools::ob_control ( TRUE );

//no apps or filesystem
$RUNTIME_NOSETUPFS = true;

// Sanity checks
\OCP\JSON::callCheck ( );
\OCP\JSON::checkLoggedIn ( );
\OCP\JSON::checkAppEnabled ( 'shorty' );

try
{
	$data = [];
	switch ( $_SERVER['REQUEST_METHOD'] )
	{
		case 'POST':
			// detect provided preferences
			$data = [];
			foreach (array_keys($_POST) as $key)
			{
				if ( isset(Type::$PREFERENCE[$key]) ) // ignore unknown preference keys
				{
					$type = Type::$PREFERENCE[$key];
					$data[$key] = Type::req_argument ( $key, $type, FALSE );
				}
			}
			// store settings
			foreach ( $data as $key=>$val )
				\OCP\Config::setUserValue( \OCP\User::getUser(), 'shorty', $key, $val );
			// swallow any accidental output generated by php notices and stuff to preserve a clean JSON reply structure
			Tools::ob_control ( FALSE );
			\OCP\Util::writeLog( 'shorty', sprintf("Preference '%s' saved",implode(',',array_keys($data))), \OCP\Util::DEBUG );
			\OCP\JSON::success ( [
				'data'    => $data,
				'level'   => 'debug',
				'message' => L10n::t("Preference '%s' saved",implode(',',array_keys($data)))
			] );
			break;

		case 'GET':
			// detect requested preferences
			foreach (array_keys($_GET) as $key)
			{
				if ( isset(Type::$PREFERENCE[$key]) ) // ignore unknown preference keys
				{
					$type = Type::$PREFERENCE[$key];
					$data[$key] = \OCP\Config::getUserValue( \OCP\User::getUser(), 'shorty', $key);
					// morph value into an explicit type
					switch ($type)
					{
						case Type::ID:
						case Type::STATUS:
						case Type::SORTKEY:
						case Type::SORTVAL:
						case Type::JSON:
						case Type::STRING:
						case Type::URL:
						case Type::DATE:
							settype ( $data[$key], 'string' );
							break;
						case Type::INTEGER:
						case Type::TIMESTAMP:
							settype ( $data[$key], 'integer' );
							break;
						case Type::FLOAT:
							settype ( $data[$key], 'float' );
							break;
						default:
					} // switch
				}
			} // foreach
			// swallow any accidental output generated by php notices and stuff to preserve a clean JSON reply structure
			Tools::ob_control ( FALSE );
			\OCP\Util::writeLog( 'shorty', sprintf("Preference '%s' retrieved",implode(',',array_keys($data))), \OCP\Util::DEBUG );
			\OCP\JSON::success ( [
				'data'    => $data,
				'level'   => 'debug',
				'message' => L10n::t("Preference '%s' retrieved",implode(',',array_keys($data)))
			] );
			break;

		default:
			throw new Exception ( "unexpected request method '%s'", $_SERVER['REQUEST_METHOD'] );
	} // switch

} catch ( Exception $e ) { Exception::JSONerror($e); }
