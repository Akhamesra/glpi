<?php
/*
 
  ----------------------------------------------------------------------
GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2004 by the INDEPNET Development Team.
 
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------
 LICENSE

This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
 
// Based on:
// IRMA, Information Resource-Management and Administration
// Christian Bauer 
// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

include ("_relpos.php");
// FUNCTIONS Computers

function titleComputers(){
              //titre
              
        GLOBAL  $lang,$HTMLRel;

         echo "<div align='center'><table border='0'><tr><td>";
         echo "<img src=\"".$HTMLRel."pics/computer.png\" alt='".$lang["computers"][0]."' title='".$lang["computers"][0]."'></td><td><a  class='icon_consol' href=\"computers-add-select.php\"><b>".$lang["computers"][0]."</b></a>";
         echo "</td></tr></table></div>";

}



function searchFormComputers($field="",$phrasetype= "",$contains="",$sort= "") {
	// Print Search Form
	
	GLOBAL $cfg_install, $cfg_layout, $layout, $lang;

	
	$option["comp.ID"]				= $lang["computers"][31];
	$option["comp.name"]				= $lang["computers"][7];
	$option["glpi_dropdown_locations.name"]			= $lang["computers"][10];
	$option["glpi_type_computers.name"]				= $lang["computers"][8];
	$option["glpi_dropdown_os.name"]				= $lang["computers"][9];
	$option["comp.osver"]			= $lang["computers"][20];
	$option["comp.processor"]			= $lang["computers"][21];
	$option["comp.processor_speed"]		= $lang["computers"][22];
	$option["comp.serial"]			= $lang["computers"][17];
	$option["comp.otherserial"]			= $lang["computers"][18];
	$option["glpi_dropdown.ram.name"]			= $lang["computers"][23];
	$option["comp.ram"]				= $lang["computers"][24];
	$option["glpi_dropdown_network.name"]			= $lang["computers"][26];
	$option["comp.hdspace"]			= $lang["computers"][25];
	$option["glpi_dropdown_sndcard.name"]			= $lang["computers"][33];
	$option["glpi_dropdown_gfxcard.name"]			= $lang["computers"][34];
	$option["glpi_dropdown_moboard.name"]			= $lang["computers"][35];
	$option["glpi_dropdown_hdtype.name"]			= $lang["computers"][36];
	$option["comp.comments"]			= $lang["computers"][19];
	$option["comp.contact"]			= $lang["computers"][16];
	$option["comp.contact_num"]		        = $lang["computers"][15];
	$option["comp.date_mod"]			= $lang["computers"][11];
	$option["glpi_networking_ports.ifaddr"] = $lang["networking"][14];
	$option["glpi_networking_ports.ifmac"] = $lang["networking"][15];
	$option["glpi_dropdown_netpoint.name"]			= $lang["networking"][51];
	$option["glpi_enterprises.name"]			= $lang["common"][5];
	
	echo "<form method=get action=\"".$cfg_install["root"]."/computers/computers-search.php\">";
	echo "<div align='center'><table border='0' width='750' class='tab_cadre'>";
	echo "<tr><th colspan='2'><b>".$lang["search"][0].":</b></th></tr>";
	echo "<tr class='tab_bg_1'>";
	echo "<td align='center'>";
	echo "<select name=\"field\" size='1'>";
        echo "<option value='all' ";
	if($field == "all") echo "selected";
	echo ">".$lang["search"][7]."</option>";
        reset($option);
	foreach ($option as $key => $val) {
		echo "<option value=\"".$key."\""; 
		if($key == $field) echo "selected";
		echo ">". $val ."</option>\n";
	}
	echo "</select>&nbsp;";
	echo $lang["search"][1];
	echo "&nbsp;<select name='phrasetype' size='1' >";
	echo "<option value='contains'";
	if($phrasetype == "contains") echo "selected";
	echo ">".$lang["search"][2]."</option>";
	echo "<option value='exact'";
	if($phrasetype == "exact") echo "selected";
	echo ">".$lang["search"][3]."</option>";
	echo "</select>";
	echo "<input type='text' size='15' name=\"contains\" value=\"". $contains ."\" />";
	echo "&nbsp;";
	echo $lang["search"][4];
	echo "&nbsp;<select name='sort' size='1'>";
	reset($option);
	foreach ($option as $key => $val) {
		echo "<option value=\"".$key."\"";
		if($key == $sort) echo "selected";
		echo ">".$val."</option>\n";
	}
	echo "</select> ";
	echo "</td><td width='80' align='center' class='tab_bg_2'>";
	echo "<input type='submit' value=\"".$lang["buttons"][0]."\" class='submit' />";
	echo "</td></tr></table></div></form>";


}

function IsDropdown($field) {
	$dropdown = array("hdtype","sndcard","moboard","gfxcard","network","processor","os");
	if(in_array($field,$dropdown)) {
		return true;
	}
	else  {
		return false;
	}
}

function showComputerList($target,$username,$field,$phrasetype,$contains,$sort,$order,$start) {


	$db = new DB;
	// Lists Computers

	GLOBAL $cfg_install, $cfg_layout, $cfg_features, $lang,$HTMLRel;


	// Build query
	if($field == "all") {
		$where = " (";
		$fields = $db->list_fields("glpi_computers");
		$columns = $db->num_fields($fields);
		
		for ($i = 0; $i < $columns; $i++) {
			if($i != 0) {
				$where .= " OR ";
			}
			$coco = mysql_field_name($fields, $i);
			if(IsDropdown($coco)) {
				$where .= " glpi_dropdown_". $coco .".name LIKE '%".$contains."%'";
			}
			elseif($coco == "ramtype") {
				$where .= " glpi_dropdown_ram.name LIKE '%".$contains."%'";
			}
			elseif($coco == "location") {
				$where .= " glpi_dropdown_locations.name LIKE '%".$contains."%'";
			}
			elseif($coco == "type") {
				$where .= " glpi_type_computers.name LIKE '%".$contains."%'";
			}
			else {
   				$where .= "comp.".$coco . " LIKE '%".$contains."%'";
			}
		}
		$where .= " OR glpi_networking_ports.ifaddr LIKE '%".$contains."%'";
		$where .= " OR glpi_networking_ports.ifmac LIKE '%".$contains."%'";
		$where .= " OR glpi_dropdown_netpoint.name LIKE '%".$contains."%'";
		$where.=" OR glpi_enterprises.name LIKE '%".$contains."%'";
		$where .= ")";
	}
	else {
		if ($phrasetype == "contains") {
			$where = "($field LIKE '%".$contains."%')";
		}
		else {
			$where = "($field LIKE '".$contains."')";
		}
	}
	if (!$start) {
		$start = 0;
	}
	if (!$order) {
		$order = "ASC";
	}
	$query = "select DISTINCT comp.ID from glpi_computers as comp LEFT JOIN glpi_dropdown_locations on comp.location=glpi_dropdown_locations.ID ";
	$query .= "LEFT JOIN glpi_dropdown_os on comp.os=glpi_dropdown_os.ID LEFT JOIN glpi_type_computers on comp.type = glpi_type_computers.ID ";
	$query .= "LEFT JOIN glpi_dropdown_hdtype on comp.hdtype = glpi_dropdown_hdtype.ID LEFT JOIN glpi_dropdown_processor on comp.processor = glpi_dropdown_processor.ID ";
	$query .= "LEFT JOIN glpi_dropdown_ram on comp.ramtype = glpi_dropdown_ram.ID LEFT JOIN glpi_dropdown_network on comp.network = glpi_dropdown_network.ID ";
	$query .= "LEFT JOIN glpi_dropdown_gfxcard on comp.gfxcard = glpi_dropdown_gfxcard.ID LEFT JOIN glpi_dropdown_moboard on comp.moboard = glpi_dropdown_moboard.ID ";
	$query .= "LEFT JOIN glpi_dropdown_sndcard on comp.sndcard = glpi_dropdown_sndcard.ID ";
	$query .= "LEFT JOIN glpi_networking_ports on (comp.ID = glpi_networking_ports.on_device AND  glpi_networking_ports.device_type='1')";
	$query .= "LEFT JOIN glpi_dropdown_netpoint on (glpi_dropdown_netpoint.ID = glpi_networking_ports.netpoint)";
	$query.= " LEFT JOIN glpi_enterprises ON (glpi_enterprises.ID = comp.FK_glpi_enterprise ) ";
	$query .= " where $where ORDER BY $sort $order";
	//$query = "SELECT * FROM glpi_computers WHERE $where ORDER BY $sort $order";
//echo $query;
	// Get it from database	
	if ($result = $db->query($query)) {
		$numrows= $db->numrows($result);

		// Limit the result, if no limit applies, use prior result
		if ($numrows>$cfg_features["list_limit"]) {
			$query_limit = $query. " LIMIT $start,".$cfg_features["list_limit"]." ";
			$result_limit = $db->query($query_limit);
			$numrows_limit = $db->numrows($result_limit);
		} else {
			$numrows_limit = $numrows;
			$result_limit = $result;
		}
		
		if ($numrows_limit>0) {
			// Produce headline
			echo "<div align='center'><table border='0' class='tab_cadre'><tr>";

			// Name
			echo "<th>";
			if ($sort=="comp.name") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=comp.name&order=ASC&start=$start\">";
			echo $lang["computers"][7]."</a></th>";
		
			// Manufacturer		
			echo "<th>";
			if ($sort=="glpi_enterprises.name") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=glpi_enterprises.name&order=ASC&start=$start\">";
			echo $lang["common"][5]."</a></th>";
			
		        // Serial
			echo "<th>";
			if ($sort=="comp.serial") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=comp.serial&order=ASC&start=$start\">";
			echo $lang["computers"][6]."</a></th>";
		

			// Type
			echo "<th>";
			if ($sort=="glpi_type_computers.name") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=glpi_type_computers.name&order=ASC&start=$start\">";
			echo $lang["computers"][8]."</a></th>";

			// OS
			echo "<th>";
			if ($sort=="glpi_dropdown_os.name") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=glpi_dropdown_os.name&order=ASC&start=$start\">";
			echo $lang["computers"][9]."</a></th>";

			// Location			
			echo "<th>";
			if ($sort=="glpi_dropdown_locations.name") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=glpi_dropdown_locations.name&order=ASC&start=$start\">";
			echo $lang["computers"][10]."</a></th>";

			// Last modified		
			echo "<th>";
			if ($sort=="date_mod") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=date_mod&order=DESC&start=$start\">";
			echo $lang["computers"][11]."</a></th>";

			// Contact person
			echo "<th>";
			if ($sort=="contact") {
				echo "<img src=\"".$HTMLRel."pics/puce-down.gif\" alt='' title=''>";
			}
			echo "<a href=\"$target?field=$field&phrasetype=$phrasetype&contains=$contains&sort=contact&order=ASC&start=$start\">";
			echo $lang["computers"][16]."</a></th>";

			echo "</tr>";

			for ($i=0; $i < $numrows_limit; $i++) {
				$ID = $db->result($result_limit, $i, "ID");
				$comp = new Computer;
				$comp->getfromDB($ID,0);
				echo "<tr class='tab_bg_2'>";
				echo "<td><b>";
				echo "<a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?ID=$ID\">";
				echo $comp->fields["name"]." (".$comp->fields["ID"].")";
				echo "</a></b></td>";
				echo "<td>". getDropdownName("glpi_enterprises",$comp->fields["FK_glpi_enterprise"]) ."</td>";
				echo "<td>".$comp->fields["serial"]."</td>";
                                echo "<td>". getDropdownName("glpi_type_computers",$comp->fields["type"]) ."</td>";
				echo "<td>". getDropdownName("glpi_dropdown_os",$comp->fields["os"]) ."</td>";
				echo "<td>". getDropdownName("glpi_dropdown_locations", $comp->fields["location"]) ."</td>";
				echo "<td>".$comp->fields["date_mod"]."</td>";
				echo "<td>".$comp->fields["contact"]."</td>";
                                
                                echo "</tr>";
			}

			// Close Table
			echo "</table></div>";

			// Pager
			$parameters="field=$field&phrasetype=$phrasetype&contains=$contains&sort=$sort";
			printPager($start,$numrows,$target,$parameters);

		} else {
			echo "<div align='center'><b>".$lang["computers"][32]."</b></div>";
			echo "<hr noshade>";
		}
	}
}

function showComputerForm($target,$ID,$withtemplate='') {
	global $lang,$HTMLRel;;
	$comp = new Computer;
	$computer_spotted = false;
	if(empty($ID) && $withtemplate == 1) {
		if($comp->getEmpty()) $computer_spotted = true;
	} else {
		if($comp->getfromDB($ID)) $computer_spotted = true;
	}
	if($computer_spotted) {
		if(!empty($withtemplate) && $withtemplate == 2) {
			$template = "newcomp";
			$datestring = $lang["computers"][14].": ";
			$date = date("Y-m-d H:i:s");
		} elseif(!empty($withtemplate) && $withtemplate == 1) { 
			$template = "newtemplate";
			$datestring = $lang["computers"][14].": ";
			$date = date("Y-m-d H:i:s");
		} else {
			$datestring = $lang["computers"][11]." : ";
			$date = $comp->fields["date_mod"];
			$template = false;
		}
		
		echo "<form name='form' method='post' action=\"$target\">";
		if(strcmp($template,"newtemplate") === 0) {
			echo "<input type=\"hidden\" name=\"is_template\" value=\"1\" />";
		}
		echo "<div align='center'>";
		echo "<table width='700px' class='tab_cadre' >";
		echo "<tr><th colspan ='2'align='center' >";
		if(!$template) {
			echo $lang["computers"][13].": ".$comp->fields["ID"];
		}elseif (strcmp($template,"newcomp") === 0) {
			echo $lang["computers"][12].": ".$comp->fields["tplname"];
		}elseif (strcmp($template,"newtemplate") === 0) {
			echo $lang["computers"][49]."&nbsp;: <input type='text' name='tplname' value=\"".$comp->fields["tplname"]."\" size='20'>";
		}
		
		echo "</th><th  colspan ='2' align='center'>".$datestring.$date;
		echo "</th></tr>";
		
		echo "<tr class='tab_bg_1'><td>".$lang["computers"][7]."&nbsp;:		</td>";
		echo "<td><input type='text' name='name' value=\"".$comp->fields["name"]."\" size='20'></td>";
						
		echo "<td>".$lang["computers"][16]."&nbsp;:	</td><td><input type='text' name='contact' size='20' value=\"".$comp->fields["contact"]."\">";
		echo "</tr>";
		
		echo "<tr class='tab_bg_1'>";
		
		echo "<td >".$lang["computers"][10]."&nbsp;: 	</td>";
		echo "<td >";
			dropdownValue("glpi_dropdown_locations", "location", $comp->fields["location"]);
		
		echo "</td>";
		
		echo "<td>".$lang["computers"][15]."&nbsp;:		</td><td><input type='text' name='contact_num' value=\"".$comp->fields["contact_num"]."\" size='20'></td></tr>";
		
	
		echo "<tr class='tab_bg_1'><td>".$lang["common"][5].": 	</td><td>";
		dropdownValue("glpi_enterprises","FK_glpi_enterprise",$comp->fields["FK_glpi_enterprise"]);
		echo "</td>";

		echo "<td valign='center' rowspan='4'>".$lang["computers"][19]."&nbsp;:</td><td valign='center' rowspan='4'><textarea  cols='35' rows='6' name='comments' >".$comp->fields["comments"]."</textarea></td></tr>";
		echo "<tr class='tab_bg_1'><td>".$lang["computers"][18]."&nbsp;:	</td>";
		echo "<td><input type='text' size='20' name='otherserial' value=\"".$comp->fields["otherserial"]."\">";
		echo "</td></tr>";

		echo "<tr class='tab_bg_1'><td>".$lang["computers"][17]."&nbsp;:	</td>";
		echo "<td><input type='text' name='serial' size='20' value=\"".$comp->fields["serial"]."\">";
		echo "</td></tr>";

		
		echo "<tr class='tab_bg_1'>";
		
		
		
		echo "<td>".$lang["computers"][27].": </td>";
		
		// Is Server?
		echo "<td>";
		if (isset($comp->fields["flags_server"]))
		{
			if($comp->fields["flags_server"]  == 1)
			{
				echo "<input type='checkbox' name='flags_server' value='1' checked>";
			}
			else
			{
			echo "<input type='checkbox' name='flags_server' value='1'>";
			}
		}
		else
		{
			echo "<input type='checkbox' name='flags_server' value='1'>";
		}
		echo " &nbsp;".$lang["computers"][28]."</td>";
		
		echo "</tr>";
		
		
		
		echo "<tr class='tab_bg_1'>";
		
		echo "<td>".$lang["computers"][9]."&nbsp;</td><td>";
		dropdownValue("glpi_dropdown_os", "os", $comp->fields["os"]);
		echo "</td>";
		
		if (!$template){
		echo "<td>".$lang["reservation"][24]."&nbsp;:</td><td><b>";
		showReservationForm(1,$ID);
		echo "</b></td>";
		} else echo "<td>&nbsp;</td><td>&nbsp;</td>";
		
		
		
		
		
		echo "</tr><tr>";
		
		if ($template) {
			if (empty($ID)){
			echo "<td class='tab_bg_2' align='center' colspan='4'>\n";
			echo "<input type='submit' name='add' value=\"".$lang["buttons"][8]."\" class='submit'>";
			echo "</td>\n";
			} else {
			echo "<td class='tab_bg_2' align='center' colspan='4'>\n";
			echo "<input type='hidden' name='ID' value=$ID>";
			echo "<input type='submit' name='update' value=\"".$lang["buttons"][7]."\" class='submit'>";
			echo "</td>\n";
			}
		} else {
			echo "<td class='tab_bg_2' colspan='2' align='center' valign='top'>\n";
			echo "<input type='submit' name='update' value=\"".$lang["buttons"][7]."\" class='submit'>";
			echo "</td>\n";
                        echo "<td class='tab_bg_2' colspan='2'  align='center'>\n";
			echo "<input type='hidden' name='ID' value=$ID>";
			echo "<input type='submit' name='delete' value=\"".$lang["buttons"][6]."\" class='submit'>";
			echo "";
			echo "</td>";
		}

		echo "</tr>\n";
		
		
		
		echo "</table>";
		echo "</div>";

		echo "</form>";
		
		
			//print devices.
		echo "<div align='center'>";
		echo "<table width='700' class='tab_cadre' >";
		echo "<tr><th colspan='64'>".$lang["devices"][10]."</th></tr>";
		foreach($comp->devices as $key => $val) {
			$devType = $val["devType"];
			$devID = $val["devID"];
			$specif = $val["specificity"];
			$compDevID = $val["compDevID"];
			$device = new Device($devType);
			$device->getFromDB($devID);
			printDeviceComputer(&$device,$specif,$comp->fields["ID"],$compDevID,$withtemplate);
			
			echo "</div>";
		}
		//ADD a new device form.
		device_selecter($_SERVER["PHP_SELF"],$comp->fields["ID"],$withtemplate);
		echo "</table>";
		return true;
	}
	else {
                echo "<div align='center'><b>".$lang["computers"][32]."</b></div>";
                echo "<hr noshade>";
                searchFormComputers();
                return false;
        }
}

function updateComputer($input) {
	// Update a computer in the database

	$comp = new Computer;
	$comp->getFromDB($input["ID"],0);

	// set new date and make sure it gets updated
	$updates[0]= "date_mod";
	$comp->fields["date_mod"] = date("Y-m-d H:i:s");

	// Pop off the last two attributes, no longer needed
	$null=array_pop($input);
	$null=array_pop($input);
	$null=array_pop($input);
	// Get all flags and fill with 0 if unchecked in form
	foreach  ($comp->fields as $key => $val) {
		if (eregi("\.*flag\.*",$key)) {
			if (empty($input[$key])) {
				$input[$key]=0;
			}
		}
	}

	// Fill the update-array with changes
	$x=1;
	foreach ($input as $key => $val) {
		if (empty($comp->fields[$key]) || $comp->fields[$key]  != $input[$key]) {
			$comp->fields[$key] = $input[$key];
			$updates[$x] = $key;
			$x++;
		}
	}
	$comp->updateInDB($updates);
}

function addComputer($input) {
	// Add Computer

	$comp = new Computer;
	
  // set new date.
   $comp->fields["date_mod"] = date("Y-m-d H:i:s");
   
	// dump status
	$null=array_pop($input);
	$null=array_pop($input);
	$i=0;
	// fill array for update
	foreach ($input as $key => $val){
	if (!isset($comp->fields[$key]) || $comp->fields[$key] != $input[$key]) {
			$comp->fields[$key] = $input[$key];
		}		
	}
	$comp->addToDB();
}

function deleteComputer($input) {
	// Delete Computer
	if(empty($input["template"])) $input["template"] = "";

	$comp = new Computer;
	$comp->deleteFromDB($input["ID"],$input["template"]);
} 	

function showConnections($ID,$withtemplate='') {

	GLOBAL $cfg_layout, $cfg_install, $lang;

	$db = new DB;

	echo "<div align='center'><table border='0' width='90%' class='tab_cadre'>";
	echo "<tr><th colspan='3'>".$lang["connect"][0].":</th></tr>";
	echo "<tr><th>".$lang["computers"][39].":</th><th>".$lang["computers"][40].":</th><th>".$lang["computers"][46].":</th></tr>";

	echo "<tr class='tab_bg_1'>";

	// Printers
	echo "<td align='center'>";
	$query = "SELECT * from glpi_connect_wire WHERE end2='$ID' AND type='".PRINTER_TYPE."'";
	if ($result=$db->query($query)) {
		$resultnum = $db->numrows($result);
		if ($resultnum>0) {
			echo "<table width='100%'>";
			for ($i=0; $i < $resultnum; $i++) {
				echo "<tr>";
				$tID = $db->result($result, $i, "end1");
				$printer = new Printer;
				$printer->getfromDB($tID);
				echo "<td align='center'><a href=\"".$cfg_install["root"]."/printers/printers-info-form.php?ID=$tID\"><b>";
				echo $printer->fields["name"]." (".$printer->fields["ID"].")";
				echo "</b></a></td>";
				if(!empty($withtemplate) && $withtemplate == 2) {
					//do nothing
				} else {
					echo "<td align='center'><a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?cID=$ID&eID=$tID&disconnect=1&device_type=".PRINTER_TYPE."&withtemplate=".$withtemplate."\"><b>";
					echo $lang["buttons"][10];
					echo "</b></a></td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		} else {
			echo $lang["computers"][38]."<br>";
		}
		if(!empty($withtemplate) && $withtemplate == 2) {
			//do nothing
		} else {
			echo "<a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?ID=$ID&connect=1&device_type=printer&withtemplate=".$withtemplate."\"><b>";
			echo $lang["buttons"][9];
			echo "</b></a>";
		}

	}
	echo "</td>";

	// Monitors
	echo "<td align='center'>";
	$query = "SELECT * from glpi_connect_wire WHERE end2='$ID' AND type='".MONITOR_TYPE."'";
	if ($result=$db->query($query)) {
		$resultnum = $db->numrows($result);
		if ($resultnum>0) {
			echo "<table width='100%'>";
			for ($i=0; $i < $resultnum; $i++) {
				echo "<tr>";
				$tID = $db->result($result, $i, "end1");
				$monitor = new Monitor;
				$monitor->getfromDB($tID);
				echo "<td align='center'><a href=\"".$cfg_install["root"]."/monitors/monitors-info-form.php?ID=$tID\"><b>";
				echo $monitor->fields["name"]." (".$monitor->fields["ID"].")";
				echo "</b></a></td>";
				if(!empty($withtemplate) && $withtemplate == 2) {
					//do nothing
				} else {
					echo "<td align='center'><a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?cID=$ID&eID=$tID&disconnect=1&device_type=".MONITOR_TYPE."&withtemplate=".$withtemplate."\"><b>";
					echo $lang["buttons"][10];
					echo "</b></a></td>";
				}
				echo "</tr>";
			}
			echo "</table>";			
		} else {
			echo $lang["computers"][37]."<br>";
		}
		if(!empty($withtemplate) && $withtemplate == 2) {
			//do nothing
		} else {
			echo "<a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?ID=$ID&connect=1&device_type=monitor&withtemplate=".$withtemplate."\"><b>";
			echo $lang["buttons"][9];
			echo "</b></a>";
		}

	}
	echo "</td>";
	
	//Peripherals
	echo "<td align='center'>";
	$query = "SELECT * from glpi_connect_wire WHERE end2='$ID' AND type='".PERIPHERAL_TYPE."'";
	if ($result=$db->query($query)) {
		$resultnum = $db->numrows($result);
		if ($resultnum>0) {
			echo "<table width='100%'>";
			for ($i=0; $i < $resultnum; $i++) {
				echo "<tr>";
				$tID = $db->result($result, $i, "end1");
				$periph = new Peripheral;
				$periph->getfromDB($tID);
				echo "<td align='center'><a href=\"".$cfg_install["root"]."/peripherals/peripherals-info-form.php?ID=$tID\"><b>";
				echo $periph->fields["name"]." (".$periph->fields["ID"].")";
				echo "</b></a></td>";
				if(!empty($withtemplate) && $withtemplate == 2) {
					//do nothing
				} else {
					echo "<td align='center'><a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?cID=$ID&eID=$tID&disconnect=1&device_type=".PERIPHERAL_TYPE."&withtemplate=".$withtemplate."\"><b>";
					echo $lang["buttons"][10];
					echo "</b></a></td>";
				}
				echo "</tr>";
			}
			echo "</table>";			
		} else {
			echo $lang["computers"][47]."<br>";
		}
		if(!empty($withtemplate) && $withtemplate == 2) {
			//do nothing
		} else {
			echo "<a href=\"".$cfg_install["root"]."/computers/computers-info-form.php?ID=$ID&connect=1&device_type=peripheral&withtemplate=".$withtemplate."\"><b>";
			echo $lang["buttons"][9];
			echo "</b></a>";
		}

	}

	echo "</tr>";
	echo "</table></div><br>";
	
}




?>
