<?php require_once('guard.php');

	
	
	//FOR QUICK DEBUGGING


	if(!function_exists('pre')){

		function pre($data){

			echo '<pre>';

			print_r($data);

			echo '</pre>';	

		}	 

	}





	if(!function_exists('ig_start')){


		function ig_start(){	
				
				$guard_obj = new guard_wordpress;
				$guard_obj->init();
				$guard_obj->update_log();
				$ig_logs = $guard_obj->get_requests_log();
				$ig_blacklisted = $guard_obj->get_blacklisted();
				$uri = $guard_obj->wp_uri_cleaned();
				$aus = $guard_obj->available_uri_strings();
				
				
				if(isset($ig_blacklisted[$uri]))
				{
					$diff = array_intersect($ig_blacklisted[$uri], $aus);
					
					if(!empty($diff)){
					global $wp_query;
					$wp_query->set_404();
					status_header( 404 );
					get_template_part( 404 ); 
					exit();
					}
				}

		}	


	}
	

	if(!function_exists('ig_update')){
		function ig_update(){	
		
			$ret = array('status'=>true);
			
			$val = esc_attr($_POST['val']);
			$type = esc_attr($_POST['type']);
			$uri = esc_attr($_POST['uri_index']);
			
			$guard_obj = new guard_wordpress;
			
			if($type=='whitelist'){
				$guard_obj->update_blacklisted($val, $uri, false);
			}else{
				$guard_obj->update_blacklisted($val, $uri, true);
			}
			echo json_encode($ret);
			exit;
		}
	}
	
?>