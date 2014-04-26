<?php
//$conn = mysql_connect("localhost","root","");
//mysql_select_db("phppot_examples",$conn);
require_once("../wp-config.php");
require_once("../wp-load.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
get_header();
if ( !is_user_logged_in() ) {
    echo 'Tiene que ingresar su usuario y contraseña para continuar...';
        get_footer();
} else {
$current_user = wp_get_current_user();
$username = $current_user->user_login;
$raw = json_decode( stripslashes( $_COOKIE[OLDUSER_COOKIE] ) );
$raw_value = $raw['0'];
$old_users = explode("|", $raw_value);
$buyer = $old_users['0'];
echo '<p> buyer = ' . $buyer . '</p>';
echo 'Bienvenidos, ' . $username . '!';

if(isset($_POST["submit"]) && $_POST["submit"]!="") {
$productCount = count($_POST["products"]);
for($i=0;$i<$productCount;$i++) {
//mysql_query("UPDATE users set userName='" . $_POST["userName"][$i] . "', password='" . $_POST["password"][$i] . "', firstName='" . $_POST["firstName"][$i] . "', lastName='" . $_POST["lastName"][$i] . "' WHERE userId='" . $_POST["userId"][$i] . "'");
echo '<br>Submitted!';
}
//header("Location:list_user.php");
}
echo '<title>Acopiar Fruta</title>';
 echo '<link rel="stylesheet" type="text/css" href="styles.css" />';
// echo '<body>';
 echo '<form name="frmUser" method="post" action="">';
 echo '<div style="width:500px;">';
 echo '<table border="0" cellpadding="10" cellspacing="0" width="500" align="center">';
 echo '<tr class="tableheader">';
 echo '<td>Acopiar Fruta</td>';
 echo '</tr>';
$rowCount = count($_POST["products"]);
//echo '<br>rowCount = '. $rowCount;
//echo var_dump($_POST);
for($i=0;$i<$rowCount;$i++) {
//$result = mysql_query("SELECT * FROM users WHERE userId='" . $_POST["users"][$i] . "'");
//$row[$i]= mysql_fetch_array($result);
echo '<tr>';
echo '<td>';
echo '<table border="0" cellpadding="10" cellspacing="0" width="500" align="center" class="tblSaveForm">';
echo '<tr>';
echo '<td><label>Product ID</label></td>';
echo '<td><input type="hidden" name="product_id[]" class="txtField" value="' . $_POST["products"][$i] . '">' . $_POST["products"][$i] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><label>Descripcion</label></td>';
echo '<td><input type="hidden" name="product_id[]" class="txtField" value="' . $_POST["product_descriptions"][$i] . '">' . $_POST["product_descriptions"][$i] . '</td>';
echo '</tr>';
echo '<tr>';
echo '<td><label>Peso</label></td>';
echo '<td><input type="hidden" name="peso[]" class="txtField" value="texto"><input type="text" name="peso[]" class="txtField" value="0"></td>';
echo '</tr>';
echo '<tr>';
echo '<td><label>Cajillas</label></td>';
echo '<td><input type="cajillas" name="cajillas[]" class="txtField" value="3"></td>';
echo '</tr>';
echo '<td><label>Peso Bruto</label></td>';
echo '<td><input type="text" name="peso_bruto[]" class="txtField" value="texto"></td>';
echo '</tr>';
echo '<td><label>Peso Neto</label></td>';
echo '<td><input type="text" name="peso_neto[]" class="txtField" value="texto"></td>';
echo '</tr>';
echo '</table>';
echo '</td>';
echo '</tr>';
?>
  <script src="../test/js/jquery.js"></script>  
  <script src="../test/js/script-v2.js"></script>       
  <script src="../test/js/jquery-ui-1.8.17.custom.min.js"></script>     
  <link rel="stylesheet" href="../test/css/style.css">
   <div style="margin-left: 20%;margin-top: 5%;">
          <input type="button" value="Add Record" id="add_new"><p>
          <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list_<?php echo $_POST["products"][$i]; ?>">
                <tr>
                        <th width="20%">Product ID</th>
                        <th width="20%">Product Description</th>
                        <th width="40%">Peso Bruto</th>
                        <th width="20%">Cajillas</th>
                        <th width="20%">Peso Neto</th>
                        <th width="20%">Remove</th>
                </tr>
                        <td>html</td>
                        <td>css</td>
          </table>
        </div>
        <div class="entry-form_<?php echo $_POST["products"][$i]; ?>">
                <form name="buyfruit" id="buyfruit_<?php echo $_POST["products"][$i]; ?>">
                <table width="100%" border="0" cellpadding="4" cellspacing="0">
                        <tr>
                                <td colspan="2" align="right"><a href="#" id="close">Close</a></td>
                        </tr>
                        <tr>
                                <td>Product ID</td>
                                <td><input type="text" name="product_id"></td>
                        </tr>
                        <tr>
                                <td>Description</td>
                                <td><input type="hidden" name="product_description" value="abc"></td>
                        </tr>
                        <tr>
                                <td>Peso Bruto</td>
                                <td><input type="text" name="peso_bruto"></td>
                        </tr>
                        <tr>
                                <td>Cajillas</td>
                                <td><input type="number" name="cajillas" value="3"></td>
                        </tr>
                        <tr>
                                <td>Peso Neto</td>
                                <td><input type="hidden" name="peso_neto" value="0"></td>
                        </tr>
                        <tr>
                                <td align="right"></td>
                                <td><input type="button" value="Save" id="save"><input type="button" value="cancel" id="cancel"></td>
                        </tr>
                </table>
                </form>
        </div>
<?php
}
echo '<tr>';
echo '<td colspan="2"><input type="submit" name="submit" value="Submit" class="btnSubmit"></td>';
echo '</tr>';
echo '</table>';
echo '</div>';
echo '</form>';
get_footer();
}
?>
