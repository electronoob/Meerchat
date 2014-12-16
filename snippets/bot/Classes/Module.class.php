<?php
/* hard coded for tonight*/

define("TEMP_DIR", "/home/ch/development/meerchat/snippets/bot/temp/");

Class Module {
	private static $modules = array();
	function __construct () {}
	private static function loadModule($module) {
		// only loads one time, not ready for rehash of code
		/* its time for an update to add rehash of code */
		
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
		//require_once "./Modules/$mod/$mod.class.php";
		/* get ourselves a nice unique identifier for our module */
		$data = file_get_contents("./Modules/$module/$module.class.php");
		while (class_exists($module_identifier = $module."_".md5($module.'_'.time().'_'.rand(100,999))));
		$data = str_replace('**{module}**',$module_identifier,$data);
		file_put_contents(TEMP_DIR.$module_identifier.'.class.php', $data);
		require_once(TEMP_DIR.$module_identifier.'.class.php');
		$service = new $module_identifier;

		return $service;
	}
	public function load ($module) {
		$service = self::loadModule($module);
		$meta = $service->__loaded();
		self::addModule ($module, $service, $meta);
		return $meta;
	}
	public function reload($module){
		//self::$modules
		$service = self::loadModule($module);
		$meta = $service->__loaded();
		$id = self::getModule($module);
		$temp_cache = self::$modules[$id]['instance']->get_cache();
		/* copy over the old cache to the new iteration of code */
		$service->set_cache($temp_cache);
		self::delModule($module);
		self::replaceModule ($id, $module, $service, $meta);
		return $meta;
	}
	private static function addModule ($name, $instance, $meta) {
		$object = array (
			'name' => $name,
			'instance' => $instance,
			'meta' => $meta
		);
		// no checking for rehashed code
		self::$modules[] = $object;
	}
	private static function replaceModule ($index, $name, $instance, $meta) {
		$object = array (
			'name' => $name,
			'instance' => $instance,
			'meta' => $meta
		);
		self::$modules[$index] = $object;
	}
	public function delModule ($module) {
		foreach (self::$modules as $index => $module) {
			if ($module['name'] == $module) {
				unset(self::$modules[$index]);
				return 1;
			}
		}
		return 0;
	}
	public function getModule ($module) {
		foreach (self::$modules as $index => $loaded_module) {
			if ($loaded_module['name'] == $module) {
				return $index;
			}
		}
		return -1;
	}
	public function list_modules () {
		$list = Array();
		foreach (self::$modules as $module) {
			$list[] = $module['name'] ." - ". $module['meta']['version'];
		}
		return $list;
	}
	public function dispatch_event ($event, $source, $destination, $message='') {
		// I should really stop reusing variables. It caused me a stupid bug.
		$event = "On" . $event;
		foreach (self::$modules as $module) {
			if (method_exists ($module['instance'], $event)) {
				$module['instance']->$event($source, $destination, $message);
			} else {

			}
		}
	}
}
