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

/**
 * Add notification
 *
 * @param int $uid User ID
 * @param string $label Notification label
 * @param string $subject Notification subject
 * @param string $body Notification body
 * @param array $data Additional data, json encoded
 * @return bool
 */
function notif_add($uid, $label, $subject, $body, $data = [])
{
	$ret = false;

	if (!is_array($data)) {
		$data = [
			$data
		];
	}

	$db_table = _DB_PREF_ . '_tblNotif';
	$items = [
		'uid' => $uid,
		'last_update' => core_get_datetime(),
		'label' => $label,
		'subject' => $subject,
		'body' => $body,
		'flag_unread' => 1,
		'data' => json_encode($data)
	];
	if ($result = dba_add($db_table, $items)) {
		foreach ( $data as $key => $val ) {
			$show_data .= $key . ':' . $val . ' ';
		}
		_log('uid:' . $uid . ' id:' . $result . ' label:' . $label . ' subject:' . $subject . ' data:[' . trim($show_data) . ']', 2, 'notif_add');

		$ret = true;
	}

	return $ret;
}

/**
 * Remove notification
 *
 * @param int $uid User ID
 * @param string $id Notification ID
 * @return bool
 */
function notif_remove($uid, $id)
{
	$ret = false;

	$db_table = _DB_PREF_ . '_tblNotif';
	if (
		dba_remove(
			$db_table,
			[
				'uid' => $uid,
				'id' => $id
			]
		)
	) {
		_log('uid:' . $uid . ' id:' . $id, 2, 'notif_remove');

		$ret = true;
	}

	return $ret;
}

/**
 * Update notification
 *
 * @param int $uid User ID
 * @param string $id Notification ID
 * @param array $fields Updated fields
 * @return bool
 */
function notif_update($uid, $id, $fields)
{
	$ret = false;

	if (!is_array($fields)) {

		return false;
	}

	$db_table = _DB_PREF_ . '_tblNotif';
	$result = dba_search(
		$db_table,
		'*',
		[
			'uid' => $uid,
			'id' => $id
		]
	);
	$fields_replaced = '';
	foreach ( $result[0] as $key => $val ) {
		$items[$key] = ($fields[$key] ? $fields[$key] : $val);
		if ($fields[$key]) {
			$fields_replaced .= $key . ':' . $val . ' ';
		}
	}
	if ($items && trim($fields_replaced)) {
		if (
			dba_update(
				$db_table,
				$items,
				[
					'id' => $id
				]
			)
		) {
			_log('uid:' . $uid . ' id:' . $id . ' ' . trim($fields_replaced), 2, 'notif_update');

			$ret = true;
		}
	}

	return $ret;
}

/**
 * Search notification
 *
 * @param array $conditions Search requirements
 * @param array $keywords Search keywords
 * @return array
 */
function notif_search($conditions = [], $keywords = [])
{
	$db_table = _DB_PREF_ . '_tblNotif';
	$results = dba_search($db_table, '*', $conditions, $keywords);

	return $results;
}
