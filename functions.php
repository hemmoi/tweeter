<?php

	session_start();
	$link = mysqli_connect("127.0.0.1", "my_user", "my_password", "my_db");

	if (!$link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}

	if ($_GET['function'] == "logout") {
		session_unset();
		session_destroy();
	}

	function time_since($since) {
		$chunks = array(
			array(60 * 60 * 24 * 365 , 'year'),
			array(60 * 60 * 24 * 30 , 'month'),
			array(60 * 60 * 24 * 7, 'week'),
			array(60 * 60 * 24 , 'day'),
			array(60 * 60 , 'hour'),
			array(60 , 'min'),
			array(1 , 'second')
		);

		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0) {
				break;
			}
		}

		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
		return $print;
	}

	function displayTweets ($type) {
		global $link;
		if ($type == "public") {
			$whereClause = "";
		} else if ($type == "followed") {
			$subClause = sprintf("(SELECT target FROM following WHERE follower=%s)",
									mysqli_real_escape_string($link, $_SESSION['id']));
			$whereClause = sprintf("WHERE userid IN %s",
									$subClause);
		} else if ($type == "myTweets") {
			$whereClause = sprintf("WHERE userid=%s",
									mysqli_real_escape_string($link, $_SESSION['id']));
		} else if ($type == "search") {
			$whereClause = "WHERE tweet LIKE '%".mysqli_real_escape_string($link, $_GET['q'])."%'";
		} else if (is_numeric($type)) {
			$tweeterQuery = sprintf("SELECT * FROM users WHERE id='%s'",
								mysqli_real_escape_string($link, $type));
			$tweeterResult = mysqli_query($link, $tweeterQuery);
			$tweeter = mysqli_fetch_assoc($tweeterResult);

			$whereClause = sprintf("WHERE userid='%s'",
								mysqli_real_escape_string($link, $type));
			echo "<p><h3>Tweets from '".$tweeter['email']."'";
			echo "<a href='?page=profiles' class='btn btn-primary' id='showTweets'>Show All Profiles</a></h3></p>";
		}

		$query = sprintf("SELECT * FROM tweets %s ORDER BY datetime DESC LIMIT 10",
							$whereClause);

		$result = mysqli_query($link, $query);

		if (mysqli_num_rows($result) == 0) {
			echo "There are no tweets to show";
		} else {
			while ($row = mysqli_fetch_assoc($result)) {

				$userQuery = sprintf("SELECT * FROM users WHERE id=%s LIMIT 1",
								mysqli_real_escape_string($link, $row['userid']));
				$userResult = mysqli_query($link, $userQuery);
				$user = mysqli_fetch_assoc($userResult);
				$timeSince = time_since(time() - strtotime($row['datetime']));
				echo "<div class='tweet'>";
				echo "<p id='emailHeader'><a href='?page=profiles&userid=".$user['id']."'>".$user['email']."</a></p>";
				echo "<p>".$row['tweet']."<span id='tweetTime'>".$timeSince." ago</span></p>";
				echo "<p><a class='toggleFollow followLink' data-userId='".$row['userid']."'>";

				$targetQuery = sprintf("SELECT * FROM following WHERE follower='%s' AND target='%s' LIMIT 1",
								mysqli_real_escape_string($link, $_SESSION['id']),
								mysqli_real_escape_string($link, $row['userid']));
				$targetResult = mysqli_query($link, $targetQuery);
				if (mysqli_num_rows($targetResult) > 0) {
					echo "Unfollow";
				} else {
					echo "Follow";
				}

				echo "</a></p></div>";
			}
		}

	}

	function displayUsers() {
		global $link;
		$query = "SELECT * FROM users";
		$result = mysqli_query($link, $query);

		if (mysqli_num_rows($result) == 0) {
			echo "There are no users to show";
		} else {
			while ($row = mysqli_fetch_assoc($result)) {
				echo "<div class='user'>";
				echo "<p><h4>".$row['email'];
				echo "<a href='?page=profiles&userid=".$row['id']."' class='btn btn-primary' id='showTweets'>Show Tweets</a></h4></p>";
				echo "</div>";

			}
		}
	}

	function searchTweets() {
        echo '
			<form class="searchTweets form-inline">
				<div class="form-group">
					<input type="hidden" name="page" value="search">
					<input type="text" name="q" class="form-control" id="search" placeholder="search tweets">
					<button id="searchButton" type="submit" class="btn btn-primary">Search</button>
				</div>

			</form>';
	}

	function postTweet() {
		if  ($_SESSION['id'] > 0) {
            echo '
				<div id="tweetSuccess" class="alert alert-success"></div>
				<div id="tweetFail" class="alert alert-danger"></div>
				<div class="form">
					<div class="form-group">
						<textarea class="form-control" id="tweetContent"></textarea>
					</div>
					<button id="postTweetButton" class="btn btn-primary">Post Tweet</button>
				</div>';
		}
	}

?>
