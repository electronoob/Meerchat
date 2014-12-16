<?php

Class **{module}** {
	static $cache = Array ();
	function __loaded () {
		$this->cache[] = Array('__loaded', TRUE);
		return Array (
			'name'    => "HelperServ",
			'version' => "0.6",
			'author'  => "TheHypnotist",
			'date'    => "2013-2-27,2014-10-27",
			'info'    => "This module is for official #dev request management."
		);
	}
	function OnServerConnect ($a,$b,$c) {
		Protocol::send_raw("JOIN #dev\nPRIVMSG #dev :Hello, World.\n");
		$this->OnPRIVMSG ('m','#dev',':!reload');
		
	}
	function OnJOIN     ($source, $destination, $message='') {
		//echo "OnJoin called with params: $source,$destination,$message.\n";
		//Protocol::send_raw("PRIVMSG #dev :Welcome aboard $source!\n");
	}
	function OnPRIVMSG     ($source, $destination, $message='') {
		//Module::load ("HelperServ");
		if ($message == ':!reload') {
			$info = $this->__loaded();
			$version = $info["version"];
			Protocol::send_raw("PRIVMSG #dev :*** attempting to reload my module, current version $version.\n");
			Module::reload ("HelperServ");
		}
		if ($message == ":!version") {
			$info = $this->__loaded();
			$version = $info["version"];
			$name = $info['name'];
			Protocol::send_raw("PRIVMSG #dev :*** $name $version\n");
		}
		/* crypto test */
		$tokens = explode(' ',$message);
		if($tokens[0] == ':!bcrypt') {
			if (sizeof($tokens) < 2) {
				Protocol::send_raw("PRIVMSG #dev :** insufficient params - !bcrypt password\n");
			} else {
				$hash = '$2y$10$VOXvpaNIzM779AqqHV2WOOAKh/JoTG9hkUgv42HJVcu2RxxEg4OSS';
				if (password_verify($tokens[1], $hash)) {
					Protocol::send_raw("PRIVMSG #dev :** Woohoo correct password you elite hacker you!\n");
				} else {
					Protocol::send_raw("PRIVMSG #dev :** Invalid password for $hash\n");
				}
			}
		}
		if ($tokens[0] == ":!ban") {
			 if (sizeof($tokens) < 2) {
			 	Protocol::send_raw("PRIVMSG #dev :** insufficient params - !ban username\n");
			 } else {
			 	$temp_user = Database::qdb_user($tokens[1]);
				if ($temp_user) {
					$result = Database::qdb_ban_user ($temp_user['username']);
					if ($result === 1) {
						Protocol::send_raw("PRIVMSG #dev :** banned.\n");
					}else{
						Protocol::send_raw("PRIVMSG #dev :** error: $result\n");
					}
				} else {
					Protocol::send_raw("PRIVMSG #dev :** user does not exist\n");
				}
			 }
		}
		if ($tokens[0] == ":!unban") {
			 if (sizeof($tokens) < 2) {
			 	Protocol::send_raw("PRIVMSG #dev :** insufficient params - !unban username\n");
			 } else {
			 	$temp_user = Database::qdb_user($tokens[1]);
				if ($temp_user) {
					$result = Database::qdb_unban_user ($temp_user['username']);
					if ($result === 1) {
						Protocol::send_raw("PRIVMSG #dev :** unbanned.\n");
					}else{
						Protocol::send_raw("PRIVMSG #dev :** error: $result\n");
					}
				} else {
					Protocol::send_raw("PRIVMSG #dev :** user does not exist\n");
				}
			 }
		}
		if ($tokens[0] == ":!db") {
			if (sizeof($tokens) < 3) {
				Protocol::send_raw("PRIVMSG #dev :** insufficient params - !db username password\n");
			} else {
				$result_temp = Database::check_password($tokens[1], $tokens[2]);

				if ($result_temp == 0) {
					Protocol::send_raw("PRIVMSG #dev :** error wrong password.\n");
				}
				if ($result_temp == 1) {
					$rank = Database::qdb_user_rank ( $tokens[1] );
					$nameserv_ranks = Config::get_parameter_pair ('nameserv_ranks');
					Protocol::send_raw("PRIVMSG #dev :** Correct password (".  $nameserv_ranks[$rank]  .")\n");
				}
				if ($result_temp === 2) {
						Protocol::send_raw("PRIVMSG #dev :** warning: user is suspended.\n");
				}
				if ($result_temp === 3) {
						Protocol::send_raw("PRIVMSG #dev :** unknown user.\n");
				}
			}
		}
	}
	function set_cache ($cache) {
		$this->cache = $cache;
	}
	function get_cache () {
		//var_dump($this->cache);
		return $this->cache;
	}
}
