<?php

	require_once('post.php');


	//If no mode or detail is set then defaults are given
	$mode   = (isset($_GET["mode"]) ? $_GET["mode"] : 'csv'); 
	$detail = (isset($_GET["detail"]) ? $_GET["detail"] : 'single');
    $filterPosts = new post('posts.csv','top_posts.csv','other_posts.csv','daily_top_posts.csv');
	
    $filterPosts->genFiles($mode,$detail);

?> 
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>John Williams' CafeMedia Code Challenge</title>


  <!--[if lt IE 9]>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
  <![endif]-->
</head>

<body>
	<h1>John Williams' CafeMedia Code Challenge</h1>

	<h2>Instructions</h2>
	<p>Enter following url example: 
  <a href="http://ec2-52-51-4-169.eu-west-1.compute.amazonaws.com/cafemedia/oo/?mode=json&detail=single">
  http://ec2-52-51-4-169.eu-west-1.compute.amazonaws.com/cafemedia/oo/?mode=json&detail=single</a>
  <br>where <b>mode</b> may equal 'json' or 'csv' and <b>detail</b> may equal 'full' or 'single' for full record download or just id column.</p>
  <h2>Output Files</h2>
  <h3>Current Detail:<?php echo $detail;?></h3>
  <ul>
  <li><a target="_blank" href="/cafemedia/oo/top_posts.<?php echo $mode;?>">top_posts.<?php echo $mode;?></a></li>
<li><a target="_blank"  href="/cafemedia/oo/other_posts.<?php echo $mode;?>">other_posts.<?php echo $mode;?></a></li>
<li><a target="_blank"  href="/cafemedia/oo/daily_top_posts.<?php echo $mode;?>">daily_top_posts.<?php echo $mode;?></a></li>
  </ul>


</body>
</html>