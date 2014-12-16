<?php

Class **{module}** {
	static $cache = Array ();
	function __loaded () {
		return Array (
			'name'    => "HelperServ",
			'version' => "0.1",
			'author'  => "TheHypnotist",
			'date'    => "2013-2-27",
			'info'    => "This module is for official #help request management."
		);
	}
	function OnServerConnect ($a,$b,$c) {
	}
	function OnJOIN     ($source, $destination, $message='') {
		echo "OnJoin called with params: $source,$destination,$message.\n";
	}

}
