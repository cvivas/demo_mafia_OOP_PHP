<?php

require_once ('mafia.php');
$mafias = new mafia;
$s = serialize($mafias);
$fp = fopen("store", "w");
fwrite($fp, $s);
fclose($fp);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="css/treecolor.css">
		<script src="js/ajax_request.js"></script>
		<script type="text/javascript">
			function isNumberKey(evt) {
				var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57))
					return false;
				return true;
			}

			function valid_actions() {
				if (document.getElementById('insert').checked) {
					document.getElementById('age').style.visibility = 'visible';
					document.getElementById('boss').style.visibility = 'visible';
					document.getElementById('name').style.visibility = 'visible';

					document.getElementById('age_label').style.visibility = 'visible';
					document.getElementById('boss_label').style.visibility = 'visible';
					document.getElementById('name_label').style.visibility = 'visible';
				} else {
					document.getElementById('age').style.visibility = 'hidden';
					document.getElementById('boss').style.visibility = 'hidden';
					document.getElementById('age_label').style.visibility = 'hidden';
					document.getElementById('boss_label').style.visibility = 'hidden';
				}
			}

			function toggle_visibility_instructions() {
				var e = document.getElementById('instructions');
				if (e.style.display == 'none')
					e.style.display = 'block';
				else
					e.style.display = 'none';
			}
		</script>
	</head>
	<body>
		<input type="button" value="show / hide instructions" id="instructions_btn" onClick="toggle_visibility_instructions()" >
		<div id="instructions">
				<span>I'm Carlos Vivas (<a href="http://www.cvivas.com">www.cvivas.com</a>) and this is an OOP exercise in PHP (with serialize to save the state) and javaScript.The interface below is built using AJAX.<br />
					The data structures that are shown are represented only with css (no javascript) <br />
					 The data structures used are mainly Tree Structures. It is the best way to show hierarchical relationships between the mafia members. <br />
					 It consists of a simple mafia problem. For all the actions (except show state) the system will print the MAFIA and JAIL structures BEFORE and AFTER the action. The different actions available are:</span>
			<ul id="instructions_list">
				<li><strong>Add a Gangster: </strong>indicate the name (primary key), Age and the name of the gangster's boss (if available)</li>
				<li><b>Kill a Gangster:</b> the gangster will be removed from the mafia structure and all his subordinates will hide (h). A gangster cannot be killed in jail.</li>
				<li><b>Jail:</b> A gangster goes to jail. All his direct subordinates are relocated and work for the oldest remaining boss at the same level than the previous boss.</li>
						<ul><li>If it was not possible, the oldest direct subordinate is promoted.  </li></ul>
				<li><b>Release from Jail:</b> When released, the gangster goes under the same boss if possible.</li>
					 <ul><li>if not, he will find a boss at the same level.</li>
					 <li>if still not possible, we will be released at the lowest grade position available.</li>
					 <li>The released gangster's direct subordinates will be removed from the organization and re-inserted under the gangster's control.</li></ul> 
				<li><b>Show state:</b> shows the current state. </li>
			</ul>
			<span>If you would like to view the source code, here is a link to the files on github: <a href="http://github.com/cvivas/demo_mafia_OOP_PHP">http://github.com/cvivas/demo_mafia_OOP_PHP </a></span>
		</div>

		<form>
			<input type="radio" name="actions" id="insert" value="insert" checked onclick="valid_actions()">
			<label for="insert">Add New Gangster </label>
			<input type="radio" name="actions" id="kill" value="kill" onclick="valid_actions()">
			<label for="kill">Kill </label>
			<input type="radio" name="actions" id="jail" value="jail" onclick="valid_actions()">
			<label for="jail">Jail </label>
			<input type="radio" name="actions" id="release" value="release" onclick="valid_actions()">
			<label for="release">Release </label>
			

			<br />
			<label for="name" id="name_label">Gangster's Name:</label>
			<input type="text" name="name" id="name" size="30" value="" class="text-input" >
			<label for="age" id="age_label">Gangster's Age:</label>
			<input type="number" min="1" max="999" name="age" id="age" size="6" value="1" class="text-input" onkeypress="return isNumberKey(event)">
			<label for="boss" id="boss_label">Gangster's boss:</label>
			<input type="text" name="boss" id="boss" size="30" value="" class="text-input">
			<br />
			<input type="button" value="submit" id="submit_btn" onClick="ajax_call()" >
			<input type="button" value="Show State" id="show_state_btn" onClick="ajax_show_state()" >
		</form>
		<div id="error_message"></div>
		<div id="results_area">

		</div>

	</body>
</html>