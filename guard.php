<?php

####################################
###### INJECTION GUARD CLASS #######
####################################
############### BY #################
####################################
##### FAHAD@ANDROIDBUBBLES.COM #####
####################################

interface guard_base{
	public function init();
	public function update_log();
	public function get_requests_log();
	public function get_requests_log_updated($var=array());
	public function get_blacklisted();
}	
class guard_plugins implements guard_base{
	
	protected $request;
	protected $request_uri;
	protected $request_uri_cleaned;
	protected $param_to_blacklist;
	protected $blacklist_action;
	
	public function init(){
		$this->request = $_REQUEST;
		$this->request_uri = $_SERVER['REQUEST_URI'];
		$this->query_string = $_SERVER['QUERY_STRING'];
		$this->request_uri_cleaned = $this->cleaned_uri();
	}
	
	public function update_log(){ 
		//WILL DIFFER IN WP, JOOMLA and DRUPAL etc.
		
	}
	
	public function get_blacklisted(){
		
	}
	
	private function cleaned_uri(){
		$ret = $this->request_uri;
		$temp_request_uri = str_replace($this->query_string, '', $ret);
		$temp_request_uri = str_replace('?', '', $temp_request_uri);
		$request_uri = explode('/', $temp_request_uri);
		$request_uri = array_filter($request_uri, 'strlen');
		if(!empty($request_uri)){
			$ret = implode('/', $request_uri);
		}else{
			$ret = '/';
		}
		return $ret;
	}
	
	public function get_requests_log(){
		
	}
	
	public function get_requests_log_updated($updated_log=array()){
		
		if(empty($updated_log)){
			$updated_log = array();
		}
		
		$updated_log[$this->request_uri_cleaned] = isset($updated_log[$this->request_uri_cleaned])?$updated_log[$this->request_uri_cleaned]:array();
		
		
		parse_str($this->query_string, $updated_log_temp);
		$updated_log[$this->request_uri_cleaned] = array_merge($updated_log[$this->request_uri_cleaned], array_keys($updated_log_temp));
		$updated_log[$this->request_uri_cleaned] = array_unique($updated_log[$this->request_uri_cleaned]);
		
		return $updated_log;		
	}
	
	public function get_blacklisted_updated($blacklisted=array(), $uri_index){
		
		if(empty($blacklisted)){
			$blacklisted = array();
		}
		
		$blacklisted[$uri_index] = isset($blacklisted[$uri_index])?$blacklisted[$uri_index]:array();
		
		if($this->blacklist_action){
			if(!in_array($this->param_to_blacklist, $blacklisted[$uri_index])){
				$blacklisted[$uri_index][]=$this->param_to_blacklist;
			}
		}else{
			
			if(in_array($this->param_to_blacklist, $blacklisted[$uri_index])){
				if (($key = array_search($this->param_to_blacklist, $blacklisted[$uri_index])) !== false){
					
					unset($blacklisted[$uri_index][$key]);			
				}
			}
		}
		
		
		
		$blacklisted[$uri_index] = array_unique($blacklisted[$uri_index]);
		
		return $blacklisted;	
	}
}
class guard_wordpress extends guard_plugins{
	
	public function update_log(){
		
		$updated_log = $this->get_requests_log();
		
		

		$updated_log = $this->get_requests_log_updated($updated_log);
		
		update_option( 'ig_requests_log', $updated_log );
		
	}
	
	public function update_blacklisted($val, $uri_index, $block_this=true){
		
		$updated_bl = $this->get_blacklisted();
		
		
		
		$this->param_to_blacklist = $val;
		$this->blacklist_action = $block_this;
						
		$updated_bl = $this->get_blacklisted_updated($updated_bl, $uri_index);
		
		update_option( 'ig_blacklisted', $updated_bl );
		
	}	
	
	public function get_requests_log(){
		return get_option('ig_requests_log');
	}
	
	public function get_blacklisted(){
		return get_option('ig_blacklisted');
	}
	
	public function wp_uri_cleaned(){
		return $this->request_uri_cleaned;
	}
	
	public function available_uri_strings(){
		$ret = array();
		
		if($this->query_string!='')
		parse_str($this->query_string, $ret);
		
		$ret = !empty($ret)?array_keys($ret):$ret;
		
		return $ret;
	}
}
?>