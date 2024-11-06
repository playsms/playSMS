<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

// gateway configuration in registry
$reg = gateway_get_registry('playnet');

// plugin configuration
$plugin_config['playnet'] = [
	'name' => 'playnet',
	'callback_url' => isset($reg['callback_url']) && $reg['callback_url'] ? $reg['callback_url'] : '',
	'callback_authcode' => isset($reg['callback_authcode']) && $reg['callback_authcode'] ? $reg['callback_authcode'] : '',
	'callback_server' => isset($reg['callback_server']) && $reg['callback_server'] ? $reg['callback_server'] : '',
	'poll_interval' => (int) ($reg['poll_interval'] ?? 10),
	'poll_limit' => (int) ($reg['poll_limit'] ?? 400),
	'module_sender' => isset($reg['module_sender']) ? $reg['module_sender'] : '',
	'datetime_timezone' => isset($reg['datetime_timezone']) ? $reg['datetime_timezone'] : '',
];

// smsc configuration
$plugin_config['playnet']['_smsc_config_'] = [
	'callback_authcode' => _('Callback authcode'),
	'callback_server' => _('Callback server'),
	'remote_on' => _('Fetch SMS from playnet server'),
	'playnet_smsc' => _('Playnet server SMSC'),
	'module_sender' => _('Module sender ID'),
	'module_timezone' => _('Module timezone')
];
