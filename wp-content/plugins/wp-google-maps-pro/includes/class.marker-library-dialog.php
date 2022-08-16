<?php

namespace WPGMZA;

class MarkerLibraryDialog
{
	protected $icons;
	
	public function __construct(){
		wp_enqueue_script('remodal', plugin_dir_url(__DIR__) . 'lib/remodal.min.js', array('jquery'));
		wp_enqueue_style('remodal', plugin_dir_url(__DIR__) . 'lib/remodal.css');
		wp_enqueue_style('remodal-default-theme', plugin_dir_url(__DIR__) . 'lib/remodal-default-theme.css');
		
		wp_enqueue_style('wpgmza-marker-library-dialog', plugin_dir_url(__DIR__) . 'css/marker-library-dialog.css');
	}
	
	public function html(){
		ob_start();

		?>
		<div class="wpgmza-marker-library-dialog" style='display:none;'>
			<button data-remodal-action="close" class="remodal-close"></button>
			<span id="wpgmza-powered-by-mappity" style="float: right;">Powered by <a style="font-family: Roboto, sans-serif;" href="https://www.mappity.org">mappity.org</a></span>
			<iframe id="mappity" src=""></iframe>
		</div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
