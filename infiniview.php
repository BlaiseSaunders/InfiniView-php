<?php
// If this is the first run, print all the HTML, CSS and JS they'll need
if (!htmlspecialchars($_GET["seed"])) : ?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">
<html>
<head>
        <title>Ganbatte!</title>

	<style>
		body
		{
			background-color: #17181A;
			color: #17181A;
		}
		img
		{
			max-width: 99.0%;
			max-height: 99.0%;
			box-shadow: 0 0 10px #000;
			transition: all 2s;
		}
		video
		{
			max-width: 94.0%;
			max-height: 94.0%;
		}
		/* Fun little expansion on mouse over to keep things interesting */
		img:hover
		{
			max-width: 99.5%;
			max-height: 99.5%;
			box-shadow: 0 0 50px #000;
		}
		/* The DIV we keep the images in */
		.container
		{
			float: left;
			width: 33%;
			height: 500px;
			padding-top: 30px;
			padding-bottom: 30px;
			overflow: hidden;
		}
		.container img video
		{
			display: block;
			margin-left: auto;
			margin-right: auto;
		}
	</style>
	<script>
		// Call ourself, except this time with the "seed" GET variable
		// set so that we know to print images and not this
		function print_images(seed)
		{
			if (typeof seed === "undefined")
				seed = Date.now();

			var big_boy = document.getElementById('big_boy_container');

			var our_name = window.location.href.split('\\').pop().split('/').pop();

			var xhttp = new XMLHttpRequest();
			// Call ourself with seed set, get the images and append them to the big container
			xhttp.open("GET", our_name+"?seed="+seed+"&offset="+window.div_count, true);
			xhttp.onreadystatechange = function()
			{
		    		if (xhttp.readyState == 4 && xhttp.status == 200)
		    		{
		    			var new_div = document.createElement('div');
		    			new_div.id = 'nu_images'+window.div_count;
						new_div.innerHTML = xhttp.responseText;
						big_boy.appendChild(new_div);
						// Hides the older images, simply delete these two lines to stop this
						var old_div = document.getElementById('nu_images'+(window.div_count-2));
						old_div.style.visibility = 'hidden';
		    		}
		    	}
			xhttp.send();
			window.div_count++;
		}
		<?php

		 	$date = new DateTime();
			$seed = $date->getTimestamp();

			// Wait for the DOM to load
			echo "document.addEventListener('DOMContentLoaded', function(event) {\n";

			// Create a listener for whent the user scrolls
			echo "	document.addEventListener('scroll', function (event) { ";

			// This code runs every scroll, it figures out where the bottom of the page is
			echo "		var sub_limit = Math.max( document.body.scrollHeight, document.body.offsetHeight,document.documentElement.clientHeight, document.documentElement.scrollHeight, document.documentElement.offsetHeight );";
			echo "	var limit = sub_limit - window.innerHeight;";

			// Check if the user has scrolled past the limit (they've hit the bottom of the page)
			echo "		if (window.scrollY >= limit)";
			echo "			print_images($seed);";
			echo "		}";
			echo "	);";

			// After setting up the scroll checker, create a variable
			// for tracking how many times we've printed images
			echo "	window.div_count = 0;\n";
			// Call the print function and load the first lot of images
			echo "	print_images($seed);\n";
			echo "});\n";
		?>
	</script>


</head>
<body>
	<center id='big_boy_container'>
	</center>
</body>
</html>



<?php else : // Otherwise, send images for the JS (in HTML form)

	// Outputs an image in a container
	function print_image($img_location, $id)
	{
		//$img_location = substr($img_location, strlen($_SERVER["DOCUMENT_ROOT"]));

		echo "<div class='container'>";
		if (strstr($img_location, ".webm") == NULL)
		{
			echo "<a href='".$img_location."'><img class='fav img_$id' src='$img_location'></h1></a>";
		}
		else
		{
			echo "<video controls loop class='img_$id'>";
			echo "	<source align='left' src='".$img_location."' type='video/webm'>";
			echo "	Your browser does not support the video tag.";
			echo "</video>";
		}

		echo "</div>\n";
	}

	/* Every time we print images, get all of the images, shuffle them the same way
	 * (but different every page refresh) and then print out a section of those
	 * shuffled images, then next time print out an offset lot of images
	 * so the user sees new images */

	$seed = htmlspecialchars($_GET["seed"]);
 	$offset = htmlspecialchars($_GET["offset"]);

	// Make sure we're shuffling the same way every time
	srand($seed);

	$limit = 49/2;
	if (!isset($offset))
		$offset = 0;
	$final_locations = glob("./*");

	// Delete this line to disable images shuffling
	shuffle($final_locations);


	// Only print images past what we've seen
	$final_locations = array_slice($final_locations, $offset*$limit);

	$i = 0;
	foreach ($final_locations as $match)
	{
		if (++$i >= $limit)
			break;
		print_image($match, $i);
	}
endif;?>

