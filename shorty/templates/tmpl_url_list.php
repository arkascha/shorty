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
 * @file templates/tmpl_url_list.php
 * A table to visualize the list of existing shortys.
 * @access public
 * @author Christian Reiner
 */

namespace OCA\Shorty;
?>

<?php namespace OCA\Shorty; ?>

<!-- the 'hourglass', a general busy indicator -->
<div id="hourglass" class="shorty-hourglass" style="left:5em;top:6em;"><img src="<?php p(\OCP\Util::imagePath('shorty', 'loading-disk.gif')); ?>"></div>

<!-- the list of urls, empty variant -->
<div id="vacuum" class="shorty-vacuum personalblock">
	<div class="factoid"><?php p(L10n::t("Nothing here yet")." !"); ?></div>
	<!-- a hint to use the personal preferences -->
	<p>
		<div class="suggestion"><?php p(L10n::t("If you just started using Shorty").":"); ?></div>
		<div class="explanation">
			<?php print_unescaped(sprintf(L10n::t("Set personal preferences using the cog wheel (%%s) on the upper right!" ),
				sprintf('<img id="controls-preferences" class="svg settings" style="vertical-align:bottom;cursor:pointer;" src="%s" />',
						\OCP\Util::imagePath('core', 'actions/settings.svg'))
				)); ?>
		</div>
	</p>
	<!-- a hint explaining how to create content -->
	<p>
		<div class="suggestion"><?php p(L10n::t("Create a new 'Shorty' and share it").":"); ?></div>
		<div class="explanation"><?php p(L10n::t("Use the '%s' button above or the fine 'Shortlet'",L10n::t("New Shorty")).":"); ?></div>
	</p>
<?php require_once('tmpl_wdg_shortlet.php'); ?>
</div>

<!-- the list of urls, non-empty variant -->
<table id="list-of-shortys" class="shorty-list shorty-collapsible" style="display:none;">
	<thead>
		<tr id="list-of-shortys-titlebar" class="shorty-titlebar">
			<!-- a button to open/close the toolbar below -->
			<th id="list-of-shortys-favicon" data-aspect="favicon">
				<div>
					<img id="list-of-shortys-tools" class="shorty-tools svg" alt="toolbar" title="<?php p(L10n::t("Toggle toolbar")); ?>"
						src="<?php p(\OCP\Util::imagePath('shorty','actions/unshade.svg')); ?>"
						data-unshade="actions/unshade" data-shade="actions/shade">
				</div>
			</th>
			<th id="list-of-shortys-title"    data-aspect="title"    class="collapsible"><div><?php p(L10n::t('Title'));     ?></div></th>
			<th id="list-of-shortys-target"   data-aspect="target"   class="collapsible"><div><?php p(L10n::t('Target'));    ?></div></th>
			<th id="list-of-shortys-clicks"   data-aspect="clicks"   class="collapsible"><div><?php p(L10n::t('Clicks'));    ?></div></th>
			<th id="list-of-shortys-until"    data-aspect="until"    class="collapsible"><div><?php p(L10n::t('Expiration'));?></div></th>
			<th id="list-of-shortys-created"  data-aspect="created"  class="collapsible"><div><?php p(L10n::t('Creation'));  ?></div></th>
			<th id="list-of-shortys-accessed" data-aspect="accessed" class="collapsible"><div><?php p(L10n::t('Access'));    ?></div></th>
			<th id="list-of-shortys-status"   data-aspect="status"   class="collapsible"><div><?php p(L10n::t('Status'));    ?></div></th>
			<th id="list-of-shortys-actions"  data-aspect="actions"><div>&nbsp;</div></th>
		</tr>
		<!-- toolbar opened/closed by the button above -->
		<tr id="list-of-shortys-toolbar" class="shorty-toolbar">
			<th id="list-of-shortys-favicon" data-aspect="favicon">
				<div style="display:none;">
					<a id="list-of-shortys-reload" class="shorty-reload">
						<img alt="<?php p(L10n::t('reload')); ?>" title="<?php p(L10n::t('Reload list')); ?>"
							class="shorty-tool shorty-reload svg" src="<?php p(\OCP\Util::imagePath('shorty','actions/reload.svg')); ?>">
					</a>
				</div>
			</th>
			<th id="list-of-shortys-title" data-aspect="title" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 't'])); ?>
					<input id="filter-title" class="shorty-filter" type="text" value="">
					<img id="clear" alt="<?php p(L10n::t('clear')); ?>" title="<?php p(L10n::t('Clear filter')); ?>"
						class="shorty-clear svg" src="<?php p(\OCP\Util::imagePath('shorty','actions/clear.svg')); ?>">
				</div>
			</th>
			<th id="list-of-shortys-target" data-aspect="target" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 'u'])); ?>
					<input id="filter-target" class="shorty-filter" type="text" value="">
					<img id="clear" alt="<?php p(L10n::t('clear')); ?>" title="<?php p(L10n::t('Clear filter')); ?>"
						class="shorty-clear svg" src="<?php p(\OCP\Util::imagePath('shorty','actions/clear.svg')); ?>">
				</div>
			</th>
			<th id="list-of-shortys-clicks" data-aspect="clicks" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 'h'])); ?>
				</div>
			</th>
			<th id="list-of-shortys-until" data-aspect="until" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 'd'])); ?>
				</div>
			</th>
			<th id="list-of-shortys-created" data-aspect="created" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 'c'])); ?>
				</div>
			</th>
			<th id="list-of-shortys-accessed" data-aspect="accessed" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<?php print_unescaped($this->inc('tmpl_tools_sortable', ['sortcol' => 'a'])); ?>
				</div>
			</th>
			<th id="list-of-shortys-status" data-aspect="status" class="collapsible">
				<div style="display:none;">
					<?php print_unescaped($this->inc('tmpl_tools_collapsible')); ?>
					<span class="shorty-select">
						<select id="filter-status" class="shorty-filter" data-placeholder=" ">
						<?php foreach($_['shorty-status'] as $option=>$label)
							print_unescaped(sprintf("<option value=\"%s\">%s</option>\n",($option?$option:''),$label));
						?>
						</select>
					</span>
					<img id="clear" alt="<?php p(L10n::t('clear')); ?>" title="<?php p(L10n::t('Clear filter')); ?>"
						class="shorty-tool shorty-clear svg" src="<?php p(\OCP\Util::imagePath('shorty','actions/clear.svg')); ?>">
				</div>
			</th>
		</tr>
		<!-- the 'dummy' row, a blueprint -->
		<tr class="shorty-dummy"
			data-id=""
			data-status=""
			data-source=""
			data-relay=""
			data-title=""
			data-favicon=""
			data-target=""
			data-clicks=""
			data-until=""
			data-created=""
			data-accessed=""
			data-notes="">
			<td id="list-of-shortys-favicon"  data-aspect="favicon"></td>
			<td id="list-of-shortys-title"    data-aspect="title"    class="collapsible"></td>
			<td id="list-of-shortys-target"   data-aspect="target"   class="collapsible"></td>
			<td id="list-of-shortys-clicks"   data-aspect="clicks"   class="collapsible"></td>
			<td id="list-of-shortys-until"    data-aspect="until"    class="collapsible"></td>
			<td id="list-of-shortys-created"  data-aspect="created"  class="collapsible"></td>
			<td id="list-of-shortys-accessed" data-aspect="accessed" class="collapsible"></td>
			<td id="list-of-shortys-status"   data-aspect="status"   class="collapsible"></td>
			<td id="list-of-shortys-actions"  data-aspect="actions">
				<span class="shorty-actions">
<!-- actions are registered via hooks, action icons will appear here -->
<?php foreach ( $_['shorty-actions'] as $action ) { ?>
				<a id="shorty-action-<?php p($action->getActionName()); ?>" title="<?php p($action->getActionAlt()); ?>" data-method="<?php p($action->getActionCallback()); ?>" class="">
					<img alt="<?php p($action->getActionAlt()); ?>" title="<?php p($action->getActionTitle()); ?>"
						class="shorty-icon svg" src="<?php p($action->getActionIcon()); ?>" />
				</a>
<?php } ?>
				</span>
			</td>
		</tr>
	</thead>
	<!-- the standard body for non-empty lists -->
	<tbody>
	</tbody>
</table>
