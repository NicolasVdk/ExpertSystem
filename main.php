<?php

	include_once ("parser.php");

	if ($argc > 2)
		error("Too much argument");	
	else if ($argc == 2 && $argv[1] == "-d") {
		define("DEBUG", true);
	} else if ($argc == 2)
		files($argv[1]);
	else if ($argc == 3 && $argv[1] == "-d") {
		files($argv[2]);
		define("DEBUG", true);
	}
	read();

	function error($error = null, $line = 0) {
		echo ($error === null ? "Unkown error occured" : $error) . ($line > 0 ? " Line : " . $line : "") . "." . PHP_EOL;
		exit (1);
	}

	function files($filename) {
		if (file_exists($filename)) {
			if (is_readable($filename)) {
				$fd = fopen($filename, "r");
				if ($fd) {
				    while (($l = fgets($fd)) !== false) {
				        Parser::ParseLine($l);
				    }
				    fclose($fd);
				} else {
				    error("Files open errors");
				}
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
