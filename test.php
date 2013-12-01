<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" href="css/treecolor.css">
	</head>
	<body >

		<?php

		require_once ('mafia.php');

		test_initial_inserts();
		// //pass
		test_killing_gangster();
		// //pass
		test_kill_simple();
		// //pass
		test_goto_jail_root_multiple_subordinates();
		test_goto_jail_no_samelevel_boss();
		// //pass
		test_goto_jail_with_same_level_boss();
		// //pass
		test_release_jail();
		// //pass
		test_release_jail_head();
		// //pass
		test_complex_release_jail();
		// //pass
		test_complex_release_jail2();
		//pass
		test_complex_release_jail_subordinate_root();
		//pass
		test_complex_release_jail_with_kill();
		//pass

		function test_initial_inserts() {
			// echo "TESTING INITIAL INSERTS <br />";
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//tests initial inserts
			$mafia -> insert_new_gangster("OldHeadBoss", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "OldHeadBoss", null);
			$mafia -> insert_new_gangster("3.2.1", 33, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("2.2", 52, $level, $location, "OldHeadBoss", null);
			$mafia -> insert_new_gangster("2.3", 50, $level, $location, "OldHeadBoss", null);
			$mafia -> insert_new_gangster("newBoss", 50, $level, $location, null, null);

			//$mafia -> dump_tree();

			$output_array = $mafia -> convert_dfs_string($mafia -> getRoot());
			$checkArray = array("newBoss", 1, "OldHeadBoss", 2, "2.1", 3, "2.2", 3, "2.3", 3, "3.2.1", 4);

			if ($output_array === $checkArray)
				echo "test_initial_inserts PASSED <br />";
			// echo "TESTING INITIAL INSERTS END <br />";
		}

		function test_killing_gangster() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "1", null);
			$mafia -> insert_new_gangster("3.2.1", 52, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.2", 54, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.3", 60, $level, $location, "2.1", null);

			// echo "STATE BEFORE KILLING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "KILL GANGSTER NAME: 2.1 <br />";
			$mafia -> kill_gangster("2.1");
			// $mafia -> dump_tree();

			// echo "TESTING KILL A GANGSTER END<br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1, "3.2.3", 2, "3.2.1", 3, "3.2.2", 3);

			$expected_jail = null;

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_killing_gangster PASSED <br />";

		}

		function test_kill_simple() {

			$mafia = new mafia;
			$level = 0;
			$location = "organization";

			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "1", null);
			// echo "STATE BEFORE KILLING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "KILL GANGSTER NAME: 2.1 <br />";
			$mafia -> kill_gangster("2.1");
			// $mafia -> dump_tree();

			// echo "TESTING KILL A GANGSTER END<br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1);

			$expected_jail = null;

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_kill_simple PASSED <br />";
		}
		function test_goto_jail_root_multiple_subordinates(){
			
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			
			$mafia -> insert_new_gangster("a", 1, $level, $location, null, null);
			$mafia -> insert_new_gangster("b", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("c", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("d", 2, $level, $location, "a", null);
			$mafia -> insert_new_gangster("e", 3, $level, $location, "a", null);
			$mafia -> insert_new_gangster("f", 1, $level, $location, "e", null);
			$mafia -> jail_gangster("a");

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("e", 1, "f", 2, "b", 2, "c", 2, "d", 2);

			$expected_jail = array( array("a", 1, "no_name", -1, "b", 2, "c", 2, "d", 2,"e",2));
			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_goto_jail_root_multiple_subordinates PASSED <br />";
		}

		function test_goto_jail_no_samelevel_boss() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "1", null);
			$mafia -> insert_new_gangster("3.2.1", 52, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.2", 54, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.3", 60, $level, $location, "2.1", null);
			// echo "TEST GO TO JAIL NOT SAME LEVEL START <br />";
			// echo "STATE BEFORE EMPRISONING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "<span>JAILING GANGSTER NAME: 2.1 <br /></span>";
			$mafia -> jail_gangster("2.1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1, "3.2.3", 2, "3.2.1", 3, "3.2.2", 3);

			$expected_jail = array( array("2.1", 2, "1", 1, "3.2.1", 3, "3.2.2", 3, "3.2.3", 3));

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_goto_jail_no_samelevel_boss PASSED <br />";
			// echo "TESTING GO TO JAIL NOT SAME LEVEL END<br />";

		}

		function test_goto_jail_with_same_level_boss() {
			// echo "TESTING GO TO JAIL SAME LEVEL <br />";
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1-1", 54, $level, $location, "1", null);
			$mafia -> insert_new_gangster("2.2-1", 55, $level, $location, "1", null);
			$mafia -> insert_new_gangster("3.2.1", 52, $level, $location, "2.1-1", null);
			$mafia -> insert_new_gangster("3.2.2", 54, $level, $location, "2.1-1", null);
			$mafia -> insert_new_gangster("3.2.3", 60, $level, $location, "2.1-1", null);

			// echo "STATE BEFORE EMPRISONING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "JAILING GANGSTER NAME: 2.1-1 <br />";
			$mafia -> jail_gangster("2.1-1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			// echo "TESTING GO TO JAIL SAME LEVEL END<br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1, "2.2-1", 2, "3.2.1", 3, "3.2.2", 3, "3.2.3", 3);

			$expected_jail = array( array("2.1-1", 2, "1", 1, "3.2.1", 3, "3.2.2", 3, "3.2.3", 3));

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_goto_jail_with_samelevel_boss PASSED <br />";
		}

		function test_release_jail() {

			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "1", null);
			$mafia -> insert_new_gangster("3.2.1", 52, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.2", 54, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.3", 60, $level, $location, "2.1", null);

			// echo "STATE BEFORE EMPRISONING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "JAILING GANGSTER NAME: 2.1 <br />";
			$mafia -> jail_gangster("2.1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			// echo "TESTING GO TO JAIL NOT SAME LEVEL END<br />";
			// echo "TESTING RELEASED PRISON NOT SAME LEVEL <br />";
			// echo "RELEASING GANGSTER NAME: 2.1 <br />";
			$mafia -> release_gangster("2.1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			// echo "TESTING RELEASED PRISON NOT SAME LEVEL END<br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1, "2.1", 2, "3.2.3", 3, "3.2.1", 3, "3.2.2", 3);

			$expected_jail = null;

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_release_jail PASSED <br />";

		}

		function test_release_jail_head() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("1", 55, $level, $location, null, null);
			$mafia -> insert_new_gangster("2.1", 54, $level, $location, "1", null);
			$mafia -> insert_new_gangster("3.2.1", 52, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.2", 54, $level, $location, "2.1", null);
			$mafia -> insert_new_gangster("3.2.3", 60, $level, $location, "2.1", null);

			// echo "STATE BEFORE EMPRISONING A GANGSTER <br />";
			// $mafia -> dump_tree();
			// echo "JAILING GANGSTER NAME: 1 <br />";
			$mafia -> jail_gangster("1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			// echo "TESTING GO TO JAIL ROOT END<br />";
			// echo "TESTING RELEASED PRISON Root <br />";

			$mafia -> release_gangster("1");
			// $mafia -> dump_tree();
			// $mafia -> dump_jail();

			// echo "TESTING RELEASED PRISON ROOT END<br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("1", 1, "3.2.1", 2, "3.2.2", 2, "3.2.3", 2, "2.1", 2);

			$expected_jail = null;

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_release_jail_head PASSED <br />";
		}

		function test_complex_release_jail() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("a", 1, $level, $location, null, null);
			$mafia -> insert_new_gangster("b", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("c", 1, $level, $location, "b", null);
			$mafia -> insert_new_gangster("d", 1, $level, $location, "c", null);
			$mafia -> insert_new_gangster("e", 1, $level, $location, "d", null);
			$mafia -> insert_new_gangster("f", 1, $level, $location, "e", null);
			$mafia -> insert_new_gangster("g", 1, $level, $location, "f", null);
			$mafia -> insert_new_gangster("h", 1, $level, $location, "f", null);
			$mafia -> insert_new_gangster("j", 1, $level, $location, "g", null);
			// echo "JAILING GANGSTER NAME: B <br />";
			$mafia -> jail_gangster("b");
			// echo "JAILING GANGSTER NAME: F <br />";
			$mafia -> jail_gangster("f");
			// echo "JAILING GANGSTER NAME: E <br />";
			$mafia -> jail_gangster("e");
			// echo "RELEASING GANGSTER NAME: E <br />";
			// $mafia -> dump_all();
			$mafia -> release_gangster("e");
			// echo "END? <br />";
			// $mafia -> dump_all();

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("a", 1, "c", 2, "d", 3, "e", 4, "j", 4, "h", 4, "g", 5);

			$expected_jail = array( array("b", 2, "a", 1, "c", 3), array("f", 5, "e", 4, "g", 6, "h", 6));

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_complex_release_jail PASSED <br />";
		}

		function test_complex_release_jail2() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			//no boss same level
			$mafia -> insert_new_gangster("a", 1, $level, $location, null, null);
			$mafia -> insert_new_gangster("b", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("c", 1, $level, $location, "b", null);
			$mafia -> insert_new_gangster("d", 1, $level, $location, "c", null);
			$mafia -> insert_new_gangster("e", 1, $level, $location, "d", null);
			$mafia -> insert_new_gangster("f", 1, $level, $location, "e", null);
			$mafia -> insert_new_gangster("g", 1, $level, $location, "f", null);
			$mafia -> insert_new_gangster("h", 1, $level, $location, "f", null);
			$mafia -> insert_new_gangster("j", 1, $level, $location, "g", null);
			// echo "JAILING GANGSTER NAME: B <br />";
			$mafia -> jail_gangster("b");
			// echo "JAILING GANGSTER NAME: F <br />";
			$mafia -> jail_gangster("f");
			// echo "JAILING GANGSTER NAME: E <br />";
			$mafia -> jail_gangster("e");
			// echo "KILL GANGSTER NAME: D <br />";
			$mafia -> kill_gangster("d");
			// $mafia -> dump_all();
			// echo "RELEASING GANGSTER NAME: E <br />";
			$mafia -> release_gangster("e");
			// $mafia -> dump_all();
			// echo "END <br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("a", 1, "c", 2, "j", 3, "h", 3, "e", 3, "g", 4);

			$expected_jail = array( array("b", 2, "a", 1, "c", 3), array("f", 5, "e", 4, "g", 6, "h", 6));

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_complex_release_jail2 PASSED <br />";
		}

		function test_complex_release_jail_subordinate_root() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			$mafia -> insert_new_gangster("a", 1, $level, $location, null, null);
			$mafia -> insert_new_gangster("b", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("c", 1, $level, $location, "b", null);
			$mafia -> insert_new_gangster("d", 1, $level, $location, "b", null);
			$mafia -> insert_new_gangster("e", 1, $level, $location, "b", null);
			// echo "JAILING GANGSTER NAME: B <br />";
			$mafia -> jail_gangster("b");
			// echo "KILL GANGSTER NAME: A <br />";
			$mafia -> kill_gangster("a");
			// $mafia->dump_all();
			// echo "RELEASING GANGSTER NAME: B <br />";
			$mafia -> release_gangster("b");
			// $mafia -> dump_all();
			// echo "END <br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("b", 1, "c", 2, "d", 2, "e", 2);

			$expected_jail = null;

			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_complex_release_jail_subordinate_root PASSED <br />";
		}

		function test_complex_release_jail_with_kill() {
			$mafia = new mafia;
			$level = 0;
			$location = "organization";
			$mafia -> insert_new_gangster("a", 1, $level, $location, null, null);
			$mafia -> insert_new_gangster("b", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("c", 1, $level, $location, "a", null);
			$mafia -> insert_new_gangster("d", 1, $level, $location, "c", null);
			$mafia -> insert_new_gangster("e", 1, $level, $location, "c", null);
			$mafia -> insert_new_gangster("f", 1, $level, $location, "e", null);
			// echo "JAILING GANGSTER NAME: C <br />";
			$mafia -> jail_gangster("c");
			// echo "KILL GANGSTER NAME: A <br />";
			$mafia -> kill_gangster("a");
			// $mafia->dump_all();
			// echo "RELEASING GANGSTER NAME: C <br />";
			$mafia -> release_gangster("c");
			// $mafia -> dump_all();
			// echo "END <br />";

			$calculated_mafia = $mafia -> convert_dfs_string($mafia -> getRoot());
			$calculated_jail = $mafia -> convert_jail_to_string_array();

			$expected_mafia = array("b", 1, "c", 2, "d", 3, "e", 3, "f", 2);

			$expected_jail = null;
			if ($expected_jail !== $calculated_jail)
				echo "not same jail";
			if ($expected_mafia !== $calculated_mafia)
				echo "not same mafia";
			if (($expected_jail === $calculated_jail) && ($expected_mafia === $calculated_mafia))
				echo "test_complex_release_jail_with_kill PASSED <br />";

		}
	?>
	</body>
