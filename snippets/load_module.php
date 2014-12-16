<?php
	/* This is the beginning of the new development code for meerchat
		codebase */
	$module = "HelperServ";
	
	define("TEMP_DIR", "/home/ch/development/meerchat/snippets/temp/");

	Class mod_loader {
		public function load_code($module) {
			/* 
				module spec:
					
					Since it's still not reasonably possible to modify classes on the
					fly we will do it the old fashioned way.
					
					Class **${module}** {
						...
					}
					
					Replace the string **${module}** with a unique name, write to temp
					directory and then use include to import it. No using stupid eval()
			*/
	
			/* get ourselves a nice unique identifier for our module */
			$data = file_get_contents("$module.php");
			while (class_exists($module_identifier = $module."_".md5($module.'_'.time().'_'.rand(100,999))));
			$data = str_replace('**{module}**',$module_identifier,$data);
			file_put_contents(TEMP_DIR.$module_identifier.'.class.php', $data);
			require_once(TEMP_DIR.$module_identifier.'.class.php');
			$module_object = new $module_identifier;
			var_dump($module_object->__loaded());
			$module_object->OnJOIN("Morris", "#development", NULL);
			return($module_object);
		}
		
	}
	var_dump(mod_loader::load_code($module));
	
?>