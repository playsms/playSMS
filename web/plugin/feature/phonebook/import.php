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

if (!auth_isvalid()) {
	auth_block();
}

$uid = $user_config['uid'];

switch (_OP_) {
	case "list":
		$content = _dialog() . "
			<h2>" . _('Phonebook') . "</h2>
			<h3>" . _('Import') . "</h3>
			<table class=ps_table>
				<tbody>
					<tr>
						<td>
							<form action=\"index.php?app=main&inc=feature_phonebook&route=import&op=import\" enctype=\"multipart/form-data\" method=POST>
							" . _CSRF_FORM_ . "
							<p>" . _('Please select CSV file for phonebook entries') . "</p>
							<p><input type=\"file\" name=\"fnpb\"></p>
							<p class=text-info>" . _('CSV file format') . " : " . _('Name') . ", " . _('Mobile') . ", " . _('Email') . ", " . _('Group code') . ", " . _('Tags') . "</p>
							<p><input type=\"submit\" value=\"" . _('Import') . "\" class=\"button\"></p>
							</form>
						</td>
					</tr>
				</tbody>
			</table>
			" . _back('index.php?app=main&inc=feature_phonebook&op=phonebook_list');
		_p($content);
		break;

	case "import":

		// fixme anton - https://www.exploit-db.com/exploits/42003
		$fnpb_name = core_sanitize_filename($_FILES['fnpb']['name']);
		if ($fnpb_name && $fnpb_name == $_FILES['fnpb']['name']) {
			$continue = true;
		} else {
			$continue = false;
		}

		$fnpb = $_FILES['fnpb'];
		$fnpb_tmpname = $_FILES['fnpb']['tmp_name'];
		$content = "
			<h2>" . _('Phonebook') . "</h2>
			<h3>" . _('Import confirmation') . "</h3>
			<div class=table-responsive>
			<table class=playsms-table-list>
			<thead><tr>
				<th width=\"5%\">*</th>
				<th width=\"20%\">" . _('Name') . "</th>
				<th width=\"20%\">" . _('Mobile') . "</th>
				<th width=\"25%\">" . _('Email') . "</th>
				<th width=\"15%\">" . _('Group code') . "</th>
				<th width=\"15%\">" . _('Tags') . "</th>
			</tr></thead><tbody>";

		if ($continue && file_exists($fnpb_tmpname)) {
			$session_import = 'phonebook_' . _PID_;
			unset($_SESSION['tmp'][$session_import]);
			ini_set('auto_detect_line_endings', true);
			if (($fp = fopen($fnpb_tmpname, "r")) !== false) {
				$i = 0;
				while ($c_contact = fgetcsv($fp, 1000, ',', '"', '\\')) {
					if ($i > $phonebook_row_limit) {
						break;
					}
					if ($i > 0) {

						// fixme anton - https://www.exploit-db.com/exploits/42044
						$c_contact = core_sanitize_inputs($c_contact);

						//$c_contant[0] = core_sanitize_string($c_contact[0]);
						$c_contant[1] = core_sanitize_mobile($c_contact[1]);
						//$c_contact[2] = core_sanitize_email($c_contact[2]);
						$c_gid = phonebook_groupcode2id($uid, $c_contact[3]);
						if (!$c_gid) {
							$c_contact[3] = '';
						}
						$c_contact[4] = phonebook_tags_clean($c_contact[4]);

						$contacts[$i] = $c_contact;
					}
					$i++;
				}
				$i = 0;
				foreach ( $contacts as $contact ) {
					if ($contact[0] && $contact[1]) {
						$i++;
						$content .= "
							<tr>
							<td>$i.</td>
							<td>$contact[0]</td>
							<td>$contact[1]</td>
							<td>$contact[2]</td>
							<td>$contact[3]</td>
							<td>$contact[4]</td>
							</tr>";
						$k = $i - 1;
						$_SESSION['tmp'][$session_import][$k] = $contact;
					}
				}
			}
			ini_set('auto_detect_line_endings', false);
			$content .= "
				</tbody></table>
				</div>
				<p>" . _('Import above phonebook entries ?') . "</p>
				<form action=\"index.php?app=main&inc=feature_phonebook&route=import&op=import_yes\" method=POST>
				" . _CSRF_FORM_ . "
				<input type=\"hidden\" name=\"number_of_row\" value=\"$j\">
				<input type=\"hidden\" name=\"session_import\" value=\"" . $session_import . "\">
				<p><input type=\"submit\" class=\"button\" value=\"" . _('Import') . "\"></p>
				</form>
				" . _back('index.php?app=main&inc=feature_phonebook&route=import&op=list');
			_p($content);
		} else {
			$_SESSION['dialog']['danger'][] = _('Fail to upload CSV file for phonebook');
			header("Location: " . _u('index.php?app=main&inc=feature_phonebook&route=import&op=list'));
			exit();
		}
		break;

	case "import_yes":
		@set_time_limit(0);
		$num = $_POST['number_of_row'];
		$session_import = $_POST['session_import'];

		// fixme anton - should not need this due to data in this session should be sanitized already
		$data = core_sanitize_inputs($_SESSION['tmp'][$session_import]);

		// $i = 0;
		foreach ( $data as $d ) {
			$name = trim($d[0]);
			$mobile = trim($d[1]);
			$email = trim($d[2]);
			if ($group_code = trim($d[3])) {
				$gpid = phonebook_groupcode2id($uid, $group_code);
			}
			$tags = phonebook_tags_clean($d[4]);
			if ($name && $mobile) {
				// fixme anton - temporary - contacts not unique
				// if ($c_pid = phonebook_number2id($uid, $mobile)) {
				if (false) {
					if ($gpid) {
						$save_to_group = true;
					}
				} else {
					$items = [
						'uid' => $uid,
						'name' => $name,
						'mobile' => core_sanitize_mobile($mobile),
						'email' => $email,
						'tags' => $tags
					];
					if ($c_pid = dba_add(_DB_PREF_ . '_featurePhonebook', $items)) {
						if ($gpid) {
							$save_to_group = true;
						} else {
							_log('contact added pid:' . $c_pid . ' m:' . $mobile . ' n:' . $name . ' e:' . $email, 3, 'phonebook_add');
						}
					} else {
						_log('fail to add contact pid:' . $c_pid . ' m:' . $mobile . ' n:' . $name . ' e:' . $email . ' tags:[' . $tags . ']', 3, 'phonebook_add');
					}
				}
				if ($save_to_group && $gpid) {
					$db_query = "SELECT id FROM " . _DB_PREF_ . "_featurePhonebook_group_contacts WHERE gpid=? AND pid=? LIMIT 1";
					if (dba_num_rows($db_query, [$gpid, $c_pid]) > 0) {
						_log('contact already in the group gpid:' . $gpid . ' pid:' . $c_pid . ' m:' . $mobile . ' n:' . $name . ' e:' . $email, 3, 'phonebook_add');
					} else {
						$items = [
							'gpid' => $gpid,
							'pid' => $c_pid
						];
						if (dba_add(_DB_PREF_ . '_featurePhonebook_group_contacts', $items)) {
							_log('contact added to group gpid:' . $gpid . ' pid:' . $c_pid . ' m:' . $mobile . ' n:' . $name . ' e:' . $email, 3, 'phonebook_add');
						} else {
							_log('contact added but fail to save in group gpid:' . $gpid . ' pid:' . $c_pid . ' m:' . $mobile . ' n:' . $name . ' e:' . $email, 3, 'phonebook_add');
						}
					}
				}
				// $i++;
				// _log("no:".$i." gpid:".$gpid." uid:".$uid." name:".$name." mobile:".$mobile." email:".$email, 3, "phonebook import");
			}
			unset($gpid);
		}
		$_SESSION['dialog']['info'][] = _('Contacts have been imported');
		header("Location: " . _u('index.php?app=main&inc=feature_phonebook&route=import&op=list'));
		exit();
}
