<?php
/**
 * <copyright company="CODDNS">
 * Copyright (c) 2013 All Right Reserved, http://coddns.es/
 *
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY
 * KIND, EITHER EXPRESSED OR IMPLIED, NO INCLUDING THE WARRANTIES OF
 * MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR PURPOSE.
 *
 * </copyright>
 * <author>Fco de Borja Sanchez</author>
 * <email>fborja.sanchezs@gmail.com</email>
 * <date>2016-03-29</date>
 * <update>2016-03-29</udate>
 * <summary> </summary>
 *
 * To use this script is required to link the Chart.js lib.
 */
require_once(__DIR__ . "/../include/config.php");


/**
 * Returns the code to build a pie graph
 */
function print_graph_pie($chart, $alsoPrint = 0){

	$str = print_graph_canvas($chart);

	$str .= "<script type='text/javascript'>\n";
	$str .= "var " . $chart["id"] . " = document.getElementById('" . $chart["id"] . "').getContext('2d');\n";
    $str .= "var " . $chart["id"] . "_data = {\n";
    $str .= "           labels: [" . $chart["data"]["labels"] . "],\n";
    $str .= "           datasets: [{\n";
    $str .= "                        data: [" . $chart["data"]["datasets"]["data"] . "],\n";
    $str .= "                        backgroundColor: [" . $chart["data"]["datasets"]["backgroundColor"] . "],\n";
    $str .= "                        hoverBackgroundColor: [" . $chart["data"]["datasets"]["hoverBackgroundColor"] . "]\n";
    $str .= "            }]};\n"; // end of data
    $str .= "var " . $chart["id"] . "_chart = new Chart(" . $chart["id"] . ", {\n";
    $str .= "               type: 'pie',\n";
    $str .= "               data: " . $chart["id"] . "_data\n";
    $str .= "           });\n";
    $str .= "</script>\n";

    if ($alsoPrint != 0){
    	echo $str;
    }

    return $str;

}

/**
 * Returns the code to build a linear graph
 */
function print_graph_line($chart, $alsoPrint = 0){

	$str = print_graph_canvas($chart);

	$str .= "<script type='text/javascript'>\n";
	$str .= "var " . $chart["id"] . " = document.getElementById('" . $chart["id"] . "').getContext('2d');\n";
    $str .= "var " . $chart["id"] . "_data = {\n";
    $str .= "           labels: [" . $chart["labels"] . "],\n";
    $str .= "           datasets: [\n";
    if (isset($chart["datasets"])) {
	    foreach ($chart["datasets"] as $d) {
		    $str .= "                      {\n";
		    $str .= "                        label: ['" . $d["label"] . "'],\n";
		    $str .= "                        data: [" . $d["data"] . "],\n";
		    $str .= "                        pointBorderWidth: 1,\n";
		    $str .= "                        pointHoverRadius: 5,\n";
		    $str .= "                        backgroundColor: " . $d["backgroundColor"] . ",\n";
		    $str .= "                        borderColor: " . $d["borderColor"] . "\n";
		    $str .= "                      },\n";
	    }
	}
    $str .= "            ]};\n"; // end of data
    $str .= "var " . $chart["id"] . "_chart = new Chart(" . $chart["id"] . ", {\n";
    $str .= "               type: 'line',\n";
    $str .= "               data: " . $chart["id"] . "_data\n";
    $str .= "           });\n";
    $str .= "</script>\n";

    if ($alsoPrint != 0){
    	echo $str;
    }

    return $str;

}

/**
 * Print a generic canvas 
 */
function print_graph_canvas($data, $alsoPrint = 0){
	if (!isset($data["id"])){
		return null;
	}
	$str = "";

	// Default values
	if (!isset($data["title"]))        { $data["title"]        = "";    }
	if (!isset($data["legend_style"])) { $data["legend_style"] = "";    }
	if (!isset($data["width"]))        { $data["width"]        = "150"; }
	if (!isset($data["height"]))       { $data["height"]       = "150"; }

	$str .= "<h4>" . $data["title"] . "</h4>\n";
	$str .= "<br>\n";
	$str .= "<canvas id='" . $data["id"] . "' width='" . $data["width"] . "' height='" . $data["height"] . "'></canvas>\n";
	$str .= "<div class='canvas_legend' style='" . $data["legend_style"] . "' id='" . $data["id"] . "_legend'></div>\n";

	if($alsoPrint != 0){
		echo $str;
	}

	return $str;
}

?>


