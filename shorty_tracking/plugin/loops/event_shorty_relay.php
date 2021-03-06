<?php
/**
 * @package shorty-tracking an ownCloud url shortener plugin addition
 * @category internet
 * @author Christian Reiner
 * @copyright 2012-2015 Christian Reiner <foss@christian-reiner.info>
 * @license GNU Affero General Public license (AGPL)
 * @link information http://apps.owncloud.com/content/show.php/Shorty+Tracking?content=152473
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
 * @file plugin/loops/event_shorty_relay.php
 * @author Christian Reiner
 */

namespace OCA\Shorty\Tracking\Loop;

/**
 * @class OCA\Shorty\Tracking\Loop\EventShortyRequest
 * @extends \OCA\Shorty\Plugin\Event
 * @brief Static 'namespace' class for api hook population
 * @access public
 * @author Christian Reiner
 */
class EventShortyRelay
{
	static public function process($param) {
		list($shorty, $request, $result) = $param;
		\OCP\Util::writeLog ( 'shorty_tracking',
			sprintf("Recording relay event on Shorty '%s' with result '%s'", $shorty->getId(), $result),
			\OCP\Util::DEBUG );
		$param  = [
			':shorty'    => $shorty->getId(),
			':time'      => $request->getTime(),
			':user'      => $request->getUser(),
			':address'   => $request->getAddress(),
			':host'      => $request->getHost()==$request->getAddress() ? ' - ? - ' : $request->getHost(),
			':result'    => $result
		];
		$query = \OCP\DB::prepare ( \OCA\Shorty\Tracking\Query::CLICK_RECORD );
		$query->execute ( $param );
		return TRUE;

	}
}
