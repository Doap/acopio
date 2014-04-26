<?php
//$conn = mysql_connect("localhost","root","");
//mysql_select_db("phppot_examples",$conn);
require_once("../wp-config.php");
require_once("../wp-load.php");
$wp->init(); $wp->parse_request(); $wp->query_posts();
$wp->register_globals(); $wp->send_headers();
//get_header();
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
echo '<script src="js/jquery.js"></script>';  
echo '<script src="javascript.php" type="text/javascript"></script>';
echo '<script src="js/jquery-ui-1.8.17.custom.min.js"></script>';
echo ' <link rel="stylesheet" href="css/style.css">';
 echo '<link rel="stylesheet" type="text/css" href="styles.css" />';
 echo var_dump($_POST["products"]); 
$product_id=8;
 echo '<br>' . $product_id;
$rowCount = count($_POST["products"]);
for($i=0;$i<$rowCount;$i++) {
?>
   <div style="margin-left: 20%;margin-top: 5%;">
          <input type="button" value="Add Record" id="add_new_<?php echo $_POST["products"][$i]; ?>"><p>
          <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list" id="table-list_<?php echo $_POST["products"][$i]; ?>">
                <tr>
                        <th width="20%">Product ID</th>
                        <th width="20%">Product Description</th>
                        <th width="40%">Peso Bruto</th>
                        <th width="20%">Cajillas</th>
                        <th width="20%">Peso Neto</th>
                        <th width="20%">Remove</th>
                </tr>
          </table>
        </div>
        <div class="entry-form" name="entry-form_<?php echo $_POST["products"][$i]; ?>" id="entry-form_<?php echo $_POST["products"][$i]; ?>">
                <form name="buyfruit" id="buyfruit_<?php echo $_POST["products"][$i]; ?>">
                <table width="100%" border="0" cellpadding="4" cellspacing="0">
                        <tr>
                                <td colspan="2" align="right"><a href="#" id="close_<?php echo $_POST["products"][$i]; ?>">Close</a></td>
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
                                <td><input type="button" value="Save" id="save_<?php echo $_POST["products"][$i]; ?>"><input type="button" value="cancel" id="cancel_<?php echo $_POST["products"][$i]; ?>"></td>
                        </tr>
                </table>
                </form>
        </div>
<?php
}
//get_footer();
}
?>
