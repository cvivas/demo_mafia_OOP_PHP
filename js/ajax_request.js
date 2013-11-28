function validate() {
	//very basic validations
	var namevalue = document.getElementById("name").value;
	var agevalue = document.getElementById("age").value;
	var bossvalue = document.getElementById("boss").value;
	var errors = "";
	//no check to be made if dump is selected

	if (namevalue.length == 0) {
		errors = "You should insert a name for the gangster";
	} else {
		//if it is an insert, we check for existance of age
		if (document.getElementById('insert').checked) {
			if (agevalue < 1 || agevalue > 999)
				errors = "age not correct";
		}
	}

	if (errors.length > 0) {
		document.getElementById("error_message").innerHTML = errors;
		document.getElementById("error_message").style.display = "block";
		return false;
	}
	document.getElementById("error_message").style.display = "none";
	return true;

}

function initiateRequest() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else {
		return new ActiveXObject("Microsoft.XMLHTTP");
	}
}

function create_request() {

	var namevalue = encodeURIComponent(document.getElementById("name").value);
	var agevalue = encodeURIComponent(document.getElementById("age").value);
	var bossvalue = encodeURIComponent(document.getElementById("boss").value);

	var radios = document.getElementsByName('actions');
	var actions;
	for (var i = 0, length = radios.length; i < length; i++) {
		if (radios[i].checked) {
			// do whatever you want with the checked radio
			actions = radios[i].value;
			break;
		}
	}
	var parameters = "name=" + namevalue + "&age=" + agevalue + "&boss=" + bossvalue + "&actions=" + actions;
	return parameters;
}

function ajax_callling() {
	var postrequest = new initiateRequest();
	postrequest.onreadystatechange = function() {
		if (postrequest.readyState == 4) {
			if (postrequest.status == 200 || window.location.href.indexOf("http") == -1) {
				document.getElementById("results_area").innerHTML = postrequest.responseText;
			} else {
				alert("An error has occured making the request");
			}
		}
	};
	var parameters = create_request();
	postrequest.open("POST", "/ajax_functions.php", true);
	postrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	postrequest.send(parameters);

}

function ajax_show_state() {
	var postrequest = new initiateRequest();
	postrequest.onreadystatechange = function() {
		if (postrequest.readyState == 4) {
			if (postrequest.status == 200 || window.location.href.indexOf("http") == -1) {
				document.getElementById("results_area").innerHTML = postrequest.responseText;
			} else {
				alert("An error has occured making the request");
			}
		}
	};
	var parameters = 'actions=dump';
	postrequest.open("POST", "/ajax_functions.php", true);
	postrequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	postrequest.send(parameters);

}

function ajax_call() {
	var bool = validate();
	if (bool)
		ajax_callling();
}

