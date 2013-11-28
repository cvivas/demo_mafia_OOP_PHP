<?php
include ('Gangster.php');
class mafia {
	private $root = null;
	private $jail = null;

	public function __construct() {

		$this -> root = null;
		$this -> jail = array();

	}

	public function isEmpty() {
		return $this -> root === null;
	}

	public function create_gangster($name, $age, $level, $location, $boss, $subordinates) {
		if (!isset($name)) {
			throw new Exception("A name is required");
		}

		$gangster = new Gangster($name, $age, $level, $location, $boss, $subordinates);
		return $gangster;
	}

	public function insert_new_gangster($name, $age, $level, $location, $boss, $subordinates, $releasing = false) {

		//verify that the gangster name is unique
		$is_in_mafia = $this -> find_gangster_by_name_in_mafia($name);
		if ($is_in_mafia)
			return null;
		if ($releasing === false) {
			//when we are not releasing from jail
			//if the gangster is in jail already, we should abort
			$is_in_jail = $this -> find_gangster_by_name_in_jail($name);
			if ($is_in_jail)
				return null;
		}
		//case if empty structure
		if ($this -> isEmpty()) {
			//create new gangster in root and assign level 1
			$gangster = $this -> create_gangster($name, $age, 1, $location, null, null);
			return $this -> root = $gangster;

		} else {

			//$is_in_jail = $this->find_gangster_by_name_in_jail($name);
			if ($is_in_mafia)
				return null;
			//find the boss and insert as subordinate
			if (!isset($boss)) {
				//the gangster has no boss, so he should become the mafia head.
				$gangster = $this -> create_gangster($name, $age, 0, $location, null, $this -> root);
				$this -> root -> addBoss($gangster);
				$this -> root = $gangster;

				$this -> add_to_all_levels($this -> root);
				return $gangster;
			} else {

				//we find the if the boss with name=boss is in the mafia structure and add the gangster as his subordinate
				$BossNode = $this -> find_gangster_by_name_in_mafia($boss);

				if (isset($BossNode)) {
					return $this -> create_gangster($name, $age, $BossNode -> getLevel() + 1, $location, $BossNode, $subordinates);
				} else {
					//the boss does not appear in the mafia structure. add at the lowest level (always traversing the first child node).
					$index = $this -> root;
					while (sizeof($index -> getSubordinates()) > 0) {
						$subordinates_list = $index -> getSubordinates();
						reset($subordinates_list);

						$index = current($index -> getSubordinates());
					}
					return $this -> create_gangster($name, $age, $index -> getLevel() + 1, $location, $index, $subordinates);

				}
			}

		}

	}

	function find_gangster_by_name_in_mafia($name) {
		//iterative dfs search for the node with name = $name.
		//Order of traversal is root, then rightest node, then leftest (kind of reverse preorder)

		if ($this -> isEmpty())
			return null;
		$stack = array($this -> root);

		while (count($stack) > 0) {

			$node = array_pop($stack);
			//remove first element of array
			if ($node -> getName() == $name) {
				//found the node we where looking fore;
				return $node;
			} else {
				//if we havent found the gangster, we add all of his subordinates to the stack
				foreach ($node->getSubordinates() as $value) {
					array_push($stack, $value);
					//add elements to the end of the array
				}
			}
		}
		return null;
	}

	function find_gangster_by_name_in_jail($name) {
		//looks if the gangster with name = $name is in the jail, not looking at subordinates
		foreach ($this->jail as $value) {
			if ($value -> getName() == $name)
				return $value;
		}
		return null;
	}

	function add_to_all_levels($node, $toAdd = 1) {
		//add +1 to all levels dfs rec
		$node -> addLevel($toAdd);
		foreach ($node->getSubordinates() as $value) {
			$this -> add_to_all_levels($value, $toAdd);
		}
	}

	function kill_gangster($name) {
		/*
		 * When we kill a gangster, the gangster will be removed.
		 * All of the subordinates will change their location from the default value
		 * "organization" to "hidden"
		 * As this subordinates will not have a boss when we remove him from the mafia structure
		 * we have to choose between reordering (like for example making the oldest subordinate the new boss)
		 * or we could make everyone dependable of the murdered gangster's boss.
		 *
		 * I prefere to reorder the subordinates to make the oldest one of them the new boss, replacing the dead
		 * gangster who will be removed. This way it will also work if we are killing the mafia leader.
		 * */
		$gangster = $this -> find_gangster_by_name_in_mafia($name);
		if (isset($gangster)) {
			$this -> hide_all_subordinates($gangster);
			$this -> promote_oldest_subordinate($gangster);
		}
	}

	function hide_all_subordinates($gangster) {
		foreach ($gangster->getSubordinates() as $value) {
			$value -> hide();
			$this -> hide_all_subordinates($value);
		}
	}

	function jail_gangster($name) {
		/*
		 * When we send a gangster to jail, we mainly look for him in the
		 * mafia tree data structure, and if he is there, then
		 * we make a deep copy of this gangster in the "jail" structure
		 * And finally reorder the subordinates appropiately
		 * */
		$gangster = $this -> find_gangster_by_name_in_mafia($name);
		if (isset($gangster)) {
			$this -> copy_to_jail($gangster);
			$this -> reorder_subordinates_goto_jail($gangster);
		}
	}

	function copy_to_jail($gangster) {
		/*
		 * This is a crucial function to our code as the "get out of jail" case depends on it.
		 * PHP by default does a shallow copy of the gangster's subordinates.
		 * The options are:
		 *  - Do a deep copy of the gangster's subordinates (not needed recursively)
		 *  - Save an array with the names instead of "gangster" classes as subordinates and then have to do searches
		 *
		 * I chose to do a deep copy of the gangster's subordinates because it does not alter
		 * the logical structure of the Gangster class.
		 *
		 *
		 * */
		$gangster_clone = clone $gangster;
		array_push($this -> jail, $gangster_clone);
	}

	function reorder_subordinates_goto_jail($gangster) {

		//first we try to relocate with oldest boss at the same level
		$oldest_boss = $this -> oldest_remaining_boss($gangster -> getLevel(), $gangster -> getName());
		if (isset($oldest_boss)) {
			foreach ($gangster->getSubordinates() as $subordinate) {
				$oldest_boss -> addSubordinate($subordinate);
				$subordinate -> addBoss($oldest_boss);
			}
			//removing subordinate --> going to jail
			$gangster -> getBoss() -> removeSubordinate($gangster -> getName());
		} else {
			//if there is no older boss, we try with the oldest son
			$this -> promote_oldest_subordinate($gangster);
		}

	}

	function promote_oldest_subordinate($gangster) {
		/*
		 * In this function we promote the oldest subordinate of a gangster $gangster if possible
		 * After we remove the Gangster $gangster from the Mafia structure.
		 * */

		$oldest_subordinate = $this -> get_oldest_subordinate($gangster);
		if (isset($oldest_subordinate)) {
			//we found the oldest subordinate
			//we add the oldest subordinate to the subordinate list of the emprisoned gangster's boss
			if ($this -> root -> getName() == $gangster -> getName()) {
				//we are promoting one of the root's subordinates
				foreach ($this->root->getSubordinates() as $subs) {
					if ($oldest_subordinate -> getName() !== $subs -> getName()) {
						//add the same level subordinate as subordinates of the oldest subordinate
						$oldest_subordinate -> addSubordinate($subs);
						$subs -> addBoss($oldest_subordinate);
					}
					$this -> root = $oldest_subordinate;
					$oldest_subordinate -> addBoss(null);
					$this -> add_to_all_levels($this -> root, -1);
				}
			} else {
				//we are promoting one of the subordinates of a gangster who is not the root element
				$gangster -> getBoss() -> addSubordinate($oldest_subordinate);

				$oldest_subordinate -> addBoss($gangster -> getBoss());

				//we substract 1 to all levels of the promoted gangster
				$this -> add_to_all_levels($oldest_subordinate, -1);
				//we add the emprisoned gangster's subordinates to the promoted subordinate
				foreach ($gangster->getSubordinates() as $subordinate) {
					if ($oldest_subordinate -> getName() !== $subordinate -> getName()) {
						$oldest_subordinate -> addSubordinate($subordinate);
						$subordinate -> addBoss($oldest_subordinate);
					}
				}
			}
			if ($gangster -> getBoss()) {
				$gangster -> getBoss() -> removeSubordinate($gangster -> getName());
			}
		} else {
			//no subordinates
			if ($gangster -> getBoss()) {
				$gangster -> getBoss() -> removeSubordinate($gangster -> getName());
			} else {
				//no subordinates and no boss, so it is the only node, we delete the mafia tree
				$this -> root = null;
			}
		}
	}

	function oldest_remaining_boss($level, $gangster_name) {
		/*
		 * iterative bfs search for the oldest gangster at a level = $level
		 * it goes to a level above the target level and get the oldest of the subordinates (we get results 1 level before)
		 * the $gangster_name is used so that we can exclude him from the results
		 */
		if ($gangster_name == $this -> root -> getName())
			return null;
		//special case for level 1 (only one possible case)
		if ($level == 1) {
			if (!$this -> isEmpty())
				return $this -> root;
		}

		$queue = array($this -> root);
		$oldest_gangster = null;
		while (count($queue) > 0) {

			$node = array_shift($queue);
			//remove first element of array
			$current_level = $node -> getLevel();
			//if the following result is 0, the gangster we are looking for has to be in the next level, so we get the oldest of the subordinates
			$current_level = $current_level - $level + 1;

			if ($current_level == 0) {
				//the target has to be one of the node's subordinates
				foreach ($node->getSubordinates() as $value) {
					//it should not be the child we already are exploring
					if ($value -> getName() !== $gangster_name) {
						if (!isset($oldest_gangster)) {
							$oldest_gangster = $value;
						}
						if ($value -> getAge() > $oldest_gangster -> getAge()) {
							$oldest_gangster = $value;
						}
					}
				}
			} elseif ($current_level < 0) {
				//we have not reached the desired depth, so we add the subordinates
				foreach ($node->getSubordinates() as $value) {
					array_push($queue, $value);
					//add elements to the end of the array
				}
			}

		}
		return $oldest_gangster;
	}

	function get_oldest_subordinate($node) {
		/*we get the oldest subordinate of the gangster $node
		 * we can not re use this function in the oldest_remaining_boss function, though they are similar
		 */
		$oldest_gangster = null;
		foreach ($node->getSubordinates() as $value) {
			if (!isset($oldest_gangster)) {
				$oldest_gangster = $value;
			}
			if ($value -> getAge() > $oldest_gangster -> getAge()) {
				$oldest_gangster = $value;
			}
		}
		return $oldest_gangster;
	}

	function release_gangster($name) {
		/*
		 * Release gangster with name = $name from jail
		 * When restoring the gangster, I decided to release him in the following manner
		 * - If the boss has died / is in prison the gangster will have as boss the oldest of the same level as the
		 * old boss. If it were not possible, the gangster would be inserted as a new gangster at the highest (deepest) level.
		 * I add the gangsters back to the "mafia" organization by creating new elements, and once they are back in the
		 * mafia, I add them their subordinates.
		 *
		 * We could also have another approach: add the subordinate trees in the jail structure and when the gangster comes back,
		 * They automatically have their subordinates. (add subordinates and then move to mafia organization)
		 *
		 *
		 *
		 * Regarding the subordinates:
		 * We will remove the gangster's subordinates from the rest of the mafia organization and add them under the released
		 * gangster. The released gangster will only have his direct and loyal subordinates.
		 *
		 * Another (complex and mind bending) implementation would be to find a way to also add
		 * the subordinate's subordinates. However this task would require in real life a lot of specific interactions that go
		 * beyond the scope. We would need ways to better manipulate in a real life situation.
		 *
		 * An example of the posible distributon of the subordinates would be:
		 *
		 * - if a subordinate is at a higher (deeper) or equal level than the boss, the subordinate would be transfered with
		 * his corresponding subordinates to be a direct subordinate of the released gangster
		 * (it makes sense that a less important subordinate would bring his crew)
		 * -if a subordinate is at a lower (less deep) level than the boss, the subordinate would be transfered with
		 * his corresponding subordinates ONLY IF the released gangster is not a child (recursively) of the subordinate.
		 * in the case the released gangster is a child of one of his subordinates, then only the subordinate would be
		 * detached and added as a subordinate of the gangster.
		 * In these cases the gangster would have surely increased his crew while in jail. However, this is beyond the scope
		 *
		 *
		 *
		 * */
		$gangster = $this -> release_gangster_under_boss($name);
		//place the gangster under the boss if posible
		if (!isset($gangster))
			return null;
		// gangster not found in jail.
		$names = array();
		foreach ($gangster->getSubordinates() as $value) {
			$temp_name = $value -> getName();
			$names[$temp_name] = $temp_name;
		}

		//names contain the subordinate's names
		$gangster_in_mafia = $this -> find_gangster_by_name_in_mafia($gangster -> getName());
		$this -> subordinates_of_released_gangster($names, $gangster_in_mafia);
		$this -> delete_from_jail($name);
	}

	function release_gangster_under_boss($name) {
		//restores the gangster with name $name from jail under the boss he should have now by creating a new gangster and inserting it
		$gangster = $this -> find_gangster_by_name_in_jail($name);
		if (isset($gangster)) {
			$gangster_boss_jail = $gangster -> getBoss();
			if (isset($gangster_boss_jail)) {
				// the boss appears referenced in the jail structure. we look for the gangster's boss in the mafia structure
				$gangster_boss_mafia = $this -> find_gangster_by_name_in_mafia($gangster_boss_jail -> getName());
				if (isset($gangster_boss_mafia)) {
					//the boss is in the mafia. we proceed to insert the gangster as a subordinate.
					//we create a new $gangster entry to insert instead of moving the gangster from jail
					// (could also do it,but the create functions work well in this case)
					$this -> insert_new_gangster($gangster -> getName(), $gangster -> getAge(), 0, "organization", $gangster_boss_mafia -> getName(), null, TRUE);
				} else {
					//boss is not in the mafia structure. we insert at the same level of the gangster's boss. if not possible we insert at the end as if he were new to the mafia.
					$oldest_remaining_boss_at_level = $this -> oldest_remaining_boss($gangster_boss_jail -> getLevel(), $gangster_boss_jail -> getName());
					if (isset($oldest_remaining_boss_at_level)) {
						//we found a boss at the same level
						$this -> insert_new_gangster($gangster -> getName(), $gangster -> getAge(), 0, "organization", $oldest_remaining_boss_at_level -> getName(), null, TRUE);
					} else {
						//no boss found at same level
						$this -> insert_new_gangster($gangster -> getName(), $gangster -> getAge(), 0, "organization", "-1bossfalse", null, TRUE);
					}
				}
			} else {
				//the root, no boss, we insert at begining
				$this -> insert_new_gangster($gangster -> getName(), $gangster -> getAge(), 0, "organization", null, null, TRUE);
			}
			return $gangster;
		}
	}

	function subordinates_of_released_gangster($names, $gangster) {
		/*
		 * Traverses the mafia $root tree structure (DFS) looking for all the gangster's names in the $names array.
		 * Then it transfers all the gangsters with names in $names found as direct subordinates of the gangster $gangster
		 * */

		$resultSubordinates = array();
		$stack = array($this -> root);

		//we first elaborate a list of the gangster's subordinates in the mafia structure to transfer them after
		while (count($stack) > 0 && count($names) > 0) {
			//we end when no more to be traversed or no names as subordinates

			$node = array_pop($stack);
			//remove last element of array

			if (in_array($node -> getName(), $names)) {
				//found a match! add it to $resultsubordinates
				$resultSubordinates[] = $node;
			}
			// we add all of the subordinates to the stack for traversal
			foreach ($node->getSubordinates() as $value) {
				array_unshift($stack, $value);
				//add elements to the beginning of the array
			}
		}
		$this -> transfer_subordinate($resultSubordinates, $gangster);
	}

	function transfer_subordinate($origins, $destinationBoss) {
		/*
		 * $origins is the released gangster's direct subordinates when he was in jail
		 * $destinationBoss is the node with the gangster that is being released
		 * This function moves the gangsters in the $origins array as direct subordinates to $destinationBoss
		 * */
		foreach ($origins as $origin) {
			if (!is_null($origin -> getBoss())) {
				//the gangser from $origins array is not the head of the mafia
				//FIRST we remove the "origin" from the list and after we add it to the released gangster's subordinates
				if (count($origin -> getSubordinates()) > 0) {
					//double linked
					foreach ($origin->getSubordinates() as $sub) {
						//linked subordinates
						$origin -> getBoss() -> addSubordinate($sub);
						$sub -> addBoss($origin -> getBoss());
						$origin -> removeSubordinate($sub -> getName());
						$this -> add_to_all_levels($sub, -1);
					}
					$origin -> getBoss() -> removeSubordinate($origin -> getName());
					$origin -> removeAllSubordinates();
				} else {
					//single linked (leaf)
					$origin -> getBoss() -> removeSubordinate($origin -> getName());
				}

				$origin -> addBoss($destinationBoss);
				$origin -> getBoss() -> addSubordinate($origin);
				$origin -> setLevel($destinationBoss -> getLevel() + 1);

			} else {
				//origin is root element, he will give root to his first subordinate and then move to be a direct subordinate of the $destinationBoss
				//$this->dump_tree();
				if (count($origin -> getSubordinates()) > 0) {
					$originSubordinates = $origin -> getSubordinates();
					$newRoot = array_shift($originSubordinates);
					$this -> add_to_all_levels($newRoot, -1);

					foreach ($origin->getSubordinates() as $subs) {
						//as we are going to move origin to the new destination and as origin was root,
						// Origin's subordinates now become the root's subordinates
						if ($subs -> getName() !== $newRoot -> getName()) {
							//case where the new root was a subordinate of the root we are removing (this is a check for cicular cases)
							$newRoot -> addSubordinate($subs);
							$subs -> addBoss($newRoot);
							$origin -> removeSubordinate($subs -> getName());
						} else {
							//case where the new root is a subordinate, as we are moving the new root to be at $this->root, we only have to take care to remove subordinates
							//we could refactor the next line but I prefere not as it is better for understanding
							$origin -> removeSubordinate($subs -> getName());
						}
					}
					//once we have treated the subordinates, we move the $origin node under the $destinationBoss
					// and also make his first subordinate the new boss ($this->root = $newRoot) while treating the different links
					$origin -> addBoss($destinationBoss);
					$origin -> getBoss() -> addSubordinate($origin);
					$origin -> setLevel($destinationBoss -> getLevel() + 1);
					$this -> root = $newRoot;
					$newRoot -> removeBoss();
				}
			}
		}
	}

	function delete_from_jail($name) {

		foreach ($this->jail as $key => $value) {
			if ($value -> getName() === $name) {
				unset($this -> jail[$key]);
			}
		}
	}

	/*
	 * print and testing functions
	 * */
	function dump_tree($type = "") {
		/*
		 * prints the mafia tree structure as ul
		 * */
		echo "Mafia Structure <br />";
		echo '<div class="tree"' . $type . '>';
		if (isset($this -> root))
			$this -> root -> dump_html();
		echo '</div>';
		echo '<div class="clearfix"> </div>';
	}

	function dump_jail() {
		/*
		 * prints the jail structure
		 * */

		echo "Jail structure <br />";
		echo '<div class="tree jail">';
		foreach ($this->jail as $value) {
			$value -> dump_jail_html();
		}
		echo "</div>";
		echo '<div class="clearfix"> </div>';
	}

	function dump_all() {
		/*
		 * prints both structures
		 * */
		echo "-----------------------------<br />";
		$this -> dump_tree();
		$this -> dump_jail();
		echo "-----------------------------<br />";
	}

	function debug_dump() {
		/*used for debugging*/
		echo "-----------------------------<br />";
		$this -> root -> dump();

		echo "-----------------------------<br />";
	}

	function convert_dfs_string($tree) {
		/*
		 * Traverses data structure and returns array with {name - level} in DFS PREORDER to automate tests.
		 * */
		$outputArray = array();
		$stack = array($tree);
		while (count($stack) > 0) {

			$node = array_shift($stack);
			//remove first element of array
			array_push($outputArray, $node -> getName());
			array_push($outputArray, $node -> getLevel());
			//reverse array for dfs in preorder
			foreach (array_reverse($node->getSubordinates()) as $value) {
				array_unshift($stack, $value);
				//add elements to the start of the array
			}
		}
		return $outputArray;
	}

	function getRoot() {
		return $this -> root;
	}

	function convert_jail_to_string_array() {
		//prints the node in jail, the boss, and then the subordinates
		$output = null;
		foreach ($this->jail as $value) {
			$output[] = $this -> convert_gangster_jail_element_to_string_array($value);
		}
		return $output;
	}

	function convert_gangster_jail_element_to_string_array(Gangster $gangster) {
		// node name, level, boss name (or no_name), level (or -1), sub name i , level i
		$output = array($gangster -> getName(), $gangster -> getLevel());
		$boss = $gangster -> getBoss();
		if (isset($boss)) {
			$output[] = $gangster -> getBoss() -> getName();
			$output[] = $gangster -> getBoss() -> getLevel();
		} else {
			$output[] = "no_name";
			$output[] = -1;
		}
		foreach ($gangster->getSubordinates() as $value) {
			$output[] = $value -> getName();
			$output[] = $value -> getLevel();
		}
		return $output;
	}
}
?>