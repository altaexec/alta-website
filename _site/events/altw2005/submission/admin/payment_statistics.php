<?php
$php_root_path = "..";
$privilege_root_path = "/admin" ;
require_once("includes/include_all_fns.php");
session_start();

$err_message = " Unable to process your request due to the following problems: <br>\n" ;

$htmlStats = get_payment_statistics(&$err_message);

do_html_header("Payment Statistics",&$err_message);
?>
<div>
	<?php echo $htmlStats?>
</div>
<?php
do_html_footer(&$err_message);
?>
