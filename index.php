<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Neverpuddi playlist</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../playlist/inc/css/custom.css" rel="stylesheet">
    <link href="../assets/css/bootstrap-responsive.css" rel="stylesheet">


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Fav and touch icons 
    <link rel="shortcut icon" href="../assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png"> -->
  </head>

	<?php 
		$artist = $_GET['artist'];
		$artistID = $_GET['id'];
		$vnum = ($_GET['vid'])?$_GET['vid']:0;;
	?>
  <body>

    <div class="container-narrow">

      <div class="masthead row hlink">
        <h3 ><a href="http://obscureserver.com">Neverpuddi Playlist</a></h3>
		<h4 class="muted">Listen to your favorite artists, for free. </h4>
      </div>

      <hr>

	<?php if ( !$artist && !$artistID): ?>
		
      <div class="jumbotron">
        <h4>Who would you like to listen to?</h4>
		<form>
		  <fieldset>
			<input name ="artist"type="text" placeholder="Type something"><br>
			<button type="submit" class="btn-success">Submit</button>
		  </fieldset>
		</form>
      </div>

     <?php else: ?>
	<?php 
		include './inc/data.php'; 
		include './inc/function.php';
	?>
	
    <?php if ( $artistID &&  $videoData->response->total != 0): ?> 

	 <div class="row-fluid marketing">

<!-- TOP LEFT ----------------------------------------------------------------------------------------------------------------->
        <div class="span6">
          <h4><?php echo $artist_name?></h4>
          <p>
			<?php 		
				foreach ($imagesData->response->images as $img){
				echo '<img src='.$img->url.' width="200"><br>';
			}?>
		  </p>

          <h4>Location</h4>
          <p>
		  <?php
				$loc = $profileData->response->artist->artist_location;
				if ($loc->city) echo "City : ".$loc->city.'<br>';
				if ($loc->region) echo "State / Region : ".$loc->region.'<br>';
				if ($loc->country) echo "Country : ".$loc->country.'<br>';
		  ?>
		  </p>

          <h4>Links</h4>
          <p>
		 
		  <?php
			//todo hide if value is null
		  	echo '<a href='.$urlData->response->urls->official_url.'>Official </a>';
		  	echo '<a href='.$urlData->response->urls->lastfm_url.'> lastfm </a>';
		  	echo '<a href='.$urlData->response->urls->myspace_url.'> myspace </a>';
		  	echo '<a href='.$urlData->response->urls->itunes_url.'> iTunes </a>';
		  ?>
		  </p>
        </div>
<!-- TOP RIGHT (PLAYER)----------------------------------------------------------------------------------------------------------------->
        <div class="span6">
            <!--<h4>Playlist</h4>
		    <h6 id="title"></h6>-->
		    <div id="player"></div>
		  	    <table class="table table-hover sidebar" >
					<thead>
						<tr>
						    <th></th>
						</tr>
					</thead>
					<tbody>
					<?php 
						$next = 0;
						$video_id_from_api = array();
						$video_data = array();

						foreach ( $videoData->response->video as $vid){
							//TODO Filter bad data
							$url = parse_url($vid->url);
							parse_str($url['query'], $query);
							array_push($video_id_from_api, $query['v']);				
						}
						//http://gdata.youtube.com/feeds/api/videos/cnMfwNMUf4U?v=2&alt=jsonc
						$ytdata = new api();
						foreach ($video_id_from_api as $video_id){
							$ytdata->url = 'http://gdata.youtube.com/feeds/api/videos/'.$video_id.'?v=2&alt=jsonc';
							try {
								$youtube_data = $ytdata->get_api_data();
							} catch (Exception  $e ) {
								echo 'Caught exception: ',  $e->getMessage(), "\n";
							}
	

							//check error and accesscontrol
							if ( $youtube_data->error || !$youtube_data->accesscontrol->embed = 'allowed' ) {
								continue;
							}
							//random data 
							if ( $youtube_data->data->totalItems ){
								continue;
							}

							//title filter
							$title = strtolower($youtube_data->data->title);																// lower case the whole title string
							$title = preg_replace('/[^A-Za-z0-9\. ]/', '', $title);																// delete all special characters
			
							$title = str_replace (strtolower($artist_name), "", $title);
							$del_song_arr = array ('cover','reply','epk','live','interview','making');																// delete the song from the list if words match on the title
							$remove_str_arr = array ('by','official','music','video','with','lyrics','description','hd');			// remove the word from the title if word/s match on the title
							// Check if the video is good, no cover and other not okay content.
							if ( check_str($del_song_arr, $title) ) {
								continue;
							}
							// Remove some words we don't to be display in the title.
							foreach ($remove_str_arr as &$ar) {
								$ar = '/\b' . preg_quote($ar, '/') . '\b/';
							}
							$title = preg_replace($remove_str_arr, '', $title);
							
							//Final push for video data array hooraaa
							array_push($video_data, array (
								"id"=> $youtube_data->data->id,
								"title"=> ucwords($title),
								"viewcount" => $youtube_data->data->viewCount,
							));
						}
						sort_array_of_array($video_data, 'viewcount');
						foreach ( $video_data as $vid){
							echo '<tr id='.$vid['id'].'>';
								echo '<td href="javascript:void(0);" onclick="nextbybtn(\''.$vid['id'].'\','.$next++.');">'. $next .'. '. $vid['title'] .'</td>';
							echo '</tr>';
						}				
					?>
					</tbody>
				</table>
        </div>
		
<!-- CENTER BOTTOM ----------------------------------------------------------------------------------------------------------------->		
		<div class="span12">
			<h4>Similar artist</h4>
			<p>
				<?php 	
					foreach( $similarData->response->artists as $sim){
						echo '<a href=/?id='.$sim->id.'>'.$sim->name.'</a> - ';
					}
				?>
			</p>
        </div>
<!-- ERROR PAGE ----------------------------------------------------------------------------------------------------------------->		
			 <?php else: ?>
			 <div class="row">
				<p><?php echo "Sorry! We could not find any songs."; ?></p>
			</div>
		<?php endif; ?>
     <?php endif; ?>
      </div>


      <div class="footer row" >
        <p><a href="http://the.echonest.com" > <img src="./inc/echo.png"> </a></p>
      </div>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster
    <script src="../assets/js/jquery.js"></script>
    <script src="../assets/js/bootstrap-transition.js"></script>
    <script src="../assets/js/bootstrap-alert.js"></script>
    <script src="../assets/js/bootstrap-modal.js"></script>
    <script src="../assets/js/bootstrap-dropdown.js"></script>
    <script src="../assets/js/bootstrap-scrollspy.js"></script>
    <script src="../assets/js/bootstrap-tab.js"></script>
    <script src="../assets/js/bootstrap-tooltip.js"></script>
    <script src="../assets/js/bootstrap-popover.js"></script>
    <script src="../assets/js/bootstrap-button.js"></script>
    <script src="../assets/js/bootstrap-collapse.js"></script>
    <script src="../assets/js/bootstrap-carousel.js"></script>
    <script src="../assets/js/bootstrap-typeahead.js"></script> -->
    <script>
      // 2. This code loads the IFrame Player API code asynchronously.
      var tag = document.createElement('script');

	  var num = <?php echo $vnum; ?>;
	  var len = 0;

	  	<?php
		$js_array = json_encode($video_data);
		echo "var varIDjs = ". $js_array . ";\n";
		?>
		len = varIDjs.length - 1;
		if ( len < num ) {
			num = 0;
		}
		var id = varIDjs[num]['id'];
      tag.src = "https://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

      // 3. This function creates an <iframe> (and YouTube player)
      //    after the API code downloads.
      var player;
      function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
          height: '340',
          width: '480',
          videoId: id,
		  playerVars: { 'autoplay': 1, 'controls': 1,  'showinfo': 0, },
          events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
          }
        });
      }

      // 4. The API will call this function when the video player is ready.
      function onPlayerReady(event) {
        event.target.playVideo();
		//document.getElementById('title').innerHTML = varIDjs[num]['title'];
		$("#" + varIDjs[num]['id']).addClass('highlight');
		
      }
		
      // 5. The API calls this function when the player's state changes.
      //    The function indicates that when playing a video (state=1),
      //    the player should play for six seconds and then stop.
      function onPlayerStateChange(event) {
			if (event.data == YT.PlayerState.ENDED) {
				$("#" + varIDjs[num]['id']).removeClass('highlight');
				num = num + 1;
				if ( num > len ) {
					num = 0;
				} 
				next(num);
			}
      }
	  	function next(n) {
			player.loadVideoById(varIDjs[n]['id']);
			player.playVideo();
			//document.getElementById('title').innerHTML = varIDjs[num]['title'];
			$("#" + varIDjs[num]['id']).addClass('highlight');
		}
      	function nextbybtn(s1, s2){
			$("#" + varIDjs[num]['id']).removeClass('highlight');
			num = s2 ;
			//document.getElementById('title').innerHTML = varIDjs[num]['title'];
			$("#" + varIDjs[num]['id']).addClass('highlight');
			player.loadVideoById(s1,0);
			player.playVideo();
		}

    </script>
  </body>
</html>
