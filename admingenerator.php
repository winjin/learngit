
<?
	# -------------------------------------------------------------------
	# adam 20080606 PHP code generator
	# generate an admin tool to manage a table
	# -------------------------------------------------------------------
	# ToDos for Version 2.0
	# 1. allow alternate row colors (minor, no need for launch)
	# 2. add option to add JS cms dropdown menu (move to it from the tree menu)
	# 3. get calendar code drop down to work inside the AJAX edit part
	# 5. allow reporting on a view table but update on a regular table
	#
	require("$DOCUMENT_ROOT/lib/dbconnect.inc.php");
	require("$DOCUMENT_ROOT/lib/debug.inc.php");
	$version = '1.1';
	
	$style = read_file("$DOCUMENT_ROOT/ssi/styles.ssi");
	dbconnect();
	if($s == 1 || !$s) {
		# 1. get table name
		$body = "<form method=POST action=$_SERVER[PHP_SELF]?s=2>
                	Table name: <input type=text name=tbl> <font class=rrr>Table needs to exist in our CMS database first.</font>
                	<p>
                	Title for the Admin Tool: <input type=text name=title>
                	<p>
			Directory of the completed Admin Tool: <input type=text name=dir value='/admin'> (ex: /crm or /reports/popups)
                	<p>
			Max Records to show per page: <input type=text name=maxrow value=50>
                	<p>
			Security Groups Access to this Admin Tool: (select one or more)<br>
			<select name=auth[] class=bb size=20 multiple>
				<option value='Accounting'>Accounting</option>
				<option value='Collections'>Collections</option>
				<option value='Course Admin'>Course Admin</option>
				<option value='IT Managers'>IT Managers</option>
				<option value='Lab Operations'>Lab Operations</option>
				<option value='LearningGG'>LearningGG</option>
				<option value='Management'>Management</option>
				<option value='Marketing'>Marketing</option>
				<option value='Marketing Coordinators'>Marketing Coordinators</option>
				<option value='Marketing Managers'>Marketing Managers</option>
				<option value='NetCom Partners'>NetCom Partners</option>
				<option value='Office Admin'>Office Admin</option>
				<option value='Operations Managers'>Operations Managers</option>
				<option value='Placement Managers'>Placement Managers</option>
				<option value='Sales'>Sales</option>
				<option value='Sales Managers'>Sales Managers</option>
				<option value='State Administration'>State Administration</option>
				<option value='Software Development'>Software Development</option>
				<option value='Training Coordinators'>Training Coordinators</option>
				<option value='Training Managers'>Training Managers</option>
			</select>
                	<p>
                	<input type=submit name=submit value='Next'>
                	</form>";
	} else if($s == 2) {
		# 2. return table details and options
		if($_POST[auth]) {
			$auth = implode("','",$_POST[auth]);
			$auth = "'$auth'";
		} else {
			$auth = 0;
		}
		if($tbl) {
			$tbl = trim($tbl); # remove all whitespaces around table - otherwise it might cause problems
			$q = "exec sp_columns $tbl";
			$qrh = mssql_query($q);
			if(mssql_num_rows($qrh)) {
				while($row = mssql_fetch_assoc($qrh)) {
					if($row[TYPE_NAME] == 'int identity') {
						$select = 'N/A, this is the identity key';
						$editing = "<input type=checkbox name=view[{$row[COLUMN_NAME]}] value=1 checked> Enable column for viewing";
					} else {
						$editing = "<input type=checkbox name=view[{$row[COLUMN_NAME]}] value=1 checked> Enable column for viewing<br>
							<input type=checkbox checked name=enable[{$row[COLUMN_NAME]}]> Enable column for editing";
						$select = "<select name=option[{$row[COLUMN_NAME]}] class=bb>
                                                        <option value=text>Text Field</option>
                                                        <option value=textarea>Text Area</option>
                                                        <option value=zerone>Option: 0/1 Value</option>
                                                        <option value=activeinactive>Option: Active/Inactive Value</option>
                                                        <option value=tblcol>Existing CMS Table Column</option>
                                                        <option value=userdefinable>User Definable</option>
                                                        <option value=date>Date/Calendar Selection</option>
                                                        <option value=currentdate>Use getdate() function</option>
                                                        <option value=null>Set to NULL Value</option>
                                                        <option value=hidden>Hidden Value</option>
                                                        </select>";
					}
					$tr .= "<tr>
						<td class=bb align=left>$row[COLUMN_NAME]</td>
						<td class=bb align=left>$select</td>
						<td class=bb align=left>$editing</td>
						</tr>
						";
				}
				$body = "<form method=POST action=$_SERVER[PHP_SELF]?s=3>
					<input type=hidden name=tbl value=\"$tbl\">
					<input type=hidden name=title value=\"$title\">
					<input type=hidden name=dir value=\"$dir\">
					<input type=hidden name=maxrow value=\"$maxrow\">
					<input type=hidden name=auth value=\"$auth\">
					<b>Edit Administration Options for:<br>
					<dd>Table: <font class=rrr>$tbl</font></dd>
					<dd>Title: <font class=rrr>$title</font></dd>
					<dd>Target Directory: <font class=rrr>$dir</font></dd>
					<dd>Security Groups: <font class=rrr>$auth</font></dd>
					<dd>Records per Page: <font class=rrr>$maxrow</font></b></dd>
					<p>
					<table cellpadding=6 cellspacing=0 border=1>
					<tr>
					<td class=bbb align=left>Column Name</td>
					<td class=bbb align=left>Option</td>
					<td class=bbb align=left>Enable</td>
					</tr>
					$tr
					<tr>
					<td colspan=3 align=center class=bbb>Main Options for the Tool</td>
					</tr>
					<tr>
					<td colspan=2 align=left class=bb>Enable Admin tool to ADD new rows to this table</td>
					<td align=center><input type=checkbox checked name=enable_add></td>
					</tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to UPDATE rows in this table</td>
                                        <td align=center><input type=checkbox checked name=enable_update></td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to DELETE rows in this table</td>
                                        <td align=center><input type=checkbox name=enable_delete></td>
                                        </tr>
					<tr><td colspan=3 align=right><input type=submit name=submit value='Next'></td></tr>
					</table>";
			} else {
				$body = "No table $tbl exists or no columns found for this table.";
			}
		} else {
			$body = "Cannot find a tablename.";
		}
	} else if($s == 3) {
		# 3. expand on more detailed options
                if($tbl) {
                        $q = "exec sp_columns $tbl";
                        $qrh = mssql_query($q);
                        if(mssql_num_rows($qrh)) {
                                while($row = mssql_fetch_assoc($qrh)) {
                                        if($row[TYPE_NAME] == 'int identity') {
						if($view[$row[COLUMN_NAME]]) {
							$vw = ' checked';
							$talign = textalign($row[COLUMN_NAME],1,'');
						} else {
							$vw = '';
							$talign = '';
						}
                                                $select = 'N/A, this is the identity key';
                                                $editing = "<input type=checkbox name=view[{$row[COLUMN_NAME]}] value=1$vw> Enable column for viewing
							<input type=hidden name=option[{$row[COLUMN_NAME]}] value=0> $talign";
                                        } else {
						unset($select);
						if($option[$row[COLUMN_NAME]] == 'text') {
							if($row[PRECISION] < 50) {
								$sz = $row[PRECISION];
							} else {
								$sz = 50;
							}
							$select = "<b>Text Field</b><br>
								Field length: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb value=$sz> chars<br>
								Max length: <input type=text name=param2[{$row[COLUMN_NAME]}] class=bb value={$row[PRECISION]}> max chars<br>
								Default Value: <input type=text name=param3[{$row[COLUMN_NAME]}] class=bb>
								<input type=hidden name=option[{$row[COLUMN_NAME]}] value=text>";
						} else if($option[$row[COLUMN_NAME]] == 'textarea') {
                                                        $select = "<b>Text Area (actual size: $row[PRECISION])</b><br>
								Field length: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb value=80><br>
								Field height: <input type=text name=param2[{$row[COLUMN_NAME]}] class=bb value=5><br>
								Default Value: <input type=text name=param3[{$row[COLUMN_NAME]}] class=bb>
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=textarea>";
						} else if($option[$row[COLUMN_NAME]] == 'zerone') {
                                                        $select = "<b>Option select box - 0/1</b><br>
									Default value: <select name=param1[{$row[COLUMN_NAME]}] class=bb>
									<option value=0>0</option>
									<option value=1>1</option>
									</select>
                                                                	<input type=hidden name=option[{$row[COLUMN_NAME]}] value=zerone>";
						} else if($option[$row[COLUMN_NAME]] == 'activeinactive') {
                                                        $select = "<b>Option select box - Active/Inactive</b><br>
									Default value: <select name=param1[{$row[COLUMN_NAME]}] class=bb>
                                                                        <option value=Active>Active</option>
                                                                        <option value=Inactive>Inactive</option>
                                                                        </select>
                                                                        <input type=hidden name=option[{$row[COLUMN_NAME]}] value=activeinactive>";
						} else if($option[$row[COLUMN_NAME]] == 'tblcol') {
                                                        $select = "<b>Existing CMS Table Column</b><br>
                                                                Table.Column: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb> ex: tbl_Status.StatusDesc<br>
                                                                Default Value: <input type=text name=param2[{$row[COLUMN_NAME]}] class=bb> optional<br>
                                                                Use Identity Key from Table: <input type=checkbox name=param3[{$row[COLUMN_NAME]}] class=bb value=1 checked>
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=tblcol>";
						} else if($option[$row[COLUMN_NAME]] == 'userdefinable') {
                                                        $select = "<b>User Definable Options</b><br>
                                                                Definable options: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb> separate options with a comma<br>
                                                                Default Value: <input type=text name=param2[{$row[COLUMN_NAME]}] class=bb>
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=userdefinable>";
						} else if($option[$row[COLUMN_NAME]] == 'date') {
							$today = date("m/d/Y");
                                                        $select = "<b>Option Calendar Date Popup Selection</b><br>
								Start Date: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb value='$today'> (mm/dd/yyyy format)<br>
                                                                Calendar Start Range before Start Date: <input type=text name=param2[{$row[COLUMN_NAME]}] class=bb value=365> (days)<br>
                                                                Calendar End Range after Start Date: <input type=text name=param3[{$row[COLUMN_NAME]}] class=bb value=365> (days)<br>
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=date>";
						} else if($option[$row[COLUMN_NAME]] == 'currentdate') {
                                                        $select = "<b>Hidden, set to default getdate() function.</b><br>
								Field set to current date using the getdate() function.
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=currentdate>";
							$enable[$row[COLUMN_NAME]] = 1;
						} else if($option[$row[COLUMN_NAME]] == 'null') {
                                                        $select = "<b>Hidden, set to NULL value.</b><br>
								Field set to NULL value.
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=null>";
							$enable[$row[COLUMN_NAME]] = 1;
						} else if($option[$row[COLUMN_NAME]] == 'hidden') {
                                                        if($row[PRECISION] < 50) {
                                                                $sz = $row[PRECISION];
                                                        } else {
                                                                $sz = 50;
                                                        }
                                                        $select = "<b>Hidden Field</b><br>
								Default Hidden Field value: <input type=text name=param1[{$row[COLUMN_NAME]}] class=bb size=$sz maxlength={$row[PRECISION]}>
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=hidden>";
							$enable[$row[COLUMN_NAME]] = 1;
						}
                                                if($view[$row[COLUMN_NAME]]) {
                                                        $editing = "<input type=checkbox checked name=view[{$row[COLUMN_NAME]}] value=1> Enable column for viewing<br>";
							$editing .= textalign($row[COLUMN_NAME],1,'');
							if($row[TYPE_NAME] == 'datetime') {
								$editing .= dateviewformat($row[COLUMN_NAME],1,'');
							}
                                                } else {
                                                        $editing = "<input type=hidden name=view[{$row[COLUMN_NAME]}] value=0> Column disabled for viewing<br>";
                                                }
						if($enable[$row[COLUMN_NAME]]) {
                                                	$editing .= "<input type=checkbox checked name=enable[{$row[COLUMN_NAME]}]> Enable column for editing";
						} else {
                                                	$editing .= "<input type=hidden name=enable[{$row[COLUMN_NAME]}] value=0>Column disabled for editing";
							if(!$select) {
								$select = "<input type=hidden name=option[{$row[COLUMN_NAME]}] value=0>&nbsp;";
							}
						}
                                        }
                                        $tr .= "<tr>
                                                <td class=bb align=left>$row[COLUMN_NAME]</td>
                                                <td class=bb align=left>$select</td>
                                                <td class=bb align=left>$editing</td>
                                                </tr>
                                                ";
                                }
				if($enable_add) { 
					$eadd = ' checked'; 
				} else {
					$eadd = '';
				}
				if($enable_update) { 
					$eupd = ' checked'; 
				} else {
					$eupd = '';
				}
				if($enable_delete) { 
					$edel = ' checked'; 
				} else {
					$edel = '';
				}
                                $body = "<form method=POST action=$_SERVER[PHP_SELF]?s=4>
                                        <input type=hidden name=tbl value=\"$tbl\">
					<input type=hidden name=title value=\"$title\">
					<input type=hidden name=dir value=\"$dir\">
					<input type=hidden name=maxrow value=\"$maxrow\">
					<input type=hidden name=auth value=\"$auth\">
                                        <b>Edit Administration Options for:<br>
                                        <dd>Table: <font class=rrr>$tbl</font></dd>
                                        <dd>Title: <font class=rrr>$title</font></dd>
                                        <dd>Target Directory: <font class=rrr>$dir</font></dd>
                                        <dd>Security Groups: <font class=rrr>$auth</font></dd>
                                        <dd>Records per Page: <font class=rrr>$maxrow</font></b></dd>
                                        <p>
                                        <table cellpadding=6 cellspacing=0 border=1>
                                        <tr>
                                        <td class=bbb align=left>Column Name</td>
                                        <td class=bbb align=left>Option</td>
                                        <td class=bbb align=left>Enable</td>
                                        </tr>
                                        $tr
                                        <tr>
                                        <td colspan=3 align=center class=bbb>Main Options for the Tool</td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to ADD new rows to this table</td>
                                        <td align=center><input type=checkbox name=enable_add$eadd></td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to UPDATE rows in this table</td>
                                        <td align=center><input type=checkbox name=enable_update$eupd></td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to DELETE rows in this table</td>
                                        <td align=center><input type=checkbox name=enable_delete$edel></td>
                                        </tr>
                                        <tr><td colspan=3 align=right><input type=submit name=submit value='Next'></td></tr>
                                        </table>";
                        } else {
                                $body = "No table $tbl exists or no columns found for this table.";
                        }
                } else {
                        $body = "Cannot find a tablename.";
                }
        } else if($s == 4) {
                # 4. confirm everything
                #print "<pre>";
                #print_r($_POST);
                #print "</pre>";
                if($tbl) {
                        $q = "exec sp_columns $tbl";
                        $qrh = mssql_query($q);
                        if(mssql_num_rows($qrh)) {
                                while($row = mssql_fetch_assoc($qrh)) {
                                        if($row[TYPE_NAME] == 'int identity') {
						if($view[$row[COLUMN_NAME]]) {
							$vw = "<b>Column ENABLED for viewing</b><input type=hidden name=view[{$row[COLUMN_NAME]}] value=1>";
							$talign = textalign($row[COLUMN_NAME],0,$_POST[align][$row[COLUMN_NAME]]);
						} else {
							$vw = "<b>Column DISABLED for viewing</b><input type=hidden name=view[{$row[COLUMN_NAME]}] value=0>";
							$talign = '';
						}
                                                $select = 'N/A, this is the identity key';
                                                $editing = "$vw <input type=hidden name=option[{$row[COLUMN_NAME]}] value=0>
							<input type=hidden name=identity value={$row[COLUMN_NAME]}> $talign";
                                        } else {
                                                unset($select);
                                                if($option[$row[COLUMN_NAME]] == 'text') {
                                                        $param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $param2 = $_POST[param2][$row[COLUMN_NAME]];
                                                        $param3 = $_POST[param3][$row[COLUMN_NAME]];
                                                        $select = "<b>Text Field</b><br>
                                                                Field length: <b>$param1</b> chars<br>
                                                                Max length: <b>$param2</b> max chars<br>
                                                                Default Value: <b>$param3</b>
                                                                <input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=param2[{$row[COLUMN_NAME]}] value=\"$param2\">
                                                                <input type=hidden name=param3[{$row[COLUMN_NAME]}] value=\"$param3\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=text>";
                                                } else if($option[$row[COLUMN_NAME]] == 'textarea') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
							$param2 = $_POST[param2][$row[COLUMN_NAME]];
							$param3 = $_POST[param3][$row[COLUMN_NAME]];
                                                        $select = "<b>Text Area (actual size: $row[PRECISION])</b><br>
                                                                Field length/width: <b>$param1</b><br>
                                                                Field height: <b>$param2</b><br>
                                                                Default Value: <b>$param3</b>
                                                                <input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=param2[{$row[COLUMN_NAME]}] value=\"$param2\">
								<input type=hidden name=param3[{$row[COLUMN_NAME]}] value=\"$param3\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=textarea>";
                                                } else if($option[$row[COLUMN_NAME]] == 'zerone') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $select = "<b>Option select box - 0/1</b><br>
                                                                        Default value: <b>$param1</b><br>
									<input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                        <input type=hidden name=option[{$row[COLUMN_NAME]}] value=zerone>";
                                                } else if($option[$row[COLUMN_NAME]] == 'activeinactive') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $select = "<b>Option select box - Active/Inactive</b><br>
                                                                        Default value: <b>$param1</b><br>
									<input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                        <input type=hidden name=option[{$row[COLUMN_NAME]}] value=activeinactive>";
                                                } else if($option[$row[COLUMN_NAME]] == 'tblcol') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $param2 = $_POST[param2][$row[COLUMN_NAME]];
                                                        $param3 = $_POST[param3][$row[COLUMN_NAME]];
							list($tblx,$column) = explode('.',$param1);
							$q2 = "select $param1 from $tblx order by $param1";
							$qrh2 = mssql_query($q2);
							while($r2 = mssql_fetch_assoc($qrh2)) {
								$vals .= "<li>$r2[$column]</li><br>";
							}
                                                        $select = "<b>Existing CMS Table Column</b><br>
                                                                Table.Column: <b>$param1</b><br>
                                                                Default value: <b>$param2</b> (optional)<br>
                                                                Use Identity Key in Table: <b>$param3</b><br>
								Data found in Table.Column:<br>
								$vals
                                                                <input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=param2[{$row[COLUMN_NAME]}] value=\"$param2\">
                                                                <input type=hidden name=param3[{$row[COLUMN_NAME]}] value=\"$param3\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=tblcol>";
							unset($vals);
                                                } else if($option[$row[COLUMN_NAME]] == 'userdefinable') {
                                                        $param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $param2 = $_POST[param2][$row[COLUMN_NAME]];
							$select = "<b>User definable options</b><br>
								Options: <b>$param1</b><br>
								Default value: <b>$param2</b><br>
                                                                <input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=param2[{$row[COLUMN_NAME]}] value=\"$param2\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=userdefinable>";
                                                } else if($option[$row[COLUMN_NAME]] == 'date') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
							$param2 = $_POST[param2][$row[COLUMN_NAME]];
							$param3 = $_POST[param3][$row[COLUMN_NAME]];
                                                        $select = "<b>Option Calendar Date Popup Selection</b><br>
                                                                Start Date: <b>$param1</b><br> (mm/dd/yyyy format)<br>
                                                                Calendar Start Range before Start Date: <b>$param2</b> (days)<br>
                                                                Calendar End Range after Start Date: <b>$param3</b> (days)<br>
                                                                <input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=param2[{$row[COLUMN_NAME]}] value=\"$param2\">
                                                                <input type=hidden name=param3[{$row[COLUMN_NAME]}] value=\"$param3\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=date>";
                                                } else if($option[$row[COLUMN_NAME]] == 'currentdate') {
                                                        $select = "<b>Hidden, set to default getdate() function.</b><br>
                                                                Field set to current date using the getdate() function.
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=currentdate>";
                                                } else if($option[$row[COLUMN_NAME]] == 'null') {
                                                        $select = "<b>Hidden, set to NULL value.</b><br>
                                                                Field set to NULL value.
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=null>";
                                                } else if($option[$row[COLUMN_NAME]] == 'hidden') {
							$param1 = $_POST[param1][$row[COLUMN_NAME]];
                                                        $select = "<b>Hidden Field</b><br>
                                                                Default Hidden Field value: <b>$param1</b><br>
								<input type=hidden name=param1[{$row[COLUMN_NAME]}] value=\"$param1\">
                                                                <input type=hidden name=option[{$row[COLUMN_NAME]}] value=hidden>";
                                                }
                                                if($view[$row[COLUMN_NAME]]) {
                                                        $editing = "<input type=hidden name=view[{$row[COLUMN_NAME]}] value=1> Column ENABLED for viewing";
							$editing .= textalign($row[COLUMN_NAME],0,$_POST[align][$row[COLUMN_NAME]]);
                                                        if($row[TYPE_NAME] == 'datetime') {
                                                                $editing .= dateviewformat($row[COLUMN_NAME],0,$_POST[dateview][$row[COLUMN_NAME]]);
                                                        }
                                                } else {
                                                        $editing = "<input type=hidden name=view[{$row[COLUMN_NAME]}] value=0> Column DISABLED for viewing<br>";
                                                }
                                                if($_POST[enable][$row[COLUMN_NAME]]) {
                                                        $editing .= "<input type=hidden name=enable[{$row[COLUMN_NAME]}] value=1>Column ENABLED for editing";
                                                } else {
                                                        $editing .= "<input type=hidden name=enable[{$row[COLUMN_NAME]}] value=0>Column DISABLED for editing";
                                                        if(!$select) {
                                                                $select = "<input type=hidden name=option[{$row[COLUMN_NAME]}] value=0>&nbsp;";
                                                        }
                                                }
                                        }
                                        $tr .= "<tr>
                                                <td class=bb align=left>$row[COLUMN_NAME]</td>
                                                <td class=bb align=left>$select</td>
                                                <td class=bb align=left>$editing</td>
                                                </tr>
                                                ";
                                }
                                if($enable_add) {
                                        $eadd = '<input type=hidden name=enable_add value=1>ENABLED';
                                } else {
                                        $eadd = '<input type=hidden name=enable_add value=0>DISABLED';
                                }
                                if($enable_update) {
                                        $eupd = '<input type=hidden name=enable_update value=1>ENABLED';
                                } else {
                                        $eupd = '<input type=hidden name=enable_update value=0>DISABLED';
                                }
                                if($enable_delete) {
                                        $edel = '<input type=hidden name=enable_delete value=1>ENABLED';
                                } else {
                                        $edel = '<input type=hidden name=enable_delete value=0>DISABLED';
                                }
                                $body = "<form method=POST action=$_SERVER[PHP_SELF]?s=5>
                                        <input type=hidden name=tbl value=\"$tbl\">
					<input type=hidden name=title value=\"$title\">
					<input type=hidden name=dir value=\"$dir\">
					<input type=hidden name=maxrow value=\"$maxrow\">
					<input type=hidden name=auth value=\"$auth\">
                                        <b>Edit Administration Options for:<br>
                                        <dd>Table: <font class=rrr>$tbl</font></dd>
                                        <dd>Title: <font class=rrr>$title</font></dd>
                                        <dd>Target Directory: <font class=rrr>$dir</font></dd>
                                        <dd>Security Groups: <font class=rrr>$auth</font></dd>
                                        <dd>Records per Page: <font class=rrr>$maxrow</font></b></dd>
                                        <p>
                                        <table cellpadding=6 cellspacing=0 border=1>
                                        <tr>
                                        <td class=bbb align=left>Column Name</td>
                                        <td class=bbb align=left>Option</td>
                                        <td class=bbb align=left>Enable</td>
                                        </tr>
                                        $tr
                                        <tr>
                                        <td colspan=3 align=center class=bbb>Main Options for the Tool</td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to ADD new rows to this table</td>
                                        <td align=center class=bbb>$eadd</td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to UPDATE rows in this table</td>
                                        <td align=center class=bbb>$eupd</td>
                                        </tr>
                                        <tr>
                                        <td colspan=2 align=left class=bb>Enable Admin tool to DELETE rows in this table</td>
                                        <td align=center class=bbb>$edel</td>
                                        </tr>
                                        <tr><td colspan=3 align=right><input type=submit name=submit value='Next'></td></tr>
                                        </table>";
                        } else {
                                $body = "No table $tbl exists or no columns found for this table.";
                        }
                } else {
                        $body = "Cannot find a tablename.";
                }
	} else if($s == 5) {
		# 5. generate the admin code to manage this table
		#print "<pre>";
		#print_r($_POST);
		#print "</pre>";
		# First figure out the directory and filename
		if($dir) {
			# fix if no forward slash at the front
			if($dir[0] != '/') {
				$dir = '/' . $dir;
			}
			# take care of forward slash at the back as well
			if(substr($dir,-1) == '/') {
				$file = $dir . "{$tbl}_index.phtml";
			} else {
				$file = "$dir/{$tbl}_index.phtml";
			}
		} else {
			$file = "/admin/{$tbl}_index.phtml";
		}
		$filename = $DOCUMENT_ROOT . $file;
		# Generate the main admin page with sorting and page views $tbl_index.phtml, sort columns and view page size 
		$indexcode = gen_index($file,$version);
		if (!$fp = @fopen($filename, 'x')) {
			# if cannot open, delete and try again.
			unlink($filename);
			if (!$fp = fopen($filename, 'x')) {
				print "Cannot open file for writing ($filename) after 2nd try.";
				exit;
			}
		}
		if (!fwrite($fp, $indexcode, strlen($indexcode)+255)) {
                        print "Cannot write to file ($filename)";
                        exit;
		} else {
			# chmod to group writable
			chmod("$filename", 0775);
			$body .= "<b>Successfully created and generated code for <font class=rrr>$file</font></b><br>";
			$s1 = 1;
		}
		fclose($fp);
		if($s1) {
			$body .= "<p>Admin Tool successfully created. You can access this tool at: <a href='$file' target='$tbl' class=bbbz>$file Admin Tool</a>
				<P>HTML Link Code: &lt;a href=&quot;$file&quot; target=&quot;$tbl&quot; class=&quot;bbz&quot;&gt;$tbl Admin Tool&lt;/a&gt;";
		} else {
			$body .= "Failed generated code somewhere.";
		}
	}
        print "<html>
                        <head>
                                <title>PHP Code Generator to create Admin Tool for Table</title>
                                $style
                        </head>
                        <body class=bb>
				<font class=blackbigger>PHP Code Generator to create Admin Tool for Table</font><p>
                                $body
                        </body>
                        </html>";
	
# ----------------------------------------------------------------------------------------------------------------------------
# Functions
# ----------------------------------------------------------------------------------------------------------------------------
function gen_index($file,$version) {
	$post = base64_encode(serialize($_POST));
	$datecode1 = date("Ymd");
	if($_POST[auth]) {
		# add security groups authentication if exists
		$security = "
	require(\$_SERVER[DOCUMENT_ROOT] . '/auth/authenticate.inc');
	\$groups = array($_POST[auth]);
	authenticate(\$groups);
		";
	} else {
		$security = '';
	}
	return <<<END
<?php
	$security
	# -----------------------------------------------------------------------
	# adam $datecode1 - This script is generated by /admin/admingenerator.php
	# admingenerator version: $version
	# Script Title: $_POST[title]
	# -----------------------------------------------------------------------
        require("\$_SERVER[DOCUMENT_ROOT]/lib/dbconnect.inc.php");
        require("\$_SERVER[DOCUMENT_ROOT]/lib/debug.inc.php");
        require("\$_SERVER[DOCUMENT_ROOT]/lib/datetime.inc.php");
        require("\$_SERVER[DOCUMENT_ROOT]/lib/datepicker/datepicker.inc.php");
	\$post = '$post';
	\$config = unserialize(base64_decode(\$post));
        \$style = read_file("\$_SERVER[DOCUMENT_ROOT]/ssi/styles.ssi");
        dbconnect();
	\$updscript = "$_POST[tbl]_index.phtml";
	if(\$config[identity]) {
		\$assumedkeyname = \$config[identity];
	} else {
		\$q = "EXEC sp_pkeys $_POST[tbl]";
		\$qrh = mssql_query(\$q);
		while(\$row = mssql_fetch_assoc(\$qrh)) {
			\$assumedkeyname = \$row[COLUMN_NAME];
		}
	}
	#print "<pre>"; print_r(\$config); print "</pre>";
	# 1. find out which column is enabled and viewable.
        foreach(\$config[view] as \$k => \$v) {
                if(\$v) {
                        \$ok2view[\$k] = \$k;
                        \$title .= "<td class=bbb><nobr>\$k 
					<a href=\$updscript?sort=\$k&dir=asc><img src=/lib/ico/arrow_up_blue.gif border=0></a>
					<a href=\$updscript?sort=\$k&dir=desc><img src=/lib/ico/arrow_down_blue.gif border=0></a>
				</nobr></td>";
                }
        }
	# For Delete, Add and Update selections
        if(\$_GET[a] == 'delete' && \$_GET[edit]) {
                \$q = "SELECT * FROM $_POST[tbl] WHERE \$assumedkeyname = '\$_GET[edit]'";
		\$qrh = mssql_query(\$q);
		\$body .= "<table cellpadding=6 cellspacing=1 border=1><tr><td colspan=2 class=blackbigger align=left>Delete Record</td></tr>";
		while(\$row = mssql_fetch_assoc(\$qrh)) {
			foreach(\$row as \$k => \$v) {
				\$body .= "<tr><td class=bbb>\$k</td><td class=bb>\$v &nbsp;</td></tr>";
			}
		}
		\$body .= "<tr><td colspan=2 class=rrr>Are you sure you want to delete the record above?</td></tr>
			</table>
			<form method=POST action=\$updscript?a=deletex&edit=\$_GET[edit]>
			<input type=hidden name=edit value='\$_GET[edit]'>
                        <p>
                        <input type=submit name=submit value='Yes' class=bbb>
			<input type=submit name=submit value='No' class=bbb>
                        </form>
			";
        	print "<html>
                        <head>
                                <title>$_POST[title]</title>
                                \$style
                        </head>
                        <body class=bb>
                                \$body
                        </body>
                        </html>";
		exit;
        } else if(\$_GET[a] == 'deletex' && \$_GET[edit]) {
		if(\$_POST[submit] == 'Yes') {
			\$q = "DELETE FROM $_POST[tbl] WHERE \$assumedkeyname = '\$_GET[edit]'";
			\$qrh = mssql_query(\$q);
			if(\$qrh) {
				\$body .= "Record #\$_GET[edit] successfully deleted.<p><a href=\$updscript class=bbz>Back to Admin Tool</a>";
			} else {
				\$body .= "Record deletion unsuccessful. Please see the administrator.<p><a href=\$updscript class=bbz>Back to Admin Tool</a>";
			}
                	print "<html>
                        	<head>
                                <title>$_POST[title]</title>
                                \$style
                        	</head>
                        	<body class=bb>
                                \$body
                        	</body>
                        	</html>";
                	exit;
		} else {
			# do not want record to be deleted, do nothing, let it return to main page.
		}
        } else if(\$_GET[a] == 'add') {
                \$q = "exec sp_columns $_POST[tbl]";
                \$qrh = mssql_query(\$q);
                \$body .= "<form name=scr1 method=POST action=\$updscript?a=addx>
			<table cellpadding=6 cellspacing=1 border=1><tr><td colspan=2 class=blackbigger align=left>Add New</td></tr>";
                while(\$row = mssql_fetch_assoc(\$qrh)) {
			if(\$row[TYPE_NAME] == 'int identity') {
                                #\$body .= "<tr><td class=bbb>$row[COLUMN_NAME]</td><td class=bb>Cannot update this column (identity key)</td></tr>";
			} else {
				if(\$config[option][\$row[COLUMN_NAME]] == 'text' && \$config[enable][\$row[COLUMN_NAME]]) {
					\$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
						<td><input type=text size={\$config[param1][\$row[COLUMN_NAME]]} maxlength={\$config[param2][\$row[COLUMN_NAME]]} name=add[{\$row[COLUMN_NAME]}] class=bb value=\"{\$config[param3][\$row[COLUMN_NAME]]}\"></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'textarea' && \$config[enable][\$row[COLUMN_NAME]]) {
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td><textarea name=add[{\$row[COLUMN_NAME]}] class=bb width={\$config[param1][\$row[COLUMN_NAME]]} height={\$config[param2][\$row[COLUMN_NAME]]}>{\$config[param3][\$row[COLUMN_NAME]]}</textarea></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'zerone' && \$config[enable][\$row[COLUMN_NAME]]) {
					if(\$config[param1][\$row[COLUMN_NAME]]) {
						\$opt1 = ' selected';
						\$opt0 = '';
					} else {
						\$opt1 = '';
						\$opt0 = ' selected';
					}
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
						<td><select name=add[{\$row[COLUMN_NAME]}] class=bb>
							<option value=0{\$opt0}>0</option>
							<option value=1{\$opt1}>1</option>
							</select></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'activeinactive' && \$config[enable][\$row[COLUMN_NAME]]) {
                                        if(\$config[param1][\$row[COLUMN_NAME]] == 'Active') {
                                                \$opt1 = ' selected';
                                                \$opt0 = '';
                                        } else {
                                                \$opt1 = '';
                                                \$opt0 = ' selected';
                                        }
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td><select name=add[{\$row[COLUMN_NAME]}] class=bb>
                                                        <option value=Active{\$opt1}>Active</option>
                                                        <option value=Inactive{\$opt0}>Inactive</option>
                                                        </select></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'tblcol' && \$config[enable][\$row[COLUMN_NAME]]) {
					list(\$tblx,\$column) = explode('.',\$config[param1][\$row[COLUMN_NAME]]);
					# if param3 is 1, then find ident key column and return values for those as well
					if(\$config[param3][\$row[COLUMN_NAME]]) {
						\$q2 = "exec sp_columns \$tblx";
						\$qrh2 = mssql_query(\$q2);
						while(\$r2 = mssql_fetch_assoc(\$qrh2)) {
							if(\$r2[TYPE_NAME] == 'int identity') {
								\$col_ident = \$r2[COLUMN_NAME];	
							}
						}
						\$q2 = "select \$col_ident,\$column from \$tblx order by \$column";
					} else {
                                        	\$q2 = "select \$column from \$tblx order by \$column";
					}
                                        \$qrh2 = mssql_query(\$q2);
                                        while(\$r2 = mssql_fetch_assoc(\$qrh2)) {
						if(\$config[param2][\$row[COLUMN_NAME]] == \$r2[\$column]) {
							if(\$config[param3][\$row[COLUMN_NAME]]) {
								\$opt .= "<option value=\"\$r2[\$col_ident]\" selected>\$r2[\$column]</option>";
							} else {
								\$opt .= "<option value=\"\$r2[\$column]\" selected>\$r2[\$column]</option>";
							}
						} else {
							if(\$config[param3][\$row[COLUMN_NAME]]) {
								\$opt .= "<option value=\"\$r2[\$col_ident]\">\$r2[\$column]</option>";
							} else {
								\$opt .= "<option value=\"\$r2[\$column]\">\$r2[\$column]</option>";
							}
						}
					}
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td><select name=add[{\$row[COLUMN_NAME]}] class=bb>
							\$opt
                                                        </select></td></tr>";
					unset(\$opt);
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'userdefinable' && \$config[enable][\$row[COLUMN_NAME]]) {
					\$list = explode(',',\$config[param1][\$row[COLUMN_NAME]]);
					foreach(\$list as \$k) {
						\$k = trim(\$k);
						if(\$config[param2][\$row[COLUMN_NAME]] == \$k) {
							\$opt .= "<option value=\"\$k\" selected>\$k</option>";
						} else {
							\$opt .= "<option value=\"\$k\">\$k</option>";
						}
					}
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td><select name=add[{\$row[COLUMN_NAME]}] class=bb>
							\$opt
                                                        </select></td></tr>";
					unset(\$opt);
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'date' && \$config[enable][\$row[COLUMN_NAME]]) {
					# use the calendar code, allow users to select the date using the calendar code
					if(\$config[param2][\$row[COLUMN_NAME]]) {
        					\$b4 = mktime(0,0,0,date("m"),date("d")-\$config[param2][\$row[COLUMN_NAME]],date("Y"));
        					\$b4_tm = date("m/d/Y",\$b4);
					} else {
						# if not given, default set to 90 days before
                                                \$b4 = mktime(0,0,0,date("m"),date("d")-90,date("Y"));
                                                \$b4_tm = date("m/d/Y",\$b4);
					}
                                        if(\$config[param3][\$row[COLUMN_NAME]]) {
        					\$af = mktime(0,0,0,date("m"),date("d")+\$config[param3][\$row[COLUMN_NAME]],date("Y"));
        					\$af_tm = date("m/d/Y",\$af);
                                        } else {
						# if not given, default set to 90 days after
						\$af = mktime(0,0,0,date("m"),date("d")+90,date("Y"));
						\$af_tm = date("m/d/Y",\$af);
					}
					\$dp = datepicker_noinitdate(\$b4_tm,\$af_tm);
					\$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
						<td class=bb>\$dp
						(MM/DD/YYYY)<br>
                        			<INPUT NAME=add_{\$row[COLUMN_NAME]} VALUE=\"{\$config[param1][\$row[COLUMN_NAME]]}\" MAXLENGTH=12 SIZE=9 class=date-pick>&nbsp;
						</td></tr>
						";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'currentdate' && \$config[enable][\$row[COLUMN_NAME]]) {
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td class=bb>Set to current date and time. <input type=hidden name=add[{\$row[COLUMN_NAME]}] value='getdate()'></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'null' && \$config[enable][\$row[COLUMN_NAME]]) {
					\$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
						<td class=bb>Set to NULL. <input type=hidden name=add[{\$row[COLUMN_NAME]}] value=NULL></td></tr>";
				} else if(\$config[option][\$row[COLUMN_NAME]] == 'hidden' && \$config[enable][\$row[COLUMN_NAME]]) {
                                        \$body .= "<tr><td class=bbb>\$row[COLUMN_NAME]</td>
                                                <td class=bb>Set to {\$config[param1][\$row[COLUMN_NAME]]} 
						<input type=hidden name=add[{\$row[COLUMN_NAME]}] value=\"{\$config[param1][\$row[COLUMN_NAME]]}\"></td></tr>";
				}
                        }
                }
                \$body .= "</table>
                        <p>
                        <input type=submit name=submit value='Add' class=bbb>
                        <input type=reset name=reset value='Reset' class=bbb> <a href=\$updscript class=bbbz>Back to Admin Tool</a>
                        </form>
                        ";
		\$calcode = read_file("\$_SERVER[DOCUMENT_ROOT]/ssi/datepicker.ssi");
                print "<html>
                        <head>
                                <title>$_POST[title]</title>
                                \$style
				\$calcode
                        </head>
                        <body class=bb>
                                \$body
                        </body>
                        </html>";
                exit;
        } else if(\$_GET[a] == 'addx') {
		\$date_exception = 0;
		foreach(\$_POST as \$k => \$v) {
			if(preg_match('/add_/',\$k)) {
				\$date_exception = 1;
				break;
			}
		}
		if(\$_POST[add] || \$date_exception) {
			if(\$_POST[add]) {
				foreach(\$_POST[add] as \$k => \$v) {
					\$q_cols[] = \$k;
					# check for getdate() and NULL that does not require single quotes around them
					if(\$v == 'NULL' || \$v == 'getdate()') {
						\$q_vals[] = "\$v";
					} else {
						\$v = str_replace("'","''",\$v);
						\$q_vals[] = "'\$v'";
					}
				}
			}
			if(\$date_exception) {
				# special handling for date
				# check for dates insertion - we have to change the date formatting
				foreach(\$_POST as \$k => \$v) {
					if(preg_match('/add_/',\$k)) {
						list(\$junk,\$column) = explode('_',\$k);
						\$q_cols[] = \$column;
						\$v = date("Y-m-d",strtotime(\$v));
						\$q_vals[] = "'\$v'";
					}
				}
			}
			\$q_cols = implode(',',\$q_cols);
			\$q_vals = implode(',',\$q_vals);
			\$q = "INSERT INTO $_POST[tbl] (\$q_cols) VALUES (\$q_vals)";
			#print "\$q";
			\$qrh = mssql_query(\$q);
			if(\$qrh) {
				\$body .= "Record successfully added.<p><a href=\$updscript class=bbz>Back to Admin Tool</a>";
			} else {
				\$body .= "Record addition unsuccessful. Please see the administrator.<p><a href=\$updscript class=bbz>Back to Admin Tool</a>";
			}
                        print "<html>
                                <head>
                                <title>$_POST[title]</title>
                                \$style
                                </head>
                                <body class=bb>
                                \$body
                                </body>
                                </html>";
                        exit;
		}
        } else if(\$_GET[a] == 'update') {
                \$q = "SELECT * FROM $_POST[tbl] WHERE \$assumedkeyname = '\$_GET[edit]'";
                \$qrh = mssql_query(\$q);
                \$body .= "<table cellpadding=6 cellspacing=1 border=1><tr><td colspan=2 class=blackbigger align=left>Update Record</td></tr>";
                while(\$row = mssql_fetch_assoc(\$qrh)) {
                        foreach(\$row as \$k => \$v) {
				if(\$config[enable][\$k]) {
					if(\$config[view][\$k]) {
                                		\$body .= "<tr><td class=bbb>\$k</td><td class=bb id=\"\$_GET[edit]_\$k\"><a href='javascript:AjaxLoad(\"\$_GET[edit]_\$k\", \"$file?a=ajaxedit&id=\$_GET[edit]&column=\$k&table=$_POST[tbl]\");' class=bbz>\$v</a>&nbsp;[<a href='javascript:AjaxLoad(\"\$_GET[edit]_\$k\", \"$file?a=ajaxedit&id=\$_GET[edit]&column=\$k&table=$_POST[tbl]\");' class=bbz>+</a>]</td></tr>";
					} else {
						\$body .= '';
					}
				} else {
					if(\$config[view][\$k]) {
                                		\$body .= "<tr><td class=bbb>\$k</td><td class=bb>\$v &nbsp;</td></tr>";
					} else {
						\$body .= '';
					}
				}
                        }
                }
                \$body .= "</table>
                        <p>
                        <a href=\$updscript class=bbbz>Back to Admin Tool</a>
                        ";
                print "<html>
                        <head>
                                <title>$_POST[title]</title>
                                \$style
				<script language='javascript' type='text/javascript' src='/lib/js/ajax.js'></script>
                        </head>
                        <body class=bb>
                                \$body
                        </body>
                        </html>";
                exit;
        } else if(\$_GET[a] == 'ajaxedit') {
                \$q = "SELECT \$_GET[column] FROM $_POST[tbl] WHERE \$assumedkeyname = '\$_GET[id]'";
                \$qrh = mssql_query(\$q);
                \$body = "<form name=scr1 method=POST action=$file?a=ajaxupdate>";
                while(\$row = mssql_fetch_assoc(\$qrh)) {
                        foreach(\$row as \$k => \$v) {
				if(\$config[view][\$k]) {
					if(\$config[enable][\$k]) {
						if(\$config[option][\$k] == 'text') {
                                			\$body .= "<input type=text size={\$config[param1][\$k]} maxlength={\$config[param2][\$k]} name=val2update class=bb value=\"\$v\">";
						} else if (\$config[option][\$k] == 'textarea') {
							\$body .= "<textarea name=val2update class=bb width={\$config[param1][\$k]} height={\$config[param2][\$k]}>{\$config[param3][\$k]}</textarea>";
						} else if (\$config[option][\$k] == 'zerone') {
                                        		if(\$config[param1][\$k]) {
                                                		\$opt1 = ' selected';
                                                		\$opt0 = '';
                                        		} else {
                                                		\$opt1 = '';
                                                		\$opt0 = ' selected';
                                        		}
							\$body .= "<select name=val2update class=bb>
                                                        	<option value=0{\$opt0}>0</option>
                                                        	<option value=1{\$opt1}>1</option>
                                                        	</select>";
						} else if (\$config[option][\$k] == 'activeinactive') {
                                        		if(\$config[param1][\$k] == 'Active') {
                                                		\$opt1 = ' selected';
                                                		\$opt0 = '';
                                        		} else {
                                                		\$opt1 = '';
                                                		\$opt0 = ' selected';
                                        		}
							\$body .= "<select name=val2update class=bb>
                                                        	<option value=Active{\$opt1}>Active</option>
                                                        	<option value=Inactive{\$opt0}>Inactive</option>
                                                        	</select>";
						} else if (\$config[option][\$k] == 'tblcol') {
                                        		list(\$tblx,\$column) = explode('.',\$config[param1][\$k]);
                                        		# if param3 is 1, then find ident key column and return values for those as well
                                        		if(\$config[param3][\$k]) {
                                                		\$q2 = "exec sp_columns \$tblx";
                                                		\$qrh2 = mssql_query(\$q2);
                                                		while(\$r2 = mssql_fetch_assoc(\$qrh2)) {
                                                        		if(\$r2[TYPE_NAME] == 'int identity') {
                                                                		\$col_ident = \$r2[COLUMN_NAME];
                                                        		}
                                                		}
                                                		\$q2 = "select \$col_ident,\$column from \$tblx order by \$column";
                                        		} else {
                                                		\$q2 = "select \$column from \$tblx order by \$column";
                                        		}
                                        		\$qrh2 = mssql_query(\$q2);
                                        		while(\$r2 = mssql_fetch_assoc(\$qrh2)) {
                                                		if(\$v == \$r2[\$column]) {
                                                        		if(\$config[param3][\$k]) {
                                                                		\$opt .= "<option value=\"\$r2[\$col_ident]\" selected>\$r2[\$column]</option>";
                                                        		} else {
                                                                		\$opt .= "<option value=\"\$r2[\$column]\" selected>\$r2[\$column]</option>";
                                                        		}
                                                		} else {
                                                        		if(\$config[param3][\$k]) {
                                                                		\$opt .= "<option value=\"\$r2[\$col_ident]\">\$r2[\$column]</option>";
                                                        		} else {
                                                                		\$opt .= "<option value=\"\$r2[\$column]\">\$r2[\$column]</option>";
                                                        		}
                                                		}
                                        		}
                                        		\$body .= "<select name=val2update class=bb>\$opt</select>";
							unset(\$opt);
						} else if (\$config[option][\$k] == 'userdefinable') {
                                        		\$list = explode(',',\$config[param1][\$k]);
                                        		foreach(\$list as \$z) {
                                                		\$z = trim(\$z);
                                                		if(\$v == \$z) {
                                                       	 		\$opt .= "<option value=\"\$z\" selected>\$z</option>";
                                                		} else {
                                                        		\$opt .= "<option value=\"\$z\">\$z</option>";
                                                		}
                                        		}
                                        		\$body .= "<select name=val2update class=bb>\$opt</select>";
							unset(\$opt);
						} else if (\$config[option][\$k] == 'date') {
							# assume value is datetime
							\$v = date("m/d/Y",strtotime(\$v));
                                        		\$body .= "(MM/DD/YYYY)<br><INPUT NAME=val2update VALUE=\"{\$v}\" MAXLENGTH=12 SIZE=9 class=date-pick>&nbsp;";
						} else if (\$config[option][\$k] == 'currentdate') {
							\$body .= "Set to current date and time. (getdate() function) <input type=hidden name=val2update value='getdate()'>";
						} else if (\$config[option][\$k] == 'null') {
							\$body .= "Set to NULL. <input type=hidden name=val2update value=NULL>";
						} else if (\$config[option][\$k] == 'hidden') {
							\$body .= "Set to {\$config[param1][\$k]}. 
                                                		<input type=hidden name=val2update value=\"{\$config[param1][\$k]}\">";
						}
						\$body .= "<input type=hidden name=id value='\$_GET[id]'><input type=hidden name=col2update value='\$k'>
							<input type=submit value=Save name=submit class=bb><input type=submit value=Cancel name=submit class=bb>";
					} else {
                                		\$body .= "\$v";
					}
				} else {
					\$body .= '';
				}
                        }
                }
                \$body .= "</form>";
                print "\$body";
		exit;
        } else if(\$_GET[a] == 'ajaxupdate') {
		# here is where we actually run the sql update command
		if(\$_POST[id] && \$_POST[col2update]) {
			\$body .= "<table cellpadding=6 cellspacing=1 border=1><tr><td colspan=2 class=blackbigger align=left>Update Record</td></tr>";
			if(\$_POST[submit] == 'Save') {
				\$_POST[val2update] = str_replace("'","''",\$_POST[val2update]);
				if(\$_POST[val2update] == 'getdate()') {
					\$q = "UPDATE $_POST[tbl] SET \$_POST[col2update]=getdate() WHERE \$assumedkeyname = '\$_POST[id]'";
				} else if(\$_POST[val2update] == 'NULL') {
					\$q = "UPDATE $_POST[tbl] SET \$_POST[col2update]=NULL WHERE \$assumedkeyname = '\$_POST[id]'";
				} else if(\$config[option][\$col2update] == 'date') {
					\$valdate = date("Y-m-d",strtotime(\$_POST[val2update]));
					\$q = "UPDATE $_POST[tbl] SET \$_POST[col2update]='\$valdate' WHERE \$assumedkeyname = '\$_POST[id]'";
				} else {
					\$q = "UPDATE $_POST[tbl] SET \$_POST[col2update]='\$_POST[val2update]' WHERE \$assumedkeyname = '\$_POST[id]'";
				}
				\$qrh = mssql_query(\$q);
			} else {
				# Cancel button pressed
				\$qrh = 1;
			}
			if(\$qrh) {
				\$q = "SELECT * FROM $_POST[tbl] WHERE \$assumedkeyname = '\$_POST[id]'";
				\$qrh = mssql_query(\$q);
				while(\$row = mssql_fetch_assoc(\$qrh)) {
					foreach(\$row as \$k => \$v) {
						if(\$config[enable][\$k]) {
                                        		if(\$config[view][\$k]) {
                                                		\$body .= "<tr><td class=bbb>\$k</td><td class=bb id=\"\$_POST[id]_\$k\"><a href='javascript:AjaxLoad(\"\$_POST[id]_\$k\", \"$file?a=ajaxedit&id=\$_POST[id]&column=\$k&table=$_POST[tbl]\");' class=bbz>\$v</a>&nbsp;[<a href='javascript:AjaxLoad(\"\$_POST[id]_\$k\", \"$file?a=ajaxedit&id=\$_POST[id]&column=\$k&table=$_POST[tbl]\");' class=bbz>+</a>]</td></tr>";
                                        		} else {
                                                		\$body .= '';
                                        		}
                                		} else {
                                        		if(\$config[view][\$k]) {
                                                		\$body .= "<tr><td class=bbb>\$k</td><td class=bb>\$v &nbsp;</td></tr>";
                                        		} else {
                                                		\$body .= '';
                                        		}
						}
					}
				}
			} else {
				\$body .= "Error occurred during updating. Please see administrator.";
			}
		}
                \$body .= "</table>
                        <p>
                        <a href=\$updscript class=bbbz>Back to Admin Tool</a>
                        ";
                print "<html>
                        <head>
                                <title>$_POST[title]</title>
                                \$style
                                <script language='javascript' type='text/javascript' src='/lib/js/ajax.js'></script>
                        </head>
                        <body class=bb>
                                \$body
                        </body>
                        </html>";
		exit;
        }
	# ------------------------------------------------------------------------------------------------------------------
	# THIS IS THE MAIN VIEW OF THE TOOL
	# ------------------------------------------------------------------------------------------------------------------
        if(\$config[enable_update]) {
		\$title .= "<td class=bbb>Edit</td>";
	}
        if(\$config[enable_delete]) {
                \$title .= "<td class=bbb>Delete</td>";
        }
        if(\$title) {
                \$title = "<tr>" . \$title . "</tr>";
        } else {
                \$title = "<tr><td class=bbb>$_POST[tbl] Admin Tool</td></tr>";
        }
	# xxx
	#print "<pre>"; print_r(\$ok2view); print_r(\$config); print "</pre>";
	if(\$_GET[sort] && \$_GET[dir]) {
		\$q_sort = " order by \$_GET[sort] \$_GET[dir]";
	} else {
		\$q_sort = " order by \$assumedkeyname ";
	}
	if(\$_GET[startrow]) {
		\$prevrow = \$_GET[startrow] - \$config[maxrow];
		\$navig_first = "<a href=\$updscript?sort=\$_GET[sort]&dir=\$_GET[dir]&startrow=1 class=bbz>First</a>";
		if(\$startrow == 1) {
			\$navig_prev = 'Previous';
		} else {
			\$navig_prev =  "<a href=\$updscript?sort=\$_GET[sort]&dir=\$_GET[dir]&startrow=\$prevrow class=bbz>Previous</a>";
		}
	} else {
		\$startrow = 1;
		\$navig_first = 'First';
		\$navig_prev = 'Previous';
	}
	\$endrow = (\$startrow + \$config[maxrow]) - 1;
	\$nextrow = \$endrow + 1;
	\$navig_next = "<a href=\$updscript?sort=\$_GET[sort]&dir=\$_GET[dir]&startrow=\$nextrow class=bbz>Next</a>";
	# get descriptions from tables where tblcol is set
	if(\$config[option]) {
		foreach(\$config[option] as \$k => \$v) {
			if(\$v == 'tblcol') {
                                list(\$tblx,\$column) = explode('.',\$config[param1][\$k]);
				# first find the identity key of the table
                        	\$q = "EXEC sp_pkeys \$tblx";
                        	\$qrh = mssql_query(\$q);
                        	if(mssql_num_rows(\$qrh)) {
                                	while(\$row = mssql_fetch_assoc(\$qrh)) {
						\$id_key_col = \$row[COLUMN_NAME];
					}
				}
				# then dump the key pair values into an array named after the column
                                \$q = "select \$id_key_col,\$column from \$tblx order by \$column";
                                \$qrh = mssql_query(\$q);
                                while(\$r = mssql_fetch_assoc(\$qrh)) {
					\$id_key = \$r[\$id_key_col];
					\$config[\$k][\$id_key] = \$r[\$column];
                                }
			}
		}
	}
	# data paging using the ROW_NUMBER and OVER function - only specific to SQL Server 2005
	\$q = "WITH tbl1 AS (SELECT ROW_NUMBER() OVER (\$q_sort) AS rownum,* FROM $_POST[tbl]) SELECT * FROM tbl1 WHERE rownum >= \$startrow AND rownum <= \$endrow";
	\$qrh = @mssql_query(\$q);
	#print "\$q";
	if(mssql_num_rows(\$qrh)) {
		while(\$row = mssql_fetch_array(\$qrh)) {
			# assume identity key is always the first column!!! (after the rownum column)
			if(\$row[\$assumedkeyname]) {
				\$id = \$row[\$assumedkeyname];
			} else {
				# oh no! identity key is not in the first column...quickly find it!
				foreach(\$row as \$k => \$v) {
					if(\$k == \$assumedkeyname) {
						\$id = \$k;
					}
				}
				if(!\$id) {
					die("Cannot find the identity key!");
				}
			}
			\$body .= "<tr>";
			if(\$ok2view) {
				foreach(\$ok2view as \$k => \$v) {
					if(\$row[\$k]) {
						# view is enabled
						# format datetime view if any
						if(\$config[dateview][\$k]) {
							\$row[\$k] = formatdateview(\$row[\$k],\$config[dateview][\$k]);
						}
						# format column with description from another table
						if(\$config[option][\$k] == 'tblcol') {
							\$descx = \$config[\$k][\$row[\$k]];
							\$row[\$k] = "\$descx (\$row[\$k])";
						}
						# align text
                                                if(\$config[align][\$k]) {
                                                        \$body .= "<td class=bb align={\$config[align][\$k]}>\$row[\$k]</td>";
                                                } else {
                                                        \$body .= "<td class=bb>\$row[\$k]</td>";
                                                }
					} else {
						\$body .= "<td class=bb>&nbsp;</td>";
					}
				}
			}
			#if(\$config[enable][\$k]) {
				if(\$config[enable_update]) {
					\$body .= "<td class=bb align=center><a href=\$updscript?a=update&edit=\$id class=bbz>Edit</a></td>";
				}
                                if(\$config[enable_delete]) {
                                        \$body .= "<td class=bb align=center><a href=\$updscript?a=delete&edit=\$id class=bbz>X</a></td>";
                                }
			#}
			\$body .= "</tr>\\n";
		}
		\$body = "<table cellpadding=5 cellspacing=1 border=1>" . \$title . \$body . "</table>";
		\$q = "select count(*) as total from $_POST[tbl]";
		\$qrh = mssql_query(\$q);
		\$row = mssql_fetch_assoc(\$qrh);
		\$totalrows = \$row[total];
		\$lastrow = floor(\$totalrows / \$config[maxrow]) * \$config[maxrow];
		if((\$totalrows - \$config[maxrow]) <= \$startrow) {
			# reset endrow number since its on the last page
			\$endrow = \$totalrows;
			\$navig_next = 'Next';
		}
	} else {
		\$body = "No rows found.";
		\$totalrows = 0;
		\$lastrow = 0;
	}
	if(\$config[enable_add]) {
		\$addscript = "$_POST[tbl]_index.phtml?a=add";
		\$navig_add = "<td bgcolor=#ffcc99 align=center><a href=\$addscript class=bbbz>Add New</a></td>";
	} else {
		\$navig_add = '';
	}
	\$bgc1 = '#e0e0e0';
        \$navigation = "<table cellpadding=3 cellspacing=1 border=0>
                <tr>
			\$navig_add
                        <td align=left bgcolor=\$bgc1 class=bb>\$navig_first</td>
                        <td align=left bgcolor=\$bgc1 class=bb>\$navig_prev</td>
                        <td align=center class=bb bgcolor=\$bgc1>Currently viewing record \$startrow .. \$endrow out of \$totalrows</td>
                        <td align=right bgcolor=\$bgc1 class=bb>\$navig_next</td>
                        <td align=right bgcolor=\$bgc1><a href=\$updscript?sort=\$_GET[sort]&dir=\$_GET[dir]&startrow=\$lastrow class=bbz>Last</a></td>
                </tr></table>";
	print "<html>
                        <head>
                                <title>$_POST[title]</title>
                                \$style
				<script language='javascript' type='text/javascript' src='/lib/js/ajax.js'></script>
                        </head>
                        <body class=bb>
				<table cellpadding=0 cellspacing=0 border=0>
				<tr><td class=bbb align=left>$_POST[title]</td></tr>
				<tr><td align=left>
					\$navigation
				</td></tr>
				<tr><td align=left>
                                	\$body
				</td></tr>
                                <tr><td align=left>
					\$navigation
				</td></tr>
				</table>
                        </body>
                        </html>";
?>
END;
}
function textalign($x,$editable,$value) {
	if($editable) {
		return "<br>Text alignment: <select name=align[$x] class=bb>
			<option value=left>Left</option>
                	<option value=center>Center</option>
                	<option value=right>Right</option>
                	</select>
			<br>";
	} else {
        	return "<br>Text alignment: $value
                	<input type=hidden name='align[$x]' value=$value>
			<br>";
	}
}
function dateviewformat($x,$editable,$value) {
        if($editable) {
                return "<br>Date view format: <select name=dateview[$x] class=bb>
                        <option value=z>Default (ex: Jun 19 2008 12:00AM)</option>
                        <option value=a>MM/DD/YYYY HH:MM (ex: 06/19/2008 12:49PM)</option>
                        <option value=b>MM/DD/YYYY</option>
                        <option value=c>MM/DD/YY</option>
                        <option value=d>MM/DD</option>
                        <option value=e>MM-DD-YYYY</option>
                        <option value=f>DD-MM-YYYY</option>
                        <option value=g>YYYY-MM-DD</option>
                        <option value=h>Month date, Year (ex: Jun 21, 2008)</option>
                        <option value=i>Month date (ex: Jun 21)</option>
                        </select>
                        <br>";
        } else {
		$dateviewformat = array(z => 'Default (ex: Jun 19 2008 12:00AM)',a => 'MM/DD/YYYY HH:MM (ex: 06/19/2008 12:49PM)',b => 'MM/DD/YYYY', c => 'MM/DD/YY', d => 'MM/DD',e => 'MM-DD-YYYY', f => 'DD-MM-YYYY', g => 'YYYY-MM-DD', h => 'Month date, Year (ex: Jun 21, 2008)', i => 'Month date (ex: Jun 21)');
                return "<br>Date view format: $dateviewformat[$value]
                        <input type=hidden name='dateview[$x]' value=$value>
                        <br>";
        }
}
?>