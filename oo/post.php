<?php
class Post
{
	private $top_posts_csv;
	private $other_posts_csv;
    private $daily_top_posts_csv;
    private $Posts;

    // method declaration
	private function array_to_csv($input_array,$output_file_object,$header,$detail){
		//converts array to csv file

		/* add csv header row on output files to improve readability for humans*/
		array_unshift($input_array,$header);

	    if ($detail == 'full'){
			foreach($input_array as $arr)
				fputcsv($output_file_object,$arr);
		} 
		else if ($detail == 'single'){
			foreach($input_array as $arr){
				//pops off first column of row (id and id values) onto
				//output file csv object
				$row = array(array_shift($arr));
				fputcsv($output_file_object,$row);
				}
		}
		fclose($output_file_object);
	}

	private function is_same_day($datetime1,$datetime2){
		//compares two datetime format variables to see if same day

		$date_format = 'D M d H:i:s Y';
		$date1=DateTime::createFromFormat($date_format, $datetime1)->format('Y-m-d');
		$date2=DateTime::createFromFormat($date_format, $datetime2)->format('Y-m-d');
		
		if($date1==$date2)
	    	return true;
		else
	    	return false;
	}

	private function is_daily_top_post($Post,$DailyTops)
	{
		/*search for posts in dailytops with same date as post
		if same date found then check to see if that dailytops like are less...if so
		replace.
		if same date not found then add post to daily top*/
		$date_found = false;
		foreach ($DailyTops as $key => $dailyTop){

			if ($this->is_same_day($Post[6],$dailyTop[6])){
				$date_found = true;
				if ($Post[3] > $dailyTop[3]){
					$DailyTops[$key] = $Post;
				}	
			}
		}
		if(!$date_found){
			array_push($DailyTops,$Post); 
		}

		return $DailyTops;
	}

	private function is_top_post($Post){
	/*

	Deterimines whether Post is 'Top Post' according to rules:
		Top Posts Rules:
		* The post must be public 
		* The post must have over 10 comments and over 9000 views
		* The post title must be under 40 characters
		[0] => id 
		[1] => title 
		[2] => privacy 
		[3] => likes 
		[4] => views 
		[5] => comments 
		[6] => timestamp */
	
	$title 			= $Post[1];
	$privacy 		= $Post[2];
	$comment_count  = $Post[5];
	$views          = $Post[4];

	return ($privacy == 'public') && ($comment_count > 10) &&($views > 9000);
	}
	private function csv_to_array($csv_file){
		//Inputs: csv file object
		//Outputs: 2d array of csv rows and columns
		$output_array     = array();
		$csv_object       = fopen($csv_file, "r");
		if(empty($csv_object) === false) {
	    	while(($data = fgetcsv($csv_object, 1000, ",")) !== FALSE){
	        	$output_array[] = $data;
	    	}	
	    	fclose($csv_object);
		}
		return $output_array;
	}


	private function array_to_json_file($input_array,$output_filename,$detail){
	//Input: 2d array
	//Output: json file
		/*
			[0] => id 
			[1] => title 
			[2] => privacy 
			[3] => likes 
			[4] => views 
			[5] => comments 
			[6] => timestamp */
		
		$assoc_array = array();
		if ($detail == 'full'){
			foreach($input_array as $arr){
				$assoc_array[$arr[0]] = array( 
					 'title'     => $arr[1],
		    		 'privacy'   => $arr[2],
		    		 'likes'     => $arr[3],
		    		 'views'     => $arr[4],
		    		 'comments'  => $arr[5],
		    		 'timestamp' => $arr[6]);

			}	
		} 
		else if ($detail == 'single'){
			foreach($input_array as $arr)
				$assoc_array[] = array("id" => $arr[0]);		
		}
		$output_file_object  = fopen($output_filename,'w');
		fwrite($output_file_object, json_encode($assoc_array, JSON_FORCE_OBJECT));
		fclose($output_file_object);

	}

	function __construct($p_csv ='var/www/html/cafemedia/oo/posts.csv',$tp_csv ='var/www/html/cafemedia/oo/top_posts.csv',$op_csv='var/www/html/cafemedia/oo/other_posts.csv',$dtp_csv = 'daily_top_posts.csv') 
    { 
		$this->top_posts_csv  	   = fopen($tp_csv,'w') or die("can't open file ".$tp_csv);
		$this->other_posts_csv 	   = fopen($op_csv,'w') or die("can't open file ".$op_csv);
    	$this->daily_top_posts_csv = fopen($dtp_csv,'w') or die("can't open file ".$dtp_csv);

    	$this->Posts  = $this->csv_to_array($p_csv);
     	

		

    }

    function genFiles($mode,$detail)
    {   
    	
    	/* Create arrays to store filtered posts before exporting to 
     	csv/json files*/
		$top_posts       = array();
		$other_posts     = array();
		$daily_top_posts = array();

		/* remove csv header row from input multi-array 
		as useless for filtering*/
		$header = array_shift($this->Posts);





    	/* Main Filter: Seperate input 'Posts' array rows into 'top posts', 'daily top posts' and
		'other' posts*/
		/* Loop through each row and seperate whether row goes to 
		top post or other post*/

		foreach ($this->Posts as $Post){

			if ($this->is_top_post($Post)){
			$top_posts[] = $Post;
			$daily_top_posts = $this->is_daily_top_post($Post,$daily_top_posts);
			}
			else{
				$other_posts[] = $Post;
			}
		}
		if ($mode == 'json'){
			$this->array_to_json_file($top_posts,'top_posts.json',$detail);
			$this->array_to_json_file($other_posts,'other_posts.json',$detail);
			$this->array_to_json_file($daily_top_posts,'daily_top_posts.json',$detail);
		} elseif ($mode == 'csv'){
			$this->array_to_csv($top_posts,$this->top_posts_csv,$header,$detail);
			$this->array_to_csv($other_posts,$this->other_posts_csv,$header,$detail);
			$this->array_to_csv($daily_top_posts,$this->daily_top_posts_csv,$header,$detail);
		}
      
    }

}

?>