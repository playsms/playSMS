<?php defined('_SECURE_') or die('Forbidden'); ?>
<?php
if(!auth_isadmin()){auth_block();};

// main
switch (_OP_) {
	case 'members':
		$rid = $_REQUEST['rid'];
		$data = inboxgroup_getdatabyid($rid);
		$in_receiver = $data['in_receiver'];
		$keywords = $data['keywords'];
		$description = $data['description'];
		$c_members = is_countable(inboxgroup_getmembers($rid)) ? count(inboxgroup_getmembers($rid)) : 0;
		$c_members = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid)."'>".$c_members."</a>";
		$c_catchall = is_countable(inboxgroup_getcatchall($rid)) ? count(inboxgroup_getcatchall($rid)) : 0;
		$c_catchall = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=catchall&op=catchall&rid='.$rid)."'>".$c_catchall."</a>";
		$c_status = $data['status'] ? "<span class=status_enabled />" : "<span class=status_disabled />";
		$content = _dialog() . "
			<h2>"._('Group inbox')."</h2>
			<h3>"._('Member list')."</h3>
			<table class=playsms-table>
			<tr><td class=label-sizer>"._('Receiver number')."</td><td>".$in_receiver."</td></tr>
			<tr><td>"._('Keywords')."</td><td>".$keywords."</td></tr>
			<tr><td>"._('Description')."</td><td>".$description."</td></tr>
			<tr><td>"._('Members')."</td><td>".$c_members."</td></tr>
			<tr><td>"._('Catch-all')."</td><td>".$c_catchall."</td></tr>
			<tr><td>"._('Status')."</td><td>".$c_status."</td></tr>
			</table>
			<table>
			<div style='float: left'>
				<form method='post' action='index.php?app=main&inc=feature_inboxgroup&route=members&op=members_add&rid=".$rid."'>
				"._CSRF_FORM_."
				<input class='button' type='submit' value='"._('Add member')."'>
				</form>
			</div>
			<div>
				<form method='post' action='index.php?app=main&inc=feature_inboxgroup&route=members&op=members_delete&rid=".$rid."'>
				"._CSRF_FORM_."
				<input class='button' type='submit' value='"._('Delete member')."'>
				</form>
			</div>
			<div class=table-responsive>
			<table class=playsms-table-list>
			<thead><tr>
				<th width='30%'>"._('Username')."</th>
				<th width='50%'>"._('Name')."</th>
				<th width='20%'>"._('Mobile')."</th>
			</tr></thead>
			<tbody>";
		$members = inboxgroup_getmembers($rid);
		$j=0;
		for ($i=0;$i<(is_countable($members) ? count($members) : 0);$i++) {
			$c_uid = $members[$i]['uid'];
			$c_user = user_getdatabyuid($c_uid);
			if ($c_username = $c_user['username']) {
				$c_name = $c_user['name'];
				$c_mobile = $c_user['mobile'];
				$j++;
				$content .= "
					<tr>
						<td>".$c_username."</td>
						<td>".$c_name."</td>
						<td>".$c_mobile."</td>
					</tr>";
			}
		}
		$content .= "
			</tbody>
			</table>
			</div>
			<div style='float: left'>
				<form method='post' action='index.php?app=main&inc=feature_inboxgroup&route=members&op=members_add&rid=".$rid."'>
				"._CSRF_FORM_."
				<input class='button' type='submit' value='"._('Add member')."'>
				</form>
			</div>
			<div>
				<form method='post' action='index.php?app=main&inc=feature_inboxgroup&route=members&op=members_delete&rid=".$rid."'>
				"._CSRF_FORM_."
				<input class='button' type='submit' value='"._('Delete member')."'>
				</form>
			</div>
		"._back('index.php?app=main&inc=feature_inboxgroup&op=list');
		_p($content);
		break;
	case 'members_add':
		$rid = $_REQUEST['rid'];
		$data = inboxgroup_getdatabyid($rid);
		$in_receiver = $data['in_receiver'];
		$keywords = $data['keywords'];
		$description = $data['description'];
		$c_members = is_countable(inboxgroup_getmembers($rid)) ? count(inboxgroup_getmembers($rid)) : 0;
		$c_members = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid)."'>".$c_members."</a>";
		$c_catchall = is_countable(inboxgroup_getcatchall($rid)) ? count(inboxgroup_getcatchall($rid)) : 0;
		$c_catchall = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=catchall&op=catchall&rid='.$rid)."'>".$c_catchall."</a>";
		$c_status = $data['status'] ? "<span class=status_enabled />" : "<span class=status_disabled />";
		$content = _dialog() . "
			<h2>"._('Group inbox')."</h2>
			<h3>"._('Add member')."</h3>
			<table class=playsms-table>
			<tr><td class=label-sizer>"._('Receiver number')."</td><td>".$in_receiver."</td></tr>
			<tr><td>"._('Keywords')."</td><td>".$keywords."</td></tr>
			<tr><td>"._('Description')."</td><td>".$description."</td></tr>
			<tr><td>"._('Members')."</td><td>".$c_members."</td></tr>
			<tr><td>"._('Catch-all')."</td><td>".$c_catchall."</td></tr>
			<tr><td>"._('Status')."</td><td>".$c_status."</td></tr>
			</table>";
		$list_of_users = '';
		// get admins
		$users = user_getallwithstatus(2);
		for ($i=0;$i<(is_countable($users) ? count($users) : 0);$i++) {
			$list_of_users .= "<option value='".$users[$i]['uid']."'>".$users[$i]['name']." ".$users[$i]['mobile']."</option>";
		}
		// get users
		$users = user_getallwithstatus(3);
		for ($i=0;$i<(is_countable($users) ? count($users) : 0);$i++) {
			$list_of_users .= "<option value='".$users[$i]['uid']."'>".$users[$i]['name']." ".$users[$i]['mobile']."</option>";
		}
		$content .= "
			<form action=\"index.php?app=main&inc=feature_inboxgroup&route=members&op=members_add_submit\" method=\"post\">
			"._CSRF_FORM_."
			<input type=hidden name='rid' value='".$rid."'>
			<table>
				<tbody>
				<tr>
					<td nowrap>
						"._('All users').":<br />
						<select name=\"uids_dump[]\" id=\"msg_combo_sendto\" size=\"10\" multiple=\"multiple\" onDblClick=\"moveSelectedOptions(this.form['uids_dump[]'],this.form['uids[]'])\">$list_of_users</select>
					</td>
					<td>
						<br />
						<p><input type=\"button\" class=\"button\" value=\"&gt;\" onclick=\"moveSelectedOptions(this.form['uids_dump[]'],this.form['uids[]'])\"></p>
						<p><input type=\"button\" class=\"button\" value=\"&lt;\" onclick=\"moveSelectedOptions(this.form['uids[]'],this.form['uids_dump[]'])\"></p>
					</td>
					<td nowrap>
					    "._('Selected users').":<br>
					    <select name=\"uids[]\" id=\"msg_combo_sendto\" size=\"10\" multiple=\"multiple\" onDblClick=\"moveSelectedOptions(this.form['uids[]'],this.form['uids_dump[]'])\"></select>
					</td>
				</tr>
				</tbody>
			</table>
			<p>"._('Press submit button to add selected users to member list')."</p>
			<p><input class='button' type='submit' value='Submit' onClick=\"selectAllOptions(this.form['uids[]'])\"></p>
			</form>
		"._back('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid);
		_p($content);
		break;
	case 'members_add_submit':
		$rid = $_REQUEST['rid'];
		$rid = $_REQUEST['rid'];
		$data = inboxgroup_getdatabyid($rid);
		$in_receiver = $data['in_receiver'];
		if ($rid && $in_receiver) {
			$uids = $_REQUEST['uids'];
			for ($i=0;$i<(is_countable($uids) ? count($uids) : 0);$i++) {
				$c_uid = $uids[$i];
				$c_username = user_uid2username($c_uid);
				if (inboxgroup_membersadd($rid, $c_uid)) {
					$_SESSION['dialog']['info'][] .= _('Member has been added')." ("._('Username').": ".$c_username.")<br />";
				} else {
					$_SESSION['dialog']['info'][] .= _('Fail to add member')." ("._('Username').": ".$c_username.")<br />";
				}
			}
		} else {
			$_SESSION['dialog']['info'][] = _('Receiver number does not exist');
		}
		header("Location: "._u('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid));
		exit();
		break;
	case 'members_delete':
		$rid = $_REQUEST['rid'];
		$data = inboxgroup_getdatabyid($rid);
		$in_receiver = $data['in_receiver'];
		$keywords = $data['keywords'];
		$description = $data['description'];
		$c_members = is_countable(inboxgroup_getmembers($rid)) ? count(inboxgroup_getmembers($rid)) : 0;
		$c_members = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid)."'>".$c_members."</a>";
		$c_catchall = is_countable(inboxgroup_getcatchall($rid)) ? count(inboxgroup_getcatchall($rid)) : 0;
		$c_catchall = "<a href='"._u('index.php?app=main&inc=feature_inboxgroup&route=catchall&op=catchall&rid='.$rid)."'>".$c_catchall."</a>";
		$c_status = $data['status'] ? "<span class=status_enabled />" : "<span class=status_disabled />";
		$content = _dialog() . "
			<h2>"._('Group inbox')."</h2>
			<h3>"._('Delete member')."</h3>
			<table width='100%>
			<tr><td class=label-sizer>"._('Receiver number')."</td><td>".$in_receiver."</td></tr>
			<tr><td>"._('Keywords')."</td><td>".$keywords."</td></tr>
			<tr><td>"._('Description')."</td><td>".$description."</td></tr>
			<tr><td>"._('Members')."</td><td>".$c_members."</td></tr>
			<tr><td>"._('Catch-all')."</td><td>".$c_catchall."</td></tr>
			<tr><td>"._('Status')."</td><td>".$c_status."</td></tr>
			</table>";
		$list_of_members = '';
		// get members
		$users = inboxgroup_getmembers($rid);
		for ($i=0;$i<(is_countable($users) ? count($users) : 0);$i++) {
			$c_uid = $users[$i]['uid'];
			$c_user = user_getdatabyuid($c_uid);
			if ($c_username = $c_user['username']) {
				$c_name = $c_user['name'];
				$c_mobile = $c_user['mobile'];
				$list_of_users .= "<option value='".$c_uid."'>".$c_name." ".$c_mobile."</option>";
			}
		}
		$content .= "
			<form action=\"index.php?app=main&inc=feature_inboxgroup&route=members&op=members_delete_submit\" method=\"post\">
			"._CSRF_FORM_."
			<input type=hidden name='rid' value='".$rid."'>
			<table>
				<tbody>
				<tr>
					<td nowrap>
						"._('Current members').":<br />
						<select name=\"uids_dump[]\" id=\"msg_combo_sendto\" size=\"10\" multiple=\"multiple\" onDblClick=\"moveSelectedOptions(this.form['uids_dump[]'],this.form['uids[]'])\">$list_of_users</select>
					</td>
					<td>
						<br />
						<p><input type=\"button\" class=\"button\" value=\"&gt;\" onclick=\"moveSelectedOptions(this.form['uids_dump[]'],this.form['uids[]'])\"></p>
						<p><input type=\"button\" class=\"button\" value=\"&lt;\" onclick=\"moveSelectedOptions(this.form['uids[]'],this.form['uids_dump[]'])\"></p>
					</td>
					<td nowrap>
						"._('Selected members').":<br>
						<select name=\"uids[]\" id=\"msg_combo_sendto\" size=\"10\" multiple=\"multiple\" onDblClick=\"moveSelectedOptions(this.form['uids[]'],this.form['uids_dump[]'])\"></select>
					</td>
				</tr>
				</tbody>
			</table>
			<p>"._('Press submit button to remove selected members from member list')."</p>
			<p><input class='button' type='submit' value='Submit' onClick=\"selectAllOptions(this.form['uids[]'])\"></p>
			</form>
		"._back('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid);
		_p($content);
		break;
	case 'members_delete_submit':
		$rid = $_REQUEST['rid'];
		$rid = $_REQUEST['rid'];
		$data = inboxgroup_getdatabyid($rid);
		$in_receiver = $data['in_receiver'];
		if ($rid && $in_receiver) {
			$uids = $_REQUEST['uids'];
			for ($i=0;$i<(is_countable($uids) ? count($uids) : 0);$i++) {
				$c_uid = $uids[$i];
				$c_username = user_uid2username($c_uid);
				if (inboxgroup_membersdel($rid, $c_uid)) {
					$_SESSION['dialog']['info'][] .= _('Member has been deleted')." ("._('Username').": ".$c_username.")<br />";
				} else {
					$_SESSION['dialog']['info'][] .= _('Fail to delete member')." ("._('Username').": ".$c_username.")<br />";
				}
			}
		} else {
			$_SESSION['dialog']['info'][] = _('Receiver number does not exist');
		}
		header("Location: "._u('index.php?app=main&inc=feature_inboxgroup&route=members&op=members&rid='.$rid));
		exit();
		break;
}

?>
