
<div class="wrap ig_settings">

<div class="icon32" id="icon-options-general"><br></div><h2><span class="icon-large icon-settings"></span>&nbsp;Injection Guard - Settings</h2>
<hr />
<?php echo $settings['notification']; $wpurl = get_bloginfo('wpurl'); ?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">


<div class="welcome-panel" id="request_panel">
<a class="welcome-panel-close dismiss_link">Dismiss</a>
<img class="flower_img" src="<?php echo plugin_dir_url(__FILE__); ?>/img/kindness.png">

		<h2><span class="promo">Show some love!</span></h2>
		<p>Want to appreciate the effort behind this plugin? <a target="_blank" class="button" href="http://www.websitedesignwebsitedevelopment.com/donate-now/" >Donate</a> $5, $10 or $50 now!</p>
       
        <p>Or you could:</p><ul><li><a href="http://wordpress.org/support/view/plugin-reviews/injection-guard" target="_blank">Rate this plugin 5 stars on WordPress.org</a></li></ul>	
</div>

<input type="hidden" name="ig_key" value="<?php echo $settings['ig_key']; ?>">

<div class="logs_area">
<div class="list_head">
<span class="icon-list-alt"></span><strong>Logged Requests</strong>
</div>
<ul>

<?php if(!empty($ig_logs)): ?>
<?php foreach($ig_logs as $log_head=>$params): ?>
	<li>
    	<span class="icon-flag"></span>
 
 		<?php echo $log_head.' ('.count($params).')'; ?>
		<?php if(!empty($params)): ?>
        <ul>
        <?php foreach($params as $params=>$param): ?>
            <li>
            	<div class="ig_params">
                <span class="icon-question-sign"></span><?php echo $param; ?>
               	</div>
                
                <div class="ig_actions" data-uri="<?php echo $log_head; ?>" data-val="<?php echo $param; ?>">
                
                <?php 
				$blacklisted = (isset($ig_blacklisted[$log_head]) && in_array($param, $ig_blacklisted[$log_head]));
				
				?>
                <a title="Click to whitelist" data-type="whitelist" class="<?php echo $blacklisted?'':'hide'; ?>"><span class="icon-thumbs-up"></span></a>
                
                <a title="Click to blacklist" data-type="blacklist" class="<?php echo $blacklisted?'hide':''; ?>"><span class="icon-thumbs-down"></span></a>
                </div>
            </li>
        <?php endforeach; ?>        
        </ul>
        <?php endif; ?>         
    </li>
<?php endforeach; ?>        
<?php endif; ?>    
</ul>
</div>



<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit"><a class="useful_link">Is it Good?</a></p></form>

</div>

<script type="text/javascript" language="javascript">

jQuery(document).ready(function($) {

	jQuery('.dismiss_link').click(function(){
		jQuery(this).parent().slideUp();
		jQuery('.useful_link').fadeIn();
	});
	jQuery('.useful_link').click(function(){
		jQuery('.dismiss_link').parent().slideDown();
		jQuery(this).fadeOut();
	});

	jQuery('.ig_actions a').die('click');
	jQuery('.ig_actions a').live('click', function(){
		var aClicked = jQuery(this);
		
		jQuery.post(ajaxurl, {action: 'ig_update','type':aClicked.attr('data-type'),'val':aClicked.parent().attr('data-val'), 'uri_index':aClicked.parent().attr('data-uri')}, function(response) {
			response = jQuery.parseJSON(response);
			
			if(response.status==true){
				
				aClicked.siblings().show();
				aClicked.hide();
			}
		});
	});

	//jQuery('.useful_link').click();


	// Find list items representing folders and
	// style them accordingly.  Also, turn them
	// into links that can expand/collapse the
	// tree leaf.
	$('.logs_area li > ul').each(function(i) {
		// Find this list's parent list item.
		var parent_li = $(this).parent('li');
	
		// Style the list item as folder.
		parent_li.addClass('folder');
	
		// Temporarily remove the list from the
		// parent list item, wrap the remaining
		// text in an anchor, then reattach it.
		var sub_ul = $(this).remove();
		parent_li.wrapInner('<a/>').find('a').click(function() {
			// Make the anchor toggle the leaf display.
	
		var options = {};
		sub_ul.toggle();// 'pulsate', options, 200 );
	
		});
		parent_li.append(sub_ul);
	});
	
	// Hide all lists except the outermost.
	$('.logs_area ul ul').hide();

});

</script>