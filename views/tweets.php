<div class="container">


	<div class="row">
	  <div class="col-md-8">
		<h2>Your Tweets </h2>
		<?php 
			displayTweets("myTweets");
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