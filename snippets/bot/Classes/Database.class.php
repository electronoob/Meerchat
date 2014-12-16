<?php

Class Database {
	private static $database;
	private static $engine;
	private static $prefix;
	function init () {
		self::$engine = Config::get_parameter_pair ('database_engine');
		if (self::$engine == "MySQL") {
			$hostname = Config::get_parameter_pair ('database_hostname');
			$username = Config::get_parameter_pair ('database_username');
			$database = Config::get_parameter_pair ('database_database');
			$password = Config::get_parameter_pair ('database_password');
			//$db = new MySQL('localhost', 'Joomla', 'cfczYJnMU6L9xqRJ', 'Joomla');
			$db = new MySQL ($hostname, $username, $password, $database);
			self::set_database ($db);
			$prefix = Config::get_parameter_pair ('database_joomla_prefix');
			self::set_prefix ( $prefix );
			//self::test_database ();
		}
	}
	function heartbeat () {
		$db = self::get_database ();
		/* check if server is alive */
		if ($db->ping()) {
			//printf ("SQL Server still connected\n");
		} else {
			Protocol::send_privmsg ("Voyager", "#staff", " [DEBUG] I have lost connection to MySQL server.\n");
			printf ("Error: %s\n", $mysqli->error);
			die("\nDue to error regarding SQL teminating session.\n");
		}
	}
	function set_database ( $db ) {
		self::$database = $db;
	}
	function get_database ( ) {
		return self::$database;
	}
	function set_prefix ( $prefix ) {
		self::$prefix = $prefix;
	}
	function get_prefix () {
		return self::$prefix;
	}
	function check_password ($snick, $passkey) {
		$user = self::qdb_user ($snick);
		if ($user) {
			if ($user['block'] == '1') {
				return 2;	
			}
			//var_dump($user);
			/* 
			 * this is the old mode */
//			$password = explode (':', $user['password']);
//			$salt = $password[1];
//			$password = $password[0];
//			$saltypasskey = $passkey.$salt;
//			$md5_saltypasskey = md5($saltypasskey);

//			if ($md5_saltypasskey == $password) {
//				return 1;
//			} else {
				// if regular password failed to verify try to compare against salted md5 in the db
//				If ($password == $passkey) return 1;
//				return 0;
//			}

			$answer = password_verify($passkey, $user['password']);
			return $answer;
		} else {
				return 3;
		}
	}

	function qdb_user_rank ( $username ) {
                $db = self::get_database ();
                $db_prefix = self::get_prefix ();

		$user = self::qdb_user($username);
		// now we have the user data, therefore the id number, we can query that against the lookup table for staff
		
		if ($result = $db->query ("SELECT SQL_NO_CACHE * FROM `".$db_prefix."user_usergroup_map` WHERE `user_id` = '".$user['id']."' LIMIT 1")) {
			$map = $result->fetch_assoc ();
			$result->close();
                } else {
                        return false;
                }
		return $map['group_id'];
	}
	function qdb_user($username) {
        $db = self::get_database ();
        $db_prefix = self::get_prefix ();
        if ($result = $db->query ("/*qc=off*/ SELECT SQL_NO_CACHE * FROM `".$db_prefix."users` WHERE `username` = '".$username."' LIMIT 1")) {
                $user = $result->fetch_assoc ();
                /* free result set */
                $result->close();
		} else {
			return false;
		}
		return $user;
	}
	function qdb_ban_user ($username) {
        $db = self::get_database ();
        $db_prefix = self::get_prefix ();
		if ($user = self::qdb_user($username)) {
			if ($result = $db->query ("UPDATE `".$db_prefix."users` SET `block` = '1' WHERE `id` = '".$user['id']."'")) {
				if ($result = $db->query ("UPDATE `".$db_prefix."user_usergroup_map` SET `group_id` = '10' WHERE `user_id` = '".$user['id']."'")) {
					return 1;
				}else{
					return $db->error;
				}
				
			} else {
				return $db->error;
			}
		} else {
			return false;
		}
	}
	function qdb_unban_user ($username) {
        $db = self::get_database ();
        $db_prefix = self::get_prefix ();
		if ($user = self::qdb_user($username)) {
			if ($result = $db->query ("UPDATE `".$db_prefix."users` SET `block` = '0' WHERE `id` = '".$user['id']."'")) {
				if ($result = $db->query ("UPDATE `".$db_prefix."user_usergroup_map` SET `group_id` = '2' WHERE `user_id` = '".$user['id']."'")) {
					return 1;
				}else{
					return $db->error;
				}
				
			} else {
				return $db->error;
			}
		} else {
			return false;
		}
	}
	function qdb_user_community ($username) {
                $db = self::get_database ();
                $db_prefix = self::get_prefix ();
		$user = self::qdb_user($username);
		$user_community = array();
                if ($result = $db->query ("SELECT SQL_NO_CACHE * FROM `".$db_prefix."community_fields_values` WHERE `user_id` = '".$user['id']."' LIMIT 30")) {
			while ($row = $result->fetch_assoc()) {
				$user_community[$row['field_id']] = $row['value'];
			}
                        /* free result set */
                        $result->close();
                } else {
                        return false;
                }
                return $user_community;
        }

	function test_database () {
		$db = self::get_database ();
		$db_prefix = self::get_prefix ();
		if ($result = $db->query ("SELECT SQL_NO_CACHE * FROM `".$db_prefix."users` LIMIT 1")) {
			printf ("Select returned %d rows.\n", $result->num_rows);
			while ( $row = $result->fetch_assoc () )
			{
				return 1;
			}
			/* free result set */
			$result->close();
		} else {
			die("Unable to search database for it's users. There's a configuration problem.");
		}
	}
	// begin LogServ methods
	function insert ($table, $assoc) {
		$db = self::get_database ();
		$ariel_prefix = Config::get_parameter_pair ('database_ariel_prefix');
		foreach ($assoc as $key => $value) {
			$keys[]   = $db->real_escape_string ($key);
			$values[] = $db->real_escape_string ($value);
		}
		$compiled_keys   = "`".implode ("`,`" ,   $keys)."`";
		$compiled_values = "'".implode ("','" ,   $values)."'";
		$table = $ariel_prefix . $table;
		$query = "INSERT INTO `$table` ($compiled_keys) VALUES ($compiled_values)";
		$result = $db->query ($query);
		if ($result  === TRUE) {
			$db->commit ();
			return 1;
		} else {
			//echo "unable to insert\n";
			printf("Errormessage: %s\n", $db->error);
			return 0;
		}
	}

        function select ($table, $assoc) {
		$db = self::get_database ();
		$ariel_prefix = Config::get_parameter_pair ('database_ariel_prefix');
                foreach ($assoc as $key => $value) {
                        $keys[]   = $db->real_escape_string ($key);
                        $values[] = $db->real_escape_string ($value);
                }
                $table = $ariel_prefix . $table;
		//$query = "INSERT INTO `$table` ($compiled_keys) VALUES ($compiled_values)";
		//                        'phptime'      => strtotime("-15 minute"),
                //		        'destination'  => $destination,
		$timestamp = $assoc['phptime'];
		$channel = $assoc['destination'];
		$source = $assoc['source'];
		$query = "SELECT SQL_NO_CACHE `id`, `sqltimestamp`, `phptime`, `source`, `target`, `destination`, `message`, `event` FROM  `$table` WHERE  `phptime` > $timestamp AND  `destination` LIKE  '$channel' ORDER BY  `$table`.`id` ASC ";
		$data_array = array();
                if ($result = $db->query ($query)) {
                        while($row = $result->fetch_assoc()) {
                                $data_array[] = $row;
                        }
                        /* free result set */
                        $result->close();
                } else {
                        return false;
                }
                return $data_array;
	}
}
