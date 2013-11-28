<?php

class Gangster {

	//properties
	private $name = "";
	private $age = 0;
	private $level = 0;
	private $location = "organization";
	private $boss = null;
	private $subordinates = array();

	function __construct($name, $age, $level, $location, $boss, $subordinates) {
		//add values to the Gangster node
		$this -> name = $name;
		$this -> age = $age;
		$this -> level = $level;
		$this -> location = $location;
		$this -> boss = $boss;
		if (isset($subordinates)) {
			$this -> subordinates = array();
			$this -> subordinates[] = $subordinates;
		} else {
			$subordinates = array();
		}
		//if there is a boss, insert the gangster in the boss's structure
		if (isset($this -> boss)) {
			$this -> boss -> addSubordinate($this);
		}
	}

	public function __clone() {
		// $this->subordinates = clone $this->subordinates;
		// we need deep cloning instead of shallow cloning for the jail array
		foreach ($this->subordinates as $key => $child) {
			$this -> subordinates[$key] = clone $this -> subordinates[$key];
		}
	}

	public function addSubordinate(Gangster $gangsterSubordinate) {
		$this -> subordinates[] = $gangsterSubordinate;
	}

	public function getSubordinates() {
		return $this -> subordinates;
	}

	public function addBoss($boss) {
		$this -> boss = $boss;
	}

	public function removeBoss() {
		$this -> boss = null;
	}

	public function setLevel($level) {
		$this -> level = $level;
	}

	public function getLevel() {
		return $this -> level;
	}

	public function addLevel($val) {
		$this -> level += $val;
	}

	function getBoss() {

		if (isset($this -> boss)) {
			return $this -> boss;
		} else {
			return null;
		}
	}

	public function getName() {
		return $this -> name;
	}

	public function getAge() {
		return $this -> age;
	}

	public function hide() {
		$this -> location = "undisclosed";
	}

	function removeSubordinate($name) {
		foreach ($this->subordinates as $key => $value) {
			if ($value -> name == $name) {
				unset($this -> subordinates[$key]);
			}
		}
	}

	function removeAllSubordinates() {
		$this -> subordinates = array();
	}

	public function dump() {
		//dump function for debugging.
		//preorder
		echo "Name: \"" . $this -> name . "\"  ";
		echo "Level: \"" . $this -> level . "\"  ";
		echo "Age: \"" . $this -> age . "\"  ";
		echo "Location: \"" . $this -> location . "\"  ";
		echo "Boss: \"";
		if (isset($this -> boss)) {
			echo $this -> boss -> getName() . "\"  ";
		} else {
			echo "NO BOSS" . "\"  ";
		}

		echo "<br />";
		foreach ($this->subordinates as $value) {
			$value -> dump();
		}
	}

	public function dump_html() {
		/*
		 * prints the mafa structure as a tree
		 * */
		if (!isset($this -> boss)) {
			//root
			echo '<ul >';

		}

		echo '<li><a href="#"><b>' . $this -> name . '</b>  (' . $this -> age . ') L-' . $this -> level;
		if ($this -> location === "undisclosed")
			echo " (h)";
		echo '</a>';
		if ($this -> subordinates != null)
			echo "<ul>";
		foreach ($this->subordinates as $value) {
			$value -> dump_html();
		}
		if ($this -> subordinates != null)
			echo "</ul>";
		echo "</li>";

		if (!isset($this -> boss)) {
			//root
			echo "</ul>";

		}

	}

	public function dump_jail_html() {
		/*
		 * Prints jail structure. if there is no boss it prints the head as No Boss.
		 * it only prints the boss, the gangster and his direct subordinates
		 * */
		echo '<ul class="first_jail_ul"><li><a href="#">Boss: ';
		if (isset($this -> boss)) {
			echo '<b>' . $this -> boss -> getName() . '</b>(' . $this -> boss -> getAge() . ') L-' . $this -> boss -> getLevel();
		} else {

			echo "NO BOSS";
		}
		echo "</a><ul>";
		echo '<li><a href="#">Jail: <b>' . $this -> name . '</b> (' . $this -> age . ') L-' . $this -> level;
		if ($this -> location === "undisclosed")
			echo " (h)";
		echo '</a>';
		if ($this -> subordinates != null) {
			echo '<ul class="subs">';
			foreach ($this->subordinates as $value) {
				echo '<li><a href="#"><b>' . $value -> getName() . '</b>(' . $value -> getAge() . ') L-' . $value -> getLevel() . '</a></li>';
			}
			echo "</ul>";
		}

		echo "</li></ul></li></ul>";
	}

}
?>