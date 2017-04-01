<div class="container">


	<div class="row">
	  <div class="col-md-8">
		<h2>Search Results for "<?php echo $_GET['q'] ?>"</h2>
		<?php 
			displayTweets("search");
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