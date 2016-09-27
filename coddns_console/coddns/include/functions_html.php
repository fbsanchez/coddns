<?php

/**
 * Expands all attributes defined in a hash into a string
 * @param  hash $attributes hashed attributes 'attribute' => 'value'
 * @return string           string transformation
 */
function extract_hashed_attributes($attributes){
	$out = "";
	foreach ($attributes as $attr => $value) {
		$out .= " $attr='" . $value . "'";
	}
	return $out;
}

/**
 * Transforms a hash into an html form
 * @param  hash  $form  hash defined as:
 *   $form["attr"]["method"] = "POST";
 *   $form["attr"]["action"] = "#";
 *   $form["inputs"]["nombre"]["id"]   = "id for input ";
 *   $form["inputs"]["nombre"]["name"] = "";
 *   $form["inputs"]["nombre"]["type"] = "text";
 *   $form["inputs"]["nombre"]["labeled"] = 1 or 0 <- enables or disables label
 * @param  boolean $print dump to output or not
 * @return string         returns the generated form (text)
 */
function html_form($form, $print = true) {
	$out = "<form" . extract_hashed_attributes($form["attr"]) . ">";
	$out .= "<ul>";
	$inputs = $form["inputs"];

	foreach ($inputs as $label => $input) {
		if ($input["type"] != "hidden") {
			$out .= "<li>";
			if ($input["labeled"] != 0){
				$out .= "<label>" . $label . "</label>";
			}
		}
		$out .= "<input ". extract_hashed_attributes($input) . "/>";
		if ($input["type"] != "hidden") {
			$out .= "</li>";
		}
	}
	$out .= "</ul>";
	$out .= "</form>";
	if ($print === true) {
		echo $out;
	}
	return $out;
}

?>
