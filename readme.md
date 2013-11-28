Mafia exercise using OOP in PHP.
===============================

It consists of a simple mafia problem. For all the actions (except show state) the system will print the MAFIA and JAIL structures BEFORE and AFTER the action. The different actions available are:
 
 - **Add a Gangster**: indicate the name (primary key), Age and the name of the gangster's boss (if available)
 
 - **Kill a Gangster**: the gangster will be removed from the mafia structure and all his subordinates will hide (h). A gangster cannot be killed in jail.
 
 - **Jail**: A gangster goes to jail. All his direct subordinates are relocated and work for the oldest remaining boss at the same level than the previous boss.
	+ If it was not possible, the oldest direct subordinate is promoted. 
	
 - **Release from Jail**: When released, the gangster goes under the same boss if possible.
	+ if not, he will find a boss at the same level.
	+ if still not possible, we will be released at the lowest grade position available.
	+ The released gangster's direct subordinates will be removed from the organization and re-inserted under the gangster's control.
	
 - **Show state**: shows the current state. 


Most of the decisions I have made are reflected in comments along the document. 


A brief version would be: 

The data structure used is mainly a tree structure. It is implemented following OOP guidelines. It is a structure suitable for this kinds of problems where there is a hierarchy. 

The tree data structure is double linked, allowing traversal downwards and upwards along the tree. I did not find a need to implement another linked list between the nodes of the same level, although it could prove useful if the level based traversals increase. 


The traversing and search algorithms include iterative and recursive versions of DFS and iterative version of BFS. 

Most of the complexity is O(N) mainly due to the DFS and BFS algorithms. 

A demo of this is available at the following link: <http://damp-fjord-8874.herokuapp.com/>





