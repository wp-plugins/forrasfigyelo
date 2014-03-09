<?php
/**
 * @package forrasfigyelo
 */
/*
Plugin Name: Forrásfigyelő CSR
Plugin URI: http://forrasfigyelo.hu/?page=letoltesek
Version: 1.0
Description: Tartalom-érzékeny, automatikusan frissülő ajánlások a <strong>forrásfigyelő.hu</strong> pályázati híreiből. A pályázatok az Ön weboldalának tartalmához illeszkedve jelennek meg, így Ön célzott pályázatokat tud ajánlani látogatóinak.
Author: e-presence, Bliszkó Viktor
Author URI: http://www.e-presence.hu
*/

class forrasfigyelo_widget extends WP_Widget {

	const FF_TYPE_KERESES = 1;
	const FF_TYPE_AJANLAS = 2;
	const FF_TYPE_MINDKETTO = 3;
	const FF_WIDTH_MIN = 152;
	const FF_HEIGHT_KERESES = 117;
	const FF_HEIGHT_AJANLAS = 200;
	const FF_HEIGHT_MINDKETTO = 200;

	private $default_type;
	private $default_width;
	private $default_height;

	public function __construct() {
		parent::__construct(
			'forrasfigyelo',
			'Forrásfigyelő CSR',
			array('description' => 'Tartalom-érzékeny, automatikusan frissülő ajánlások a forrásfigyelő.hu pályázati híreiből.')
		);
		$this->default_type = self::FF_TYPE_AJANLAS;
		$this->default_width = self::FF_WIDTH_MIN;
		$this->default_height = self::FF_HEIGHT_AJANLAS;
	}

	private function filter_type($type) {
		$type = (int)$type;
		if (!in_array($type, array(self::FF_TYPE_AJANLAS, self::FF_TYPE_KERESES, self::FF_TYPE_MINDKETTO))) {
			$type = self::FF_TYPE_AJANLAS;
		}
		return $type;
	}

	private function filter_width($width) {
		$width = (int)$width;
		if ($width < self::FF_WIDTH_MIN) {
			$width = self::FF_WIDTH_MIN;
		}
		return $width;
	}

	private function filter_height($height, $type) {
		$height = (int)$height;
		$default_height = array(
			self::FF_TYPE_KERESES 	=> self::FF_HEIGHT_KERESES,
			self::FF_TYPE_AJANLAS 	=> self::FF_HEIGHT_AJANLAS,
			self::FF_TYPE_MINDKETTO	=> self::FF_HEIGHT_MINDKETTO);
		if ($height < $default_height[$type]) {
			$height = $default_height[$type];
		}
		return $height;
	}

	public function widget($args, $instance) {

		$ff_type = $this->filter_type($instance['ff_type']);
		$ff_width = $this->filter_width($instance['ff_width']);
		$ff_height = $this->filter_height($instance['ff_height'], $ff_type);

		echo $args['before_widget'];

		echo "<script src=\"http://forrasfigyelo.hu/forrasfigyelo.js\" type=\"text/javascript\"></script>";
		echo "<script type=\"text/javascript\">try{ffigyelo.keret({t:'" . $ff_type . "',w:'" . $ff_width . "',h:'" . $ff_height . "'});}catch(evt){alert(evt.message)}</script>";

		echo $args['after_widget'];
	}

	public function form($instance) {

		$ff_type = $instance['ff_type'];
		$ff_width = $instance['ff_width'];
		$ff_height = $instance['ff_height'];

		echo "<p>";
		echo "<label for=\"" , $this->get_field_id('ff_type'), "\">Tartalom: </label>";
	    echo "<select name=\"", $this->get_field_name('ff_type'), "\" id=\"", $this->get_field_id('ff_type'), "\" class=\"widefat\" style=\"width:auto\">";
		echo "<option value=\"" . self::FF_TYPE_AJANLAS . "\"";
		if (esc_attr($ff_type) == self::FF_TYPE_AJANLAS) {
			echo " selected=\"selected\"";
		}
		echo ">Ajánlás</option>";

		echo "<option value=\"" . self::FF_TYPE_KERESES . "\"";
		if (esc_attr($ff_type) == self::FF_TYPE_KERESES) {
			echo " selected=\"selected\"";
		}
		echo ">Keresés</option>";

		echo "<option value=\"" . self::FF_TYPE_MINDKETTO . "\"";
		if (esc_attr($ff_type) == self::FF_TYPE_MINDKETTO) {
			echo " selected=\"selected\"";
		}
		echo ">Mindkettő</option>";

	    echo "</select>";
		echo "</p>";

		echo "<p>";
		echo "<label for=\"", $this->get_field_id('ff_width'), "\">Szélesség: </label>";
		echo "<input class=\"widefat\" id=\"", $this->get_field_id('ff_width'), "\" name=\"", $this->get_field_name('ff_width'), "\" type=\"text\" value=\"", esc_attr($ff_width), "\" />";
		echo "</p>";

		echo "<p>";
		echo "<label for=\"", $this->get_field_id('ff_height'), "\">Magasság: </label>";
		echo "<input class=\"widefat\" id=\"", $this->get_field_id('ff_height'), "\" name=\"", $this->get_field_name('ff_height'), "\" type=\"text\" value=\"", esc_attr($ff_height), "\" />";
		echo "</p>";

		echo "<p>A widget külső hivatkozásokat használ, amihez a widget használatával hozzájárulok. <input type=\"checkbox\" checked=\"checked\" disabled=\"disabled\"></p>";

	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		if (!isset($instance['ff_type'])) {
			$instance['ff_type'] = $this->default_type;
		} else {
			$instance['ff_type'] = $new_instance['ff_type'];
		}
		$instance['ff_type'] = $this->filter_type($instance['ff_type']);

		if (!isset($instance['ff_width'])) {
			$instance['ff_width'] = $this->default_width;
		} else {
			$instance['ff_width'] = $new_instance['ff_width'];
		}
		$instance['ff_width'] = $this->filter_width($instance['ff_width']);

		if (!isset($instance['ff_height'])) {
			$instance['ff_height'] = $this->default_height;
		} else {
			$instance['ff_height'] = $new_instance['ff_height'];
		}
		$instance['ff_height'] = $this->filter_height($instance['ff_height'], $instance['ff_type']);

		return $instance;
	}

}

function forrasfigyelo_load_widget() {
	register_widget('forrasfigyelo_widget');
}

add_action('widgets_init', 'forrasfigyelo_load_widget');

?>