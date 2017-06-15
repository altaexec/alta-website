<?php 

	$php_root_path = ".." ;
	$privilege_root_path = "/admin" ;
	require_once("includes/include_all_fns.php");
    require_once("$php_root_path/includes/page_includes/page_fns.php");
	session_start();
	// extract ( $_SESSION , EXTR_REFS ) ;	
	$err_message = " Unable to process your request due to the following problems: <br>\n" ;
	
	// If file was selected to import data
	if ($HTTP_POST_VARS["Submit"] == "Import")
	{
        if ($_FILES["ImportFile"]["tmp_name"]){
            // read in settings object (which was in the file)
            $objstr = file_get_contents($_FILES["ImportFile"]["tmp_name"]);
            // attempt to unserialize the object so that it can be used
            $settingObj = unserialize(ltrim($objstr));
			// Check first if the object was unserializable
            if ($settingObj) {
                // Replace values from form with those in settings file
                $result = updateSettings(get_object_vars($settingObj));
				if ($result === true)
				{
					// Write page
					do_html_header("Successful Update");
					?>
					<p>The settings are successfully updated<br>
					View new settings <a href="general_settings.php">here</a>.</p>
					<?php
					do_html_footer();
					exit;
				} else {
					do_html_header("Error Information");
					echo "<p>$result</p>";
					do_html_footer();
					exit;
				}
            } else {
				// Write page
				do_html_header("Invalid Settings File");
				?>
				<p>The settings file selected was invalid or corrupted.<br>
				<?php
				do_html_footer();
				exit;
			}
        }
	}
	else if ($HTTP_POST_VARS["Submit"] == "Export")
	{
		//Retrieve the setting information
		$settingInfo = get_Conference_Settings();
		header('Content-Type: application/octet-stream; name="settings"');	 
        header('Content-disposition: attachment; filename="settings"');
        echo serialize($settingInfo);
        exit;
	}
	
	do_html_header("Import/Export Settings");
?>
<form method="post" action="" enctype="multipart/form-data" >
	<h2>Import Settings</h2>
    <p>Select a file to import conference settings info from it.</p>
    <input type="file" name="ImportFile">
	<input type="submit" name="Submit" value="Import">
	
	<h2>Export Settings</h2>
    <p>Click to download the current settings to file.</p>
    <input type="submit" name="Submit" value="Export">
</form>
<?php do_html_footer(& $err_message);?>
