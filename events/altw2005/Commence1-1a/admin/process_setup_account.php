<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");	
	session_start() ;
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	if($HTTP_POST_VARS["Submit"] == "Back"){
		$url = "Location: setup_new_account.php?accountType=".$HTTP_POST_VARS["accountType"];
		header($url);
		exit;
	}

	//Generate the random password
	$password = generate_password();	
	
	//Call the function to setup reviewer account
	$result = setup_new_account($HTTP_POST_VARS,$password);
	
	if($result === true){
		do_html_header("Successful Setup");
		echo "<p>The following account has been setup successfully.<br><br>";
		echo "Account Type: ".$HTTP_POST_VARS["accountType"]."<br><br>";
		echo "Login Name: <strong>".$HTTP_POST_VARS["loginname"]."</strong><br>";
		echo "Email Address: <strong>".$HTTP_POST_VARS["email"]."</strong><br><br>";
		if($HTTP_POST_VARS["accountType"] == "Reviewer")
			{
			echo "You can either <a href=\"setup_new_account.php?accountType=Reviewer\"> Add </a> another reviewer or <a href=\"view_all_reviewers.php\">View Reviewer Accounts</a>.</p>";
			}
		else
			echo "Go back to <a href=\"view_all_users.php\">View All Users</a>.</p>";
					
		do_html_footer();
	}else{
		do_html_header("Error Information");
		echo "<p>$result</p>";
		do_html_footer();
	}
	
	//Destory the session variable
	unset($_SESSION["arrLoginInfo"]);


?>
