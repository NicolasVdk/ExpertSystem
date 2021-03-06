<?php

	include_once ("parser.php");
	include_once ("resolution.php");

	global $verbose;
	$verbose = false;

	if ($argc > 2 && $argv[1] !== '-v')
		error("Too much argument");
	else if ($argc == 3 && $argv[1] === '-v') {
		$verbose = true;
		files($argv[2]);
	} else if ($argc == 2 && $argv[1] !== '-v')
		files($argv[1]);
	else
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
		resolve(true);
	}

	function read() {
		$restart = false;
		while($l = fgets(STDIN)){
		    if ($l !== PHP_EOL)
		    	Parser::ParseLine($l);
		    else {
		    	$restart = true;
		    	break;
		    }
		}
		resolve($restart);
	}

	function replay() {
		echo "Change a fact ?".PHP_EOL;
		$restart = false;
		while($l = fgets(STDIN)){
			if ($l !== PHP_EOL && $l !== 'exit'.PHP_EOL)
		    	Parser::ParseLine($l);
		    else if ($l === 'exit'.PHP_EOL) {
		    	exit();
		    } else {
		    	$restart = true;
		    	break;
		    }
		}
		resolve($restart);
	}

	function resolve($restart) {
		global $verbose;
		$p = Parser::singleton();
		new Resolution($p->condition, $p->affected, $p->rpndata, $p->variablestates, $p->search, $verbose);
		if ($restart)
			replay();
	}
