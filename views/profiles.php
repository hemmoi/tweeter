<div class="container">


	<div class="row">
	  <div class="col-md-8">
		<h2>Public Profiles </h2>
		
		<?php 
			if ($_GET['userid']) {
				displayTweets($_GET['userid']);
			
			} else {
			displayUsers();
			}
		?>
	  
	  </div>
	  <div class="col-md-4">
		<?php
			searchTweets();
		?>
		<?php	
			postTweet()
		?>
	  </div>
	</div>

</div>