<pre>
<?
$db_name = "armpocke_magento";
$db_user = "armpocke_root";  // usually root
$db_pass = "N0Return"; 
$db_ip = "localhost"; 

@$db = mysql_connect($db_ip, $db_user, $db_pass) or die("Database connection error!\n");
mysql_select_db($db_name);
if ($db) echo "Connected to database at ".date("g:i:s a").".\n\n";

// see http://us2.php.net/manual/en/datetime.gettimestamp.php to do something with dates
$date_start='2011-03-31';
$date_end='2011-07-01';

printf("This sales tax report runs from AFTER %s until BEFORE %s\n", $date_start, $date_end);

// NOTE: Tax exempt resellers who buy from us are HARD CODED!! They are in the list 1440,1264, blah blah
$sql="SELECT  
sfo.entity_id AS id,state, customer_id AS cust_id, base_shipping_amount AS shipping, grand_total, subtotal, tax_amount, customer_email, sfosh.created_at AS date  
FROM sales_flat_order sfo 
LEFT JOIN sales_flat_order_status_history sfosh ON sfo.entity_id = sfosh.parent_id  
WHERE customer_id IN (  1440,1264,1199,1146,1082,1076,677,603,454,392,216,191,189,175,168,166,146,145,137,118,114,71  ) 
AND state='complete' 
AND sfosh.status='complete' 
AND sfosh.created_at > '$date_start'
AND sfosh.created_at < '$date_end'
AND tax_amount=0
GROUP BY sfo.entity_id  
ORDER BY sfosh.entity_id DESC";

$ca_dealer_sales=multiRow( $sql );
// print_r( $newOrders );
if ( $ca_dealer_sales === FALSE ){
	echo "Unexpected error getting info:\n$sql\n".mysql_error()."\n\n";
	mysql_close($db);
	exit;
}
$reseller_sales=0;
$non_txbl_sales=0;
foreach( $ca_dealer_sales as $sale ){
	$reseller_sales+=$sale['subtotal'];
	$non_txbl_sales+=$sale['shipping'];
	// see http://us2.php.net/manual/en/datetime.gettimestamp.php to do something with dates
}

$sql="SELECT  
sfo.entity_id AS id,state, customer_id AS cust_id, base_shipping_amount AS shipping, grand_total, subtotal, tax_amount, customer_email, sfosh.created_at AS date  
FROM sales_flat_order sfo 
LEFT JOIN sales_flat_order_status_history sfosh ON sfo.entity_id = sfosh.parent_id  
WHERE tax_amount>0
AND state='complete' 
AND sfosh.status='complete' 
AND sfosh.created_at > '$date_start'
AND sfosh.created_at < '$date_end'
GROUP BY sfo.entity_id  
ORDER BY sfosh.entity_id DESC";

$ca_taxable_sales=multiRow( $sql );
// print_r( $newOrders );
if ( $ca_taxable_sales === FALSE ){
	echo "Unexpected error getting info:\n$sql\n".mysql_error()."\n\n";
	mysql_close($db);
	exit;
}
$txbl_ca_sales=0;
$tax_collected=0;
foreach( $ca_taxable_sales as $sale ){
	$txbl_ca_sales+=$sale['subtotal'];
	$tax_collected+=$sale['tax_amount'];
}


$sql="SELECT  
sfo.entity_id AS id,state, customer_id AS cust_id, base_shipping_amount AS shipping, grand_total, subtotal, tax_amount, customer_email, sfosh.created_at AS date  
FROM sales_flat_order sfo 
LEFT JOIN sales_flat_order_status_history sfosh ON sfo.entity_id = sfosh.parent_id  
WHERE tax_amount=0
AND state='complete' 
AND sfosh.status='complete' 
AND sfosh.created_at > '$date_start'
AND sfosh.created_at < '$date_end'
AND customer_id NOT IN (  1440,1264,1199,1146,1082,1076,677,603,454,392,216,191,189,175,168,166,146,145,137,118,114,71  ) 
GROUP BY sfo.entity_id  
ORDER BY sfosh.entity_id DESC";
$non_txbl_interstate_sales=multiRow( $sql );
// print_r( $newOrders );
if ( $non_txbl_interstate_sales === FALSE ){
	echo "Unexpected error getting info:\n$sql\n".mysql_error()."\n\n";
	mysql_close($db);
	exit;
}
$interstate_sales=0;
foreach( $non_txbl_interstate_sales as $sale ){
	$interstate_sales+=$sale['grand_total'];
}

printf( "Taxable Sales  $%9.2f (%d)\n",$txbl_ca_sales,count($ca_taxable_sales));
printf( "Tax collected  $%9.2f (%d)\n",$tax_collected,count($ca_taxable_sales));
printf( "Sanity check   :%9.2f%%\n", $tax_collected/$txbl_ca_sales*100 );
printf( "Non txbl sales $%9.2f (%d) &larr; this is 'Labor' column\n", $non_txbl_sales,count($ca_dealer_sales));
printf( "Interstate     $%9.2f (%d)\n", $interstate_sales,count($non_txbl_interstate_sales));
printf( "Reseller Sales $%9.2f (%d)\n",$reseller_sales,count($ca_dealer_sales));
?>

Numbers in () are quantity of sales contributing to dollar amount.
<?

// --- FUNCTIONS
function oneRow($sql) {
	$data = mysql_query($sql) or die(mysql_error());
	if (isData($data)) {
		$data = mysql_fetch_assoc($data);
	} else {
		$data = 0;
	}
	return $data;
}

function isData($sql) {
	if ($sql) {
		if (mysql_num_rows($sql)) {
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function multiRow ( $sql ) {
	if( $sql == "" ) return FALSE;
	//echo "<hr>\$sql=$sql<br><pre>\n"; // \$row:
	$Q = mysql_query($sql);
	if( isData($Q) )
	while(	$row[]=mysql_fetch_assoc($Q) ) {
		;//print_r( $row );
	}
	if( $row == "" )
		$rtn_stuff=FALSE;
	else
		for($i=0; $i<count($row); $i++){
			if( $row[$i] == "" )
				unset($row[$i]);
		}
	/*?>print_r( $row ): <? print_r( $row ); ?> </pre> <? */
	return $row;
}


?>
</pre>
