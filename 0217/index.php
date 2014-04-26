<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
 <head>
  <title>Ajax Table Editing</title>
  <script src="js/jquery.js"></script>	
  <script src="js/script-v2.js"></script>	
  <script src="js/jquery-ui-1.8.17.custom.min.js"></script>	
  <link rel="stylesheet" href="css/style.css">
 </head>
 <body>
 <br>
   <div style="margin-left: 20%;margin-top: 5%;">
	  <input type="button" value="Add Record" id="add_new"><p>
	  <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list">
		<tr>
			<th width="20%">Product ID</th>
			<th width="20%">Product Description</th>
			<th width="40%">Peso Bruto</th>
			<th width="20%">Cajillas</th>
			<th width="20%">Peso Neto</th>
			<th width="20%">Remove</th>
		</tr>
<!--		<tr>
			<td>jquery</td>
			<td>ajax</td>
			<td>jquery@ajax.com</td>
			<td>242525</td>
			<td><a href="#" id="1" class="del">Delete</a></td>
		</tr>
		<tr>
			<td>php</td>
			<td>mysql</td>
			<td>php@mysql.com</td>
			<td>242525</td>
			<td><a href="#" id="2" class="del">Delete</a></td>
		</tr>
		<tr>
			<td>html</td>
			<td>css</td>
			<td>html@css.com</td>
			<td>242525</td>
			<td><a href="#" id="3" class="del">Delete</a></td>
		</tr>
		<tr>
			<td>wordpress</td>
			<td>plugins</td>
			<td>wordpress@plugins.com</td>
			<td>242525</td>
			<td><a href="#" id="4" class="del">Delete</a></td>
		</tr>
--!>
	  </table>
	</div>
	<div class="entry-form">
		<form name="buyfruit" id="buyfruit"> 
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
 </body>
</html>
