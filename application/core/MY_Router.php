<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH . "third_party/MX/Router.php";

include_once APPPATH . "libraries/Route.php";

class MY_Router extends MX_Router {

	private $active_route;

	private $module_name;


	private function module_name(){
		if (!$this->module_name) {
			include APPPATH."config/config.php";
			$this->module_name = isset($config["system_version"]) ? $config["system_version"] : "v1";
		}
		return $this->module_name;
	}



	public function locate($segments)	{
		if (isset($_SERVER['REQUEST_URI']) && (strtok($_SERVER['REQUEST_URI'], '?') === "/" || strtok($_SERVER['REQUEST_URI'], '?') === "")) {
			return parent::locate($segments);
		} else {
			$this->module = $this->directory = "";
			$ext = $this->config->item("controller_suffix") . EXT;

			// Use module route if available
			if (isset($segments[0]) && $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
	    	$segments = $routes;
			}

			// Get the segments array elements
			list($module, $directory, $controller) = array_pad($segments, 3, NULL);

			// ------------------------------------------------------------------------
			// 1. Check modules (recursive)
			// ------------------------------------------------------------------------
			foreach (Modules::$locations as $location => $offset) {
				// Module controllers/ exists?
				if (is_dir($source = $location . $module . "/controllers/")) {
					$this->module    = $module;
					$this->directory = $offset . $module . "/controllers/";

					// Temporary helper variables
					$base_directory = $this->directory;
					$segments_copy  = array_slice($segments, 1);

					do {
						if (isset($segments_copy[0]) && $directory !== $segments_copy[0]) {
							$this->directory  = $base_directory . $directory . '/';
							$directory       .= '/' . $segments_copy[0];
						}

						$directory_string = substr($directory, 0,strrpos($directory, '/'));
						$pos = strrpos($directory, '/');
						$manual_directory_string = !$pos ? $directory : substr($directory, $pos + 1);
						if ($directory && is_file($source.strtolower($directory_string) . "/" . ucfirst($manual_directory_string).$ext)) {
	    				return $segments_copy;
						}

						// Move forward through the segments
						$segments_copy = array_slice($segments_copy, 1);

					}
					while ($segments_copy && $directory && is_dir($source . $directory . '/'));

					// Check for default module-named controller
					if (is_file($source . $module . $ext)) {
						$this->directory = $base_directory;
						return $segments;
					}
				}
			}


			// foreach
			// ------------------------------------------------------------------------
			// 2. Check app controllers in APPPATH/controllers/
			// ------------------------------------------------------------------------
			if (is_file(APPPATH . '../modules/' . $this->module_name() . '/controllers/' . $module . $ext)) {
				return $segments;
			}

			// Application sub-directory controller exists?
			if ($directory && is_file(APPPATH . '../modules/' . $this->module_name() . '/controllers/' . $module . '/' . $directory . $ext)) {
	    	$this->directory = $module . '/';
	    	return array_slice($segments, 1);
			}

			// ------------------------------------------------------------------------
			// 4. Check multilevel sub-directories in APPPATH/controllers/
			// ------------------------------------------------------------------------
			if ($directory) {
				$dir = '';
				do {
					// Go forward in segments to check for directories
					empty($dir) OR $dir .= '/';
					$dir .= $segments[0];
					// Update segments array
					$segments = array_slice($segments, 1);
				}
				while (count($segments) > 0 && is_dir(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $dir . '/' . $segments[0]));

				// Set the directory and remove it from the segments array
				$this->directory = str_replace('.', '', $dir) . '/';
				// If no controller found, use 'default_controller' as defined in 'config/routes.php'
				if (count($segments) > 0 && ! file_exists(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
		    	array_unshift($segments, $this->default_controller);
				} else if (empty($segments) && is_dir(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $this->directory)) {
		    	$segments = array($this->default_controller);
				}

				if (count($segments) > 0) {
	    	// Does the requested controller exist in the sub-folder?
	    		if ( ! file_exists(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
	        	$this->directory = '';
	    		}
				}

				if ($this->directory . $segments[0] != $module . '/' . $this->default_controller && count($segments) > 0 && file_exists(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $this->fetch_directory() . $segments[0] . EXT ) ) {
					return $segments;
				}
			}

			// ------------------------------------------------------------------------
			// 5. Check application sub-directory default controller
			// ------------------------------------------------------------------------
			if (is_file(APPPATH . '../modules/'.$this->module_name().'/controllers/' . $module . '/' . $this->default_controller . $ext))
			{
	    	$this->directory = $module . '/';
	    	return array($this->default_controller);
			}
		}

	}

	/**
	 * _set_routing
	 *
	 * Adds routes that are stored in the /application/routes/ directory
	 * then calls the usual _set_routing method
	 *
	 * @return	void
	 */
	public function _set_routing() {

		if (is_dir(APPPATH.'../modules/'.$this->module_name().'/routes')) {
			$file_list = scandir(APPPATH.'../modules/'.$this->module_name().'/routes');
			foreach($file_list as $file) {
				if (is_file(APPPATH.'../modules/'.$this->module_name().'/routes/'.$file) and pathinfo($file, PATHINFO_EXTENSION) == 'php') {
					include APPPATH.'../modules/'.$this->module_name().'/routes/'.$file;
				}
			}
		}
		parent::_set_routing();
	}



	/**
	 * Parse Routes
	 *
	 * Matches any routes that may exist in the config/routes.php file
	 * against the URI to determine if the class/method need to be remapped.
	 *
	 * @return	void
	 */
	public function _parse_routes()
	{
		// Turn the segment array into a URI string
		$uri = implode('/', $this->uri->segments);

		// Get HTTP verb
		$http_verb = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : 'cli';

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$uri]))
		{
			// Check default routes format
			if (is_string($this->routes[$uri]))
			{
				$this->_load_request_uri($uri);
				$this->_set_request(explode('/', $this->routes[$uri]));
				return;
			}
			// Is there a matching http verb?
			elseif (is_array($this->routes[$uri]) && isset($this->routes[$uri][$http_verb]))
			{
				$this->_load_request_uri($uri);
				$this->_set_request(explode('/', $this->routes[$uri][$http_verb]));
				return;
			}
		}

		// Loop through the route array looking for wildcards
		foreach ($this->routes as $key => $val)
		{
			// Check if route format is using http verb
			if (is_array($val))
			{
				if (isset($val[$http_verb]))
				{
					$val = $val[$http_verb];
				}
				else
				{
					continue;
				}
			}

			//we have to keep the original key because we will have to use it
			//to recover the route again
			$original_key = $key;
			// Convert wildcards to RegEx
			$key = str_replace(array(':any', ':num'), array('[^/]+', '[0-9]+'), $key);

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $uri, $matches))
			{
				// Are we using callbacks to process back-references?
				if ( ! is_string($val) && is_callable($val))
				{
					// Remove the original string from the matches array.
					array_shift($matches);

					// Execute the callback using the values in matches as its parameters.
					$val = call_user_func_array($val, $matches);
				}
				// Are we using the default routing method for back-references?
				elseif (strpos($val, '$') !== FALSE && strpos($key, '(') !== FALSE)
				{
					$val = preg_replace('#^'.$key.'$#', $val, $uri);
				}
				$this->_load_request_uri($original_key);

				$this->_set_request(explode('/', $val));
				return;
			}
		}

		show_404();
	}


	private function _load_request_uri($uri)
	{
		$this->active_route = $uri;
		$this->uri->load_uri_parameters($uri);
	}

	public function get_active_route()
	{
		return $this->active_route;
	}

}
