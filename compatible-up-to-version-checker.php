<?php
 
/*
Plugin Name: Compatible Up To Version Checker
Version: 1.0.1
Author: Mark McEver
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_head', function(){
	echo "<script>
			BetterPluginCompatibility = {
				forEachPlugin: function(action){
					jQuery('#the-list tr').each(function(index, row){
						row = jQuery(row)
						var slug = row.data('slug')
						action(row, slug)
					})
				},
				showCompatibleUpToVersion: function(){
					jQuery('.better-plugin-compatibility-check-link').parent().html('Checking...')
					
					BetterPluginCompatibility.forEachPlugin(function(row, slug){
						jQuery.get(
							ajaxurl,
							{
								action: 'better_plugin_compatibility_get_plugin_info',
								slug: slug
							},
							function(data){
								if(slug != ''){
									data = JSON.parse(data)
									// console.log('done', slug, data)
									jQuery('#the-list tr[data-slug=' + slug + '] .better-plugin-compatibility-compatible-up-to-version').html(data.tested)
								}
							}
						)
					})
				}
			}

			jQuery(function(){
				BetterPluginCompatibility.forEachPlugin(function(row, slug){
					var versionText
					if(slug == ''){
						// Likely a custom plugin not on wordpress.org
						versionText = 'Not Found'
					}
					else if (jQuery('#' + slug + '-update').length > 0){
						versionText = 'Update Required'
					}
					else{
						versionText = \"<a class='better-plugin-compatibility-check-link' onclick='BetterPluginCompatibility.showCompatibleUpToVersion()'>Check</a>\"
					}

					row.find('.row-actions').after(\"<div>Compatible up to: <span class='better-plugin-compatibility-compatible-up-to-version'>\" + versionText + \"</span></div>\")
				})
			})
	      </script>";
});

add_action( 'wp_ajax_better_plugin_compatibility_get_plugin_info', function(){
	$ch = curl_init('http://api.wordpress.org/plugins/info/1.0/' . $_GET['slug']  . '.json');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	curl_close($ch);

	wp_send_json($data);
});

?>