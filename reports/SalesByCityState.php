<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml">  
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />  

  <script src="js/jquery-1.9.1.js"></script>
  <script src="js/jquery-ui-1.10.3.custom.js"></script>
  <link rel="stylesheet" href="css/ui-lightness/jquery-ui-1.10.3.custom.css" />
  <script>
  jQuery(function() {
  	jQuery("#datepicker1").datepicker({dateFormat: 'yy-mm-dd'});
  	jQuery("#datepicker2").datepicker({dateFormat: 'yy-mm-dd'});
  });
  </script>
<title>Heard Report</title>  
<style>
.body { color:#2f2f2f; text-align:center; font-family: Tahoma; font-size:12px; margin:0px; padding:0px; }
</style>
</head>  
<body class="body">  
<img src="http://www.armpocket.com/media/wysiwyg/logo_footer_1.png">

<h2 style="margin-top:0px">Orders by Country Report</h2>

<?php

if($_POST["startdate"]) {
	$date_start = $_POST["startdate"];
	$date_end = $_POST["enddate"];

	$php_start_date = $_POST["startdate"] . " 00:00:00";
	$php_end_date = $_POST["enddate"] . " 23:59:59";

	$createDate = new DateTime($php_start_date);
	$createDate->modify('+4 hours');
	$createDate = $createDate->format('Y-m-d H:i:s');
	//echo "Create Date: " . $createDate. "<br />";

	$endDate = new DateTime($php_end_date);
	$endDate->modify('+4 hours');
	$endDate = $endDate->format('Y-m-d H:i:s');
}
else 
{
	$date_start= date('Y-m-d', strtotime("-1 day"));;
	$date_end= date('Y-m-d');
}
?>

<form action="SalesByCountry.php" method="post">
	<!--Start Date: <input style="width:100px;" type="text" name="startdate" value="<?php echo $date_start ?>">
	End Date: <input style="width:100px;" type="text" name="enddate" value="<?php echo $date_end ?>">-->
	Start Date: <input style="width:100px;" type="text" id="datepicker1" name="startdate" value="<?php echo $date_start ?>" />
	End Date: <input style="width:100px;" type="text" id="datepicker2" name="enddate" value="<?php echo $date_end ?>" />
	<input type = "submit">
	<?php if($_POST["startdate"]) {
		echo "<br /><br /><b>Report Start Date</b>: " . $php_start_date . " - <b>Report End Date</b>: " . $php_end_date;
	}
	else 
	{
	echo "<br />Enter Dates as YYYY-MM-DD";
} ?>
	<br /><br />
	
</form>

<?php

$db_name = "armpocke_magento";
$db_user = "armpocke_root";  // usually root
$db_pass = "N0Return"; 
$db_ip = "localhost"; 

@$db = mysql_connect($db_ip, $db_user, $db_pass) or die("Database connection error!\n");
mysql_select_db($db_name);
//if ($db) echo "Connected to database at ".date("g:i:s a").".\n\n";

$sql2 = "SELECT quickcheckout_heard,COUNT(*) 
FROM sales_flat_order 
WHERE quickcheckout_heard IS NOT NULL 
AND created_at > '$createDate'
AND created_at < '$endDate'
GROUP BY quickcheckout_heard"; 

$sql = "SELECT base_grand_total,country_id,COUNT(*)
FROM sales_flat_order
INNER JOIN sales_flat_order_address
ON sales_flat_order.entity_id=sales_flat_order_address.parent_id
WHERE sales_flat_order_address.address_type='shipping'
AND sales_flat_order.created_at > '$createDate'
AND sales_flat_order.created_at < '$endDate'
GROUP BY country_id";

$report= mysql_query($sql) or die(mysql_error());

echo "<div style='width:325px; margin:0 auto;'>";
echo "<table border='1' style='border-collapse: collapse;border-color: silver;'>";  
echo "<tr style='font-weight: bold;'>";  
echo "<td width='125' align='center'>Country</td>";  
echo "<td width='200' align='center'>Number of Orders</td>";  
echo "</tr>"; 

    while($row=mysql_fetch_array($report)) {
    	echo "<tr>";  
		echo "<td align='center' width='125'>" . $row['country_id'] . "</td>";  
		echo "<td align='center' width='200'>" . $row['COUNT(*)'] . "</td>";  
		echo "</tr>";  
    }

echo "</table>";
echo "</div>";
echo "<br />";
 
mysql_close($db);
?>

</body>  
</html> 