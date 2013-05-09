<?php
/**
* @package shorty an ownCloud url shortener plugin
* @category internet
* @author Christian Reiner
* @copyright 2011-2013 Christian Reiner <foss@christian-reiner.info>
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
 * @file verification.php
 * Verifies a specified url whether it is valid to be used as a base url for the static backend. 
 * @access public
 * @author Christian Reiner
 */

// session checks
OCP\User::checkLoggedIn  ( );
OCP\User::checkAdminUser ( );
OCP\App::checkAppEnabled ( 'shorty' );

$RUNTIME_NOSETUPFS = true;

OCP\Util::addStyle  ( 'shorty', 'shorty' );
// TODO: remove OC-4.0-compatibility:
if (OC_Shorty_Tools::versionCompare('<','4.80')) // OC-4.0
	OCP\Util::addStyle ( 'shorty', 'shorty-oc40' );
// TODO: remove OC-4.5-compatibility:
if (OC_Shorty_Tools::versionCompare('<','4.91')) // OC-4.5
	OCP\Util::addStyle ( 'shorty', 'shorty-oc45' );
OCP\Util::addStyle  ( 'shorty', 'verification' );

OCP\Util::addScript ( 'shorty', 'shorty' );
if ( OC_Log::DEBUG==OC_Config::getValue( "loglevel", OC_Log::WARN ) )
	OCP\Util::addScript ( 'shorty',  'debug' );
OCP\Util::addScript ( 'shorty', 'util' );
OCP\Util::addScript ( 'shorty', 'verification' );

// we cannot ise OCs template engine here, since it would add unwanted headers...
// so we have to 'simulate' using a template: 

try 
{
	// set requested target as global variable for use in template further down
	$target = OC_Shorty_Type::req_argument ( 'target',  OC_Shorty_Type::URL, FALSE );
} 
catch ( Exception $e ) 
{
	p ( sprintf('Error %s: %s',$e->getCode(),$e->getMessage()) );
}

// manipulate the config entry setting the Content-Security-Policy header sent by the template engine
// we modify that value to grant the ajax request required to verify the base url for the static backend
$csp_policy = OC_Config::getValue('custom_csp_policy', FALSE); // load and get global OC config into cache
// does the configuration define a policy (does not return the default FALSE)?

if ( ! empty($csp_policy) )
	// if so: manipulate it
	OC_Config::setValue ( 'custom_csp_policy', 
// 		preg_replace("/script-src (.*)'unsafe-eval'(.*)\\w?;/", 'script-src $1$2;', $csp_policy) );
		preg_replace("/script-src [^;]*\\w?;/", 'script-src * ;', $csp_policy) );
else
	// else define it
	OC_Config::setValue ( 'custom_csp_policy', 
		"default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; frame-src *; img-src *; font-src 'self' data:" );

// fetch template
$tmpl = new OCP\Template ( 'shorty', 'tmpl_wdg_verify', '_' ); // the undefined view '_' suppresses the typical OC framework

// inflate template
$tmpl->assign ( 'verification-target', $target );
// render template
$tmpl->printPage();

// reset tp previous config value for the csp policy
if ( ! empty($csp_policy) )
	OC_Config::setValue ( 'custom_csp_policy', $csp_policy );
else
	OC_Config::deleteKey ( 'custom_csp_policy' );
?>
