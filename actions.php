<?php
	include("functions.php");

	if($_GET["action"] == "loginSignup") {
		$error = "";
		
		if(!$_POST["email"]) {
			$error = "Email address is required.";
		} else if(!$_POST["password"]) {
			$error = "Password is required.";
		} else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			$error = "Invalid email format.";
		}
			
		if($error != "") {
			print_r ($error);
			exit;
		} 

		
		
  		if ($_POST["loginActive"] == "0") {
			
 			$query = sprintf("SELECT * FROM users WHERE email='%s' LIMIT 1",
					mysqli_real_escape_string($link, $_POST["email"]));
			$result = mysqli_query($link, $query);
			
			if(mysqli_num_rows($result) > 0) {
				$error = "Email address already exists.";
			} 
			else {
				$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$sql = sprintf("INSERT INTO users (`email`, `password`) VALUES ('%s', '%s')",
					mysqli_real_escape_string($link, $_POST["email"]),
					mysqli_real_escape_string($link, $hash)
					);

				if (mysqli_query($link, $sql)) {
					$_SESSION['id'] = mysqli_insert_id($link);
					echo 1;
				} else {
					$error = "New user could not be created. Please try again later.";
				}
			} 
			
		} 	else if ($_POST["loginActive"] == "1") {
				$query = sprintf("SELECT * FROM users WHERE email='%s' LIMIT 1",
					mysqli_real_escape_string($link, $_POST["email"]));
				$result = mysqli_query($link, $query);
			
				if(mysqli_num_rows($result) == 0) {
					$error = "User could not be found.";
				} else {
					$row = mysqli_fetch_array($result);
					if (password_verify($_POST['password'], $row['password'])) {
						$_SESSION['id'] = $row['id'];
						echo 1;
						
					  } else {
						$error = "Wrong password. Please try again.";
					  }
				}
			}				
			
		
 		if($error != "") {
			print_r ($error);
			exit;
		} 
	}
	
	if ($_GET['action'] == "toggleFollow") {
		if($_POST['targetId']) {
			
			$query = sprintf("SELECT * FROM following WHERE follower='%s' AND target='%s'", 
								$_SESSION['id'], $_POST['targetId']);
			$result = mysqli_query($link, $query);

			if (mysqli_num_rows($result) > 0) {
				$followQuery = sprintf("DELETE FROM following WHERE follower='%s' AND target='%s' LIMIT 1", 
										$_SESSION['id'], $_POST['targetId']);
				mysqli_query($link, $followQuery);
				echo 1;
			} else {
				$followQuery = sprintf("INSERT INTO following (follower, target) VALUES (%s, %s)", 
										$_SESSION['id'], $_POST['targetId']);
				mysqli_query($link, $followQuery);
				echo 2;
			}
				
		}
		
	}
	
	if ($_GET['action'] == "postTweet") {
		if($_POST['tweetContent']) {
			$query = sprintf("INSERT INTO tweets (tweet, userid, datetime) VALUES ('%s', '%s', now())",
						mysqli_real_escape_string($link, $_POST['tweetContent']),
						mysqli_real_escape_string($link, $_SESSION['id']));
			if (mysqli_query($link, $query)) {
				echo 1;
			} else {
				echo 0;
			}
		}
	}
	
	if ($_GET['action'] == "searchTweets") {
		if($_POST['searchContent']) {
			$query = "SELECT * FROM tweets WHERE tweet LIKE '%".mysqli_real_escape_string($link, $_POST['searchContent'])."%'";
			echo $query;
			if (mysqli_query($link, $query)) {
				echo 1;
			} else {
				echo 0;
			}
		}
	}
	

?>