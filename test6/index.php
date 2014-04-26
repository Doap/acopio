<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
 <head>
  <title>Ajax Table Editing</title>
  <script src="js/jquery.js"></script>	
  <script src="javascript.php" type="text/javascript"></script>
  <script src="js/jquery-ui-1.8.17.custom.min.js"></script>	
  <link rel="stylesheet" href="css/style.css">
 </head>
 <body>
 <br>
   <div style="margin-left: 20%;margin-top: 5%;">
	  <input type="button" value="Add Record" id="add_new_123"><p>
	  <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list" id="table-list_123">
		<tr>
			<th width="20%">Product ID</th>
			<th width="20%">Product Description</th>
			<th width="40%">Peso Bruto</th>
			<th width="20%">Cajillas</th>
			<th width="20%">Peso Neto</th>
			<th width="20%">Remove</th>
		</tr>
		<tr>
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
	  </table>
	</div>
	<div class="entry-form" id="entry-form_123">
		<form name="buyfruit" id="buyfruit_123"> 
		<table width="100%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2" align="right"><a href="#" id="close_123">Close</a></td>
			</tr>
			<tr>
				<td>Product ID</td>
				<td><input type="hidden" name="product_id" value="123"></td>
			</tr>
			<tr>
				<td>Description</td>
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
			</tr>
			<tr>
				<td align="right"></td>
				<td><input type="button" value="Save" id="save_123"><input type="button" value="cancel" id="cancel_123"></td>
			</tr>
		</table>
		</form>
	</div>
   <div style="margin-left: 20%;margin-top: 5%;">
	  <input type="button" value="Add Record" id="add_new_456"><p>
	  <table width="70%" border="0" cellpadding="0" cellspacing="0" class="table-list" id="table-list_456">
		<tr>
			<th width="20%">Product ID</th>
			<th width="20%">Product Description</th>
			<th width="40%">Peso Bruto</th>
			<th width="20%">Cajillas</th>
			<th width="20%">Peso Neto</th>
			<th width="20%">Remove</th>
		</tr>
		<tr>
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
	  </table>
	</div>
	<div class="entry-form" id="entry-form_456">
		<form name="buyfruit" id="buyfruit_456"> 
		<table width="100%" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2" align="right"><a href="#" id="close_456">Close</a></td>
			</tr>
			<tr>
				<td>Product ID</td>
				<td><input type="hidden" name="product_id" value="456"></td>
			</tr>
			<tr>
				<td>Description</td>
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
			</tr>
			<tr>
				<td align="right"></td>
				<td><input type="button" value="Save" id="save_456"><input type="button" value="cancel" id="cancel_456"></td>
			</tr>
		</table>
		</form>
	</div>
 </body>
</html>
