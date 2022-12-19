<?php

namespace WPGMZA\CustomFieldFilterWidget;

require_once(plugin_dir_path(__DIR__) . 'custom-fields/class.custom-field-filter-widget.php');

class Date extends \WPGMZA\CustomFieldFilterWidget
{
	public function __construct($filter)
	{
		\WPGMZA\CustomFieldFilterWidget::__construct($filter);
	}
	
	public function html()
	{
		$attributes = $this->getAttributesString();
		ob_start();
		?>
		<input type='date'
			<?php echo $attributes; ?> data-date-start='true'
			placeholder='<?php echo htmlspecialchars($this->filter->getFieldData()->name); ?>'
			/>
			<?php _e( 'to', 'wp-google-maps' ); ?>
		<input type='date'
			<?php echo $attributes; ?> data-date-end='true'
			placeholder='<?php echo htmlspecialchars($this->filter->getFieldData()->name); ?>'
			/>
		<?php
		$html = ob_get_clean();
		return $html;
	}
}