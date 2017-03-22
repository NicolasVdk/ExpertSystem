<?php

	include_once ("parser.php");

	if ($argc > 2)
		error("Too much argument");	
	else if ($argc == 2)
		files($argv[1]);
	else
		read();

	function error($error = null, $line = 0) {
		echo ($error === null ? "Unkown error occured" : $error) . ($line > 0 ? " Line : " : "") . "." . PHP_EOL;
		exit (1);
	}

	function files($filename) {
		if (file_exists($filename)) {
			if (is_readable($filename)) {
				echo "Files in parsing ...";
			} else {
				error("Files unreadable");
			}
		} else {
			error("Files doesn't exist");
		}
	}

	function read() {
		while($l = fgets(STDIN)){
		    Parser::ParseLine($l);
		}
	}
