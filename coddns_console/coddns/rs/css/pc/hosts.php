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
 * <date>2016-02-11</date>
 * <update>2016-02-11</udate>
 * <summary> </summary>
 */


?>

section.uarea ul{
    list-style-type: none;
    width: 100%;
    margin: 0px auto;
}
div#launch_help_dns_type{
	cursor: pointer;
	float:right;
	margin-left: 15px;
	width: 20px;
	height: 20px;
	background: url('<?php echo $config["html_root"] . "/rs/img/question_20.png";?>') no-repeat center;
    background-size: 15px;
}
div#help_dns_type {
    max-height: 0;
    max-width: 0;
    overflow: hidden;
    position: absolute;
    box-shadow: 0px 2px 10px -2px #424242;
    border-radius: 5px;
    margin-top: 15px;
    left: 50%;
    margin-left: -225px;
    font-size: 0.9em;
    background: #FFFAA3;
    transition: max-height 0.3s 0s, max-width 0.3s 0s;
}
div#help_dns_type > div {
    width: 400px;
    height: 200px;
    margin-left: 10px;
}
table {
    padding: 5px;
    margin: 0 auto;
    border-collapse: collapse;
}
thead{
    text-align: center;
    background: #1F282B;
    color: white;
}
thead td {
    padding: 5px;
}
tbody *{
    padding: 5px;
}
td {
    border: 1px solid #ddd;
}
tbody tr:hover{
    background: #E8F1F9;
}
div.hidden{
    max-height: 0;
    overflow: hidden;
}
button, input[type="submit"]{
    padding-left: 5px;
    padding-right: 5px;
}
td.del{
    cursor: pointer;
    background-repeat: no-repeat;
    background-position: center;
    background-size: 1em;
    width: 1.4em;
    height: 1.4em;
}
td.edit{
    cursor: pointer;
    background: url('<?php echo $config["html_root"];?>/rs/img/edit.png') no-repeat center;
    background-size: 1em;
    width: 1.4em;
    height: 1.4em;
}
td.del{
    cursor: pointer;
    background: url('<?php echo $config["html_root"];?>/rs/img/delete.png') no-repeat center;
    background-size: 1em;
    width: 1.4em;
    height: 1.4em;
}

div#rec_info {
    height: 1em;
}

div#myhosts {
    width: 90%;
    margin: 40px auto;
    padding: 10px;
}
div#myhosts table{
    width: 100%;
    margin: 20px auto;
}
