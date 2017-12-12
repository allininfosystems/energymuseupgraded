<?php

$hostname = "mysql50-83.wc1.dfw1.stabletransit.com";
$database = "524545_magento";
$username = "524545_magento";
$password = "Energy88!";

$conn = mysql_connect($hostname, $username, $password);
mysql_select_db($database, $conn);



// delete from tables
$date = strtotime("-90 days");
$query = "DELETE FROM report_viewed_product_index WHERE added_at < '" . $date . "'";
mysql_query($query, $conn);

$date = strtotime("-10 days");
$query = "DELETE log_url, log_url_info FROM log_url JOIN log_url_info WHERE log_url.url_id = log_url_info.url_id AND log_url.visit_time < '" . $date . "'";
mysql_query($query, $conn);
$query = "DELETE log_visitor, log_visitor_info FROM log_visitor JOIN log_visitor_info WHERE log_visitor.visitor_id = log_visitor_info.visitor_id AND log_visitor.last_visit_at < '" . $date . "'";
mysql_query($query, $conn);



// optimize tables
$query = "OPTIMIZE TABLE 524545_magento.log_url, 524545_magento.log_url_info, 524545_magento.log_visitor, 524545_magento.log_visitor_info";
mysql_query($query, $conn);


?>