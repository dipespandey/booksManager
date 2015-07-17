<?php
	require_once 'login.php';
	$server = new mysqli($db_hostname, $db_username, $db_password, $db_database);

	if($server->connect_error) die($server->connect_error);
	else echo "Connection Successful..";

	//delete a record
	if(isset($_POST['delete']) && isset($_POST['isbn']))
	{
		$isbn = get_post($server, 'isbn');
		$query = "DELETE FROM classics WHERE isbn ='$isbn'";
		$result = $server->query($query);

		if(!$result) die("Delete failed: $query".$server->error);
	}

	//populate the table
	if (isset($_POST['author']) &&
		isset($_POST['title']) &&
		isset($_POST['category']) &&
		isset($_POST['year']) &&
		isset($_POST['isbn']))
	{
		$author 	= get_post($server,'author');
		$title 		= get_post($server,'title');
		$category 	= get_post($server,'category');
		$year 		= get_post($server,'year');
		$isbn 		= get_post($server,'isbn');

		$query = "INSERT INTO classics VALUES"."('$author','$title','$category', '$year','$isbn')";

//		echo $query."<br>";

		$result = $server->query($query);

		if(!$result) echo "INSERT failed.."."<br>";
		 else echo "Successfully inserted to the database <br>";
	
	}


	//render the form
	echo<<<_END
	<form action = "mysqliTest.php" method ="POST"><pre>
		Author <input type = "text" name ="author">
		Title <input type = "text" name ="title">
		Category <input type = "text" name ="category">
		Year <input type = "text" name ="year">
		ISBN <input type = "text" name ="isbn">
			 <input type = "submit" value ="ADD RECORD">
		</pre>
	</form>
_END;

	//Query the database
	$query = "SELECT * FROM classics";
	$result = $server->query($query);

	if(!$result) die("Database access failed.".$server->error);

	//Fetch the no of rows
	$rows = $result->num_rows;

	//Show contents rowwise
	for($i=0; $i<$rows; $i++)
	{
		$result->data_seek($i);
		$row = $result->fetch_array(MYSQLI_NUM);
		echo <<<_END
		<pre>
		Author   $row[0]
		Title    $row[1]
	 	Category $row[2]
	 	Year     $row[3]	
	 	ISBN     $row[4]
	 	</pre>
	 	  <form action="mysqliTest.php" method="post">
 			 <input type="hidden" name="delete" value="yes">
 			 <input type="hidden" name="isbn" value="$row[4]">
 			 <input type="submit" value="DELETE RECORD">
 		  <form>
_END;
	}

	$result->close();
	$server->close();

	//to sanitize the input taken from the form
	function get_post($server,$var)
	{
		return $server->real_escape_string($_POST[$var]);
	}

?>