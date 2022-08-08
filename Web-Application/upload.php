<link href="assets/css/bootstrap.css" rel="stylesheet" />
<link href="assets/css/main.css" rel="stylesheet" />
<link href="assets/css/jquery.dataTables.min.css" rel="stylesheet" />
<link href="assets/css/responsive.dataTables.min.css" rel="stylesheet" />
<script src="assets/js/jquery-3.5.1.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>
<script src="assets/js/bootstrap.js"></script>


// If this was a real web application, this function of the app should be hidden behind a login and only be viewable for people with the right clearance
<?php
	function redirect ( $uri )
	{ ?>
		<script type="text/javascript">
		<!--
		document.location.href="<?php echo $uri; ?>";
		-->
	</script>
	<?php die;}
	// database connection information
	$usr = "Username here";
	$pw = "Password here";
	$db = "equipment";
	$hostname = "localhost";
	$extArray = array('application/pdf','application/docx', 'image/png', 'image/jpg', 'image/jpeg', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	$dblink = new mysqli($hostname, $usr, $pw, $db);

	//get the device id and type
	$did = $_REQUEST['did'];
	$type = $_REQUEST['type'];

	// get basic information of the device
	$sql = "Select `manu_Tbl`.`manufacturer`, `serial_number`, `status` From `".$type."` Join `manu_Tbl` on `".$type."`.`manu_id` = `manu_Tbl`.`manu_id` where `auto_id` = $did";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	$device=$result->fetch_array(MYSQLI_ASSOC);

	echo '<div class="row">';
	echo '<div class="panel panel-success col-md-4">';
	echo '<div class="panel-heading">Device Info</div>';
	echo '<div class="panel-body">';
	echo '<p>Manufacturer: '.$device['manufacturer'].'</p>';
	echo '<p>Serial Number: '.$device['serial_number'].'</p>';
	echo '<p>Status: '.$device['status'].'</p>';
	$manufacturer = $device['manufacturer'];

	// query for any files associated with the device
	$sql="Select * from `files` where `device` =$did";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql");
	// if something was found, display it
	if ($result->num_rows>0)
	{
		echo '<p>Device record Files Found:</p>';
		while ($data=$result->fetch_array(MYSQLI_ASSOC))
		{
			$name = $data['file_name'];
			//Button to open up the file in a new tab
			echo '<p><a class="btn btn-sm btn-primary" href="./files/'.$name.'" target="_blank">View Record</a></p>';
			echo '<form method="post">';
			//Button to allow the user to delete
			//
        		echo '<input type="submit" name="deleteFile" value="Delete"/>';
			echo '</form>';
			
			
			//echo '<button class="btn btn-success" name="UploadFileSys" type="button" value="deleteFile"/>Delete</button>';
			//if the deletedFile button was clicked delete the file from the database
			if (isset($_POST['deleteFile']))
			{
				echo "<p> Sql: ".$did."</p>";
				$sql = "Delete  from `files` where `device` = '$did' and `file_name` = '".$data['file_name']."'";
				
				$dblink->query($sql) or
					die("Something went wrong with $sql");
				redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/upload.php?did=".$did."&type=".$type."");
			}

		}
		
	}
	
				
	
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '<div class="panel panel-primary">';
	echo '<div class="panel-heading">Upload File</div>';
	echo '<div class="panel-body">';
	//form to allow for upload of a new file
	echo '<form role="form" method="post" enctype="multipart/form-data" action="">';
	echo '<input type="hidden" name="MAX_FILE_SIZE" value="50000000">';
	echo '<input type="hidden" name="id" value="'.$did.'">';
	echo '<div class="form-group">';
	echo '<label class="control-label col-lg-4">File Upload</label>';
	echo '<div = class"">';
	echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
	echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>';
	echo '<div class="row">';
	echo '<div class="col-md-2"><span class="btn btn-file btn-primary"><span class="fileupload-new">Select File</span><span class="fileupload-exists">Change</span>';
	echo '<input name="userfile" type="file"></span></div>';
	echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a></div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '<hr>';
	echo '<div class="col-md-2"><button class="btn btn-success" name="UploadFileSys" type="submit" value="UploadFileSys"/>Upload</button></div>';
	echo '<div class="col-md-2"><a class="btn btn-danger" href="">Cancel</a></div>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	// if the upload button was clicked and the size of the file is larger then 0 continue with upload
	if (isset($_POST["UploadFileSys"]) && $_FILES['userfile']['size'] > 0)
	{	
		$start_time = microtime(true);
		$uploadDir = "/var/www/html/files";
		//$did = $_POST['did'];
		//$type = $_POST['type'];
		$fileNameTmp = $_FILES['userfile']['name'];
		$fileName = preg_replace('/\s+/','_',$fileNameTmp);
		$regexEx = "^(([a-zA-Z]:)|(\\{2}\w+)\?)(\\(\w[\w].*))+(.jpg|.docx|.pdf|.png)$"; //regex to check for file names
		$tmpName = $_FILES['userfile']['tmp_name'];
		$fileSize = $_FILES['userfile']['size'];
		$fileType = $_FILES['userfile']['type'];
		$location = "$uploadDir/$fileName";
		// check if the files end with the correct type
		if (in_array($_FILES['userfile']['type'], $extArray) && preg_match($regexEx, $fileName) !== 0)
		{	
			move_uploaded_file($tmpName, $location);
			$sql = "Insert into `files` (`file_name`,`file_type`,`file_size`, `location`, `device`) Values";
			$sql.=" ('$fileName', '$fileType', '$fileSize', '$location', '$did')";
			$dblink->query($sql) or
				die("Something went wrong with $sql");
			
			redirect("https://ec2-54-144-131-180.compute-1.amazonaws.com/upload.php?did=".$did."&type=".$type."");
		}
		//if they don't match the regex warn that this filetype is not allowed
		// Could use alert instead of this
		else
		{	
			echo" $fileType";
			echo"<p>This file extenstion is not allowed</p>";
			echo"<p> The only file Types allowed are: png, jpg, word, pdf</p>";
		}
		$end_time = microtime(true);
	}
	echo '<div class="panel panel-success col-md-3">';
	echo '<div class="panel-heading">More Options</div>';
	echo '<div class="panel-body">';
	echo '<div> <a href="index.php">Home</a></div>';
	echo '<div> <a href="create.php">Create/Insert</a></div>';
	echo '<div> <a href="update.php?did='.$did.'&type='.$type.'">Update</a></div>';

	
?>
