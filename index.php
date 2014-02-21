<?php 

/*

Plugin Name: Injection Guard

Plugin URI: http://www.websitedesignwebsitedevelopment.com/wordpress/plugins/injection-guard

Description: This plugin will block all unauthorized and irrelevant requests through query strings by redirecting them to an appropriate error page instead of generating identical results for it.

Version: 1.0

Author: Fahad Mahmood 

Author URI: http://www.androidbubbles.com

License: GPL3

*/ 

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	include('functions.php');


	global $ig_logs;
	global $ig_blacklisted;
    global $ig_rs;	
	
	
	$ig_rs = array();      
	$ig_rs[] = '<a target="_blank" href="plugin-install.php?tab=search&s=wp+mechanic&plugin-search-input=Search+Plugins">Install WP Mechanic</a>';
	$ig_rs[] = '<a target="_blank" href="http://www.websitedesignwebsitedevelopment.com/contact">Contact Developer</a>';
        
	
	
	function ig_menu(){

		 add_options_page('Injection Guard', 'IG Settings', 'update_core', 'ig_settings', 'ig_settings');
		

	}
	
	
	function ig_settings() { 

		if ( !current_user_can( 'update_core' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
 			
		}

		global $ig_logs, $ig_blacklisted;
		$guard_obj = new guard_wordpress;
		$ig_blacklisted = $guard_obj->get_blacklisted();
		$ig_logs = $guard_obj->get_requests_log();
		
		if(is_array($ig_logs) && !empty($ig_logs))
		ksort($ig_logs);
		
		$blog_info = get_bloginfo('admin_email');

		$salt = date('YmddmY')+date('m');

		//DEFAULT BACKUP RECIPIENT EMAIL ADDRESS	
		$default_email = get_bloginfo('admin_email');
		
		$default_email = $default_email!=''?$default_email:'info@'.str_replace('www.','',$_SERVER['HTTP_HOST']); 

		
		include('ig_settings.php');			

	}	
	
	
	
	function register_ig_styles($hook_suffix) {
		
		if($hook_suffix!='settings_page_ig_settings')
		return false;
		
		wp_register_style( 'ig-style', plugins_url('css/style.css', __FILE__) );
		
		
		
		wp_register_style( 'ig-bsm', plugins_url('css/bootstrap.min.css', __FILE__) );
		wp_register_style( 'ig-bsr', plugins_url('css/bootstrap-responsive.min.css', __FILE__) );
		wp_register_style( 'ig-bsi', plugins_url('css/bootstrap.icon-large.min.css', __FILE__) );
		
		wp_enqueue_style( 'ig-style' );
		wp_enqueue_style('ig-bsm');
		wp_enqueue_style('ig-bsr');
		wp_enqueue_style('ig-bsi');
				

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script("jquery-effects-core");
		
		wp_enqueue_script(
			'bootstrap.min',
			plugins_url('js/bootstrap.min.js', __FILE__)
		);		
	}
	


	
	if(is_admin()){
		add_action( 'admin_menu', 'ig_menu' );	

		add_action( 'admin_enqueue_scripts', 'register_ig_styles' );
		
		add_action( 'wp_ajax_ig_update', 'ig_update' );
		
	}else{
		//ACTION TIME
		add_action('init', 'ig_start', 1);	
	}