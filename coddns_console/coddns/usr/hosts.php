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

require_once (dirname(__FILE__) . "/../include/config.php");
require_once (dirname(__FILE__) . "/../lib/db.php");
require_once (dirname(__FILE__) . "/../lib/util.php");
require_once (dirname(__FILE__) . "/../lib/coduser.php");

if (! defined("_VALID_ACCESS")) { // Avoid direct access
    header ("Location: " . $config["html_root"] . "/");
    exit (1);
}

$auth_level_required = get_required_auth_level('usr','hosts','');
$user = new CODUser();
$user->check_auth_level($auth_level_required);


/* CASTELLANO */
$text["es"]["hosts_welcome"] = "
    <h2>Gestor de etiquetas</h2>
    <p>Aqu&iacute; puedes agregar nuevas etiquetas para tus dispositivos (<i>hosts</i>) o administrar las existentes.</p>
    <p>Recuerda que la responsabilidad sobre los contenidos que abras a Internet es s&oacute;lo tuya.</p>
";
$text["es"]["ht_hname"]      = "Nombre de host";
$text["es"]["label_tag"]     = "Etiqueta:";
$text["es"]["label_ip"]      = "Direcci&oacute;n IP:";
$text["es"]["label_getip"]   = "Coger mi IP";
$text["es"]["f_add"]         = "Agregar";
$text["es"]["ht_htitle"]     = "Mis hosts";
$text["es"]["dberror"]       = "Wooops, contacte con el administrador";
$text["es"]["reg_type"]      = "Tipo de registro";

/* ENGLISH */
$text["en"]["hosts_welcome"] = "
    <h2>Tag manager</h2>
    <p>Here you can add new tags for your devices (<i>hosts</i>) or manage them.</p>
    <p>Remember this: The responsability over the content you have opened to Internet is only yours.</p>
";
$text["en"]["ht_hname"]      = "Hostname";
$text["en"]["label_tag"]     = "Tag:";
$text["en"]["label_ip"]      = "IP address:";
$text["en"]["label_getip"]   = "Select my IP";
$text["en"]["f_add"]         = "Add";
$text["en"]["ht_htitle"]     = "My hosts";
$text["en"]["dberror"]       = "Wooops, please contact the administrator at footer";
$text["en"]["reg_type"]      = "DNS record type";

/* DEUTSCH */
$text["de"]["hosts_welcome"] = "
    <h2>Tag manager</h2>
    <p>Here you can add new tags for your devices (<i>hosts</i>) or manage them.</p>
    <p>Remember this: The responsability over the content you have opened to Internet is only yours.</p>
";
$text["de"]["ht_hname"]      = "Hostname";
$text["de"]["label_tag"]     = "Tag:";
$text["de"]["label_ip"]      = "IP address:";
$text["de"]["label_getip"]   = "Select my IP";
$text["de"]["f_add"]         = "Add";
$text["de"]["ht_htitle"]     = "My hosts";
$text["de"]["dberror"]       = "Wooops, please contact the administrator at footer";
$text["de"]["reg_type"]      = "DNS record type";

?>
<!DOCTYPE html>

<html>
<head>
    <title>resultados</title>
    <style type="text/css"/>
		<?php
		include_once (dirname(__FILE__) . "/../rs/css/pc/hosts.php");

		?>
        
    </style>
<script type="text/javascript">
    var t="";
    var sort_field="rr";
    var sort_mode=0;
    var page=0;

    function checkHostName(obj){
        if(/^([a-zA-Z]+([0-9]*[a-zA-Z]*)*)/.test(obj.value))
            updateContent("rec_info", "rest_host.php", "h="+obj.value);
        return false;
    }
    function select_my_ip(){
        ip.value="<?php echo _ip();?>";
        return false;
    }

    function toggle_help_ns(){
    	if(help_dns_type.style["max-width"] == ""){
    		help_dns_type.style["max-width"]  = "450px";
    		help_dns_type.style["max-height"] = "230px";
    		help_dns_type.style["padding"]    = "5px 0 0 15px";
    	}
    	else
    		help_dns_type.removeAttribute("style");
    }

    function adjustClass(field){
        if (field != null) {
            document.getElementById("td_tag").className="filter";
            document.getElementById("td_rr").className="filter";
            document.getElementById("td_value").className="filter";
            document.getElementById("td_ttl").className="filter";
            document.getElementById("td_" + field).className += " selected";
        }
    }

    function sortHostsBy(field){
        var data=[];
        if (field == null) {
            data[0] = sort_field;
        }
        else {
            adjustClass(field);         
            data[0] = field;
            if ( (field == sort_field) && (sort_mode=="std")){
                sort_mode = "invert";
            }
            else {
                sort_mode = "std";
                sort_field = field;
            }
        }
        data[1] = sort_mode;
        data[2] = page;
        updateContent("hosts_list", "ajax.php", "action=list_hosts&args="+JSON.stringify(data),true);
    }
    document.onload = sortHostsBy();
</script>
</head>

<body>
<section style="text-align: justify; margin-bottom: 20px;">
<?php
echo $text[$lan]["hosts_welcome"];
?>
</section>
<section class="uarea">
    <form id="newhost" method="POST" action="" onsubmit="fsgo('newhost', 'ajax_message','usr/hosts_rq_new.php', true,raise_ajax_message);return false;">
    <ul>
        <li>
            <label><?php echo $text[$lan]["label_tag"];?></label>
        </li>
        <li>
            <div style="float:left;"><input type="text" id="h" name="h" onchange="checkHostName(this);return false;" pattern="^([a-zA-Z]+([0-9]*[a-zA-Z]*)*)" required/><i class="extension">.<?php echo $config["domainname"]?></i></div>
			<div style="float:right;">
				<label><?php echo $text[$lan]["reg_type"];?>:</label> 
                <script type="text/javascript">
                    function showItem(item){
                        // hide all RR sections
                        rr_A.style["max-height"] = "0";
                        rr_NS.style["max-height"] = "0";
                        rr_CNAME.style["max-height"] = "0";
                        rr_MX.style["max-height"] = "0";

                        document.getElementById("rr_"+item).style["max-height"] = "1000px";
                    }
                </script>
                <select style="margin-left: 15px; width: 90px;" onchange="showItem(this.value)" name="rtype">
				
                <?php
                    // Retrieve all DNS Record types available from de DB

                $dbclient = new DBClient($db_config) or die ($dbclient->lq_error());
                $dbclient->connect() or die ($dbclient->lq_error());


                $q = "select h.tag from hosts h, record_types r where oid=(select id from users where mail='" . $_SESSION["email"] . "') and r.id=h.rtype and r.tag='A';";
                $r = $dbclient->exeq($q) or die ($dbclient->lq_error());
                $tag_options = "";
                while ($row = $dbclient->fetch_array ($r)) {
                    $tag_options .= "<option value='" . $row["tag"] . "'>" . $row["tag"] . "</option>";
                }

                $results  = $dbclient->exeq("select tag from record_types;");

                while ($r = $dbclient->fetch_object($results)) {
                ?>
					<option value="<?php echo $r->tag;?>"><?php echo $r->tag;?></option>
                <?php
                }
                
                ?>
				</select>
				<div id="launch_help_dns_type" onclick="toggle_help_ns();">&nbsp;</div>
				<div id="help_dns_type">
					<div>
					<p>Los registros de DNS aceptados son:</p>
					<ol>
						<li><b>A</b> Registro por defecto, representa un host</li>
						<li><b>MX</b> Registro de correo, indica que el registro corresponde a un servidor de correo electr&oacute;nico</li>
						<li><b>CNAME</b> Registro de etiqueta, establece un alias para un host ya registrado</li>
						<li><b>NS</b> Registro de servidor de nombres, agrega un <i>nameserver</i> que responder&aacute; las peticiones DNS hechas contra su subdominio</li>
					</ol>
					</div>
				</div>
			</div>
            <div id="rec_info" style="clear:both;"></div>
        </li>
    <div style="float:right;">
        <span>TTL:</span> <input style="margin: 0 35px 0 15px; width: 90px;" type="number" id="ttl" name="ttl" value="12" />
    </div>
    </ul>
    <?php
        // the custom query depends on RR active - ask via AJAX
    ?>
    <div id="rr_A" class="hidden" style="max-height: 1000px;">
        <ul>
            <li>
                <label><?php echo $text[$lan]["label_ip"];?></label>
            </li>
            <li>
                <div style="float:left;">
                    <input type="text" id="ip" name="ip" value="<?php echo _ip();?>" required/> <button onclick="select_my_ip(); return false;"><?php echo $text[$lan]["label_getip"];?></button>
                </div>
            </li>
        </ul>
    </div>
    <div id="rr_NS" class="hidden">
        <ul style="width: 300px; margin: 0;">
            <li>
                <label>NS:</label>
                <select style="float:right; min-width: 150px;" name="rtag_NS">
                <?php echo $tag_options; ?>
                </select>
            </li>
        </ul>
    </div>
    <div id="rr_CNAME" class="hidden">
        <ul style="width: 300px; margin: 0;">
            <li>
                <label>CNAME:</label>
                <select style="float:right; min-width: 150px;" name="rtag_CNAME">
                <?php echo $tag_options; ?>
                </select>
            </li>
        </ul>
    </div>
    <div id="rr_MX" class="hidden">
        <ul style="width: 300px; margin: 0;">
            <li>
                <label>MX:</label>
                <select style="float:right; min-width: 150px;" name="rtag_MX">
                <?php echo $tag_options; ?>
                </select>
            </li>
            <li>
                <label>Prioridad:</label>
                <input style="width: 150px;" type="number" name="mx_priority" value="10"/>
            </li>
        </ul>
    </div>
    <ul>
        <li>
            <label></label>
            <input type="submit" value="<?php echo $text[$lan]["f_add"]; ?>"/>
        </li>
    </ul>
    </form>
</section>



<div id="myhosts">

<h3><?php echo $text[$lan]["ht_htitle"];?></h3>
<form id="change" action="<?php echo $config["html_root"];?>/?m=usr&z=hosts&op=mod" method="POST">
    <input type="hidden" id="edith" name="edith" required/>
    <input type="hidden" id="editip" name="editip" required/>
</form>
<form id="del" action="#" onsubmit="return false;" method="POST">
    <input type="hidden" id="delh" name="delh" required/>
</form>

<table>
    <thead>
        <tr>
            <td id="td_tag" class="filter" onclick="sortHostsBy('tag');"><?php echo $text[$lan]["ht_hname"];?></td>
            <td id="td_rr" class="filter" onclick="sortHostsBy('rr');" title="record type">RR</td>
            <td id="td_value" class="filter" onclick="sortHostsBy('value');">IP/ VALUE</td>
            <td id="td_ttl" class="filter" onclick="sortHostsBy('ttl');">TTL</td>
            <td colspan="2">Ops.</td>
        </tr>
    </thead>
    <tbody id="hosts_list">
    <!-- Filled by AJAX ~ onload sortHostsBy() -->
    </tbody>
</table>
</div>
</body>
</html>
<?php
$dbclient->disconnect();
?>
