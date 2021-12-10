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
 * <date>2016-10-12</date>
 * <update>2016-10-12</udate>
 * <summary> </summary>
 */

class HTMLWriter
{

    public function __construct()
    {
    }
    /**
     * Expands all attributes defined in a hash into a string
     *
     * @param  hash $attributes hashed attributes 'attribute' => 'value'
     * @return string           string transformation
     */
    public function extract_hashed_attributes($attributes)
    {
        $out = "";
        foreach ($attributes as $attr => $value) {
            $out .= " $attr='" . $value . "'";
        }
        return $out;
    }

    /**
     * Transforms a hash into an html form
     *
     * @param hash    $form  hash defined as:
     *                       $form["attr"]["method"]
     *                       = "POST";
     *                       $form["attr"]["action"]
     *                       = "#";
     *                       $form["inputs"]["nombre"]["id"]
     *                       = "id for
     *                       input ";
     *                       $form["inputs"]["nombre"]["name"]
     *                       = "";
     *                       $form["inputs"]["nombre"]["type"]
     *                       = "text";
     *                       $form["inputs"]["nombre"]["labeled"]
     *                       = 1 or 0 <-
     *                       enables or
     *                       disables label
     *
     * @param  boolean $print dump to output or not
     * @return string         returns the generated form (text)
     */
    public function form($form, $print = true)
    {
        $out = "<form" . $this->extract_hashed_attributes($form["attr"]) . ">";
        $out .= "<ul>";
        $inputs = $form["inputs"];

        foreach ($inputs as $label => $input) {
            if ($input["type"] != "hidden") {
                $out .= "<li>";
                if ($input["labeled"] != 0) {
                    $out .= "<label>" . $label . "</label>";
                }
            }
            $out .= "<input ". $this->extract_hashed_attributes($input) . "/>";
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


    /**
     * Print a div element
     *
     * @param hash    $div   contents:
     *                       div->data
     *                       = content
     *                       of the
     *                       div
     *                       div->attr
     *                       = array
     *                       of
     *                       attributes
     *                       (div
     *                       element)
     *                       Example
     *                       div["att"]["width"]
     *                       = "100%";
     *
     * @param  boolean $print print the element or not
     * @return String         returns the string with the code of the element
     */
    public function div($div, $print = true)
    {
        $out = "<div";
        foreach ($div["att"] as $k => $v) {
            $out .= " $k='" . $v . "'";
        }
        $out .= ">";

        $out .= $div["data"];
        $out .= "</div>";
        if ($print) {
            echo $out;
        }
        return $out;
    }

    /**
     * Print a table element
     *
     * @param hash    $table contents:
     *                       table->header
     *                       = header
     *                       of the
     *                       table
     *                       table->data
     *                       = content
     *                       of the
     *                       table
     *                       (matrix
     *                       nm -
     *                       row/col)
     *                       table->attr
     *                       = array
     *                       of
     *                       attributes
     *                       (table
     *                       element)
     *                       Example
     *                       table["att"]["width"]
     *                       = "100%";
     *                       Each sub
     *                       element
     *                       is
     *                       identified
     *                       by key:
     *                       table["att"]["th"][index]
     *                       =
     *                       'style="width:
     *                       100%;"';
     *                       table["att"]["tr"][index]
     *                       =
     *                       'style="width:
     *                       100%;"';
     *                       table["att"]["td"][row][column]
     *                       =
     *                       'id="value"
     *                       style="width:
     *                       100%;"';                                                                                                                                                                                              100%;"';
     *
     * @param  boolean $print print the element or not
     * @return String         returns the string with the code of the element
     */
    public function table($table, $print = true)
    {
        $out = "";

        // Start tag
        $out .= "<table ";
        foreach ($table["att"] as $k => $v) {
            if (($k == "th")|| ($k == "td")) { // exclude specific attributes
                continue;
            }
            $out .= " $k='" . $v . "'";
        }
        $out .= ">";

        // table header
        $out .= "<tr>";
        $i = 0;
        foreach ($table["header"] as $th) {
            $out .= "<th " . $table["att"]["th"][$i] . ">" . $th . "</th>";
            $i++;
        }
        $out .= "</tr>";
        $i = 0;
        $j = 0;
        foreach ($table["data"] as $td) {
            $out .= "<tr " . $table["att"]["tr"][$i] . ">";
            if (is_array($td)) {
                foreach ($td as $v) {
                    $out .= "<td " . $table["att"]["td"][$i][$j] . ">" . $v . "</td>";
                }
                $j++;
            } else {
                $out .= "<td" . $table["att"]["td"][$i] . ">" . $td . "</td>";
            }
            $out .= "</tr>";
            $i++;
        }

        $out .= "</table>";

        if ($print) {
            echo $table;
        }
        return $table;
    }
}
