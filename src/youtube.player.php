	<script>
        // 2. This code loads the IFrame Player API code asynchronously.
          var tag = document.createElement('script');
    
          tag.src = "https://www.youtube.com/iframe_api";
          var firstScriptTag = document.getElementsByTagName('script')[0];
          firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    
          // 3. This function creates an <iframe> (and YouTube player)
          //    after the API code downloads.
          var player;
          var currentNum = 0;
          var jsVID =[];
    	  	<?php
        	  	for($i = 0; $i < $yt->getNumVid(); $i++){
                    echo 'jsVID.push(\''.$yt->getID($i).'\');';
        	  	}
    		?>

	     var id = jsVID[currentNum];
          
          function onYouTubeIframeAPIReady() {
            player = new YT.Player('player', {
              height: '315',
              width: '560',
              videoId: id,
              playerVars: { 'autoplay': 1,  'controls': 1 , 'enablejsapi': 1 },
              events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange,
                'onPlaybackQualityChange': onPlayerQuality
              }
            });
          }
          // 4. The API will call this function when the video player is ready.
          function onPlayerReady(event) {
            event.target.playVideo();
            $("#" + jsVID[currentNum]).addClass('highlight');
            //setInterval(updatePlayerInfo, 0);
          }
    
          function onPlayerStateChange(event) {
            if (event.data == YT.PlayerState.ENDED) {
                $("#" + jsVID[currentNum]).removeClass('highlight');
                 currentNum++;
                $("#" + jsVID[currentNum]).addClass('highlight');
                if ( currentNum >= <? echo $yt->getNumVid(); ?> ) {
					currentNum = 0;
				} 
                player.loadVideoById(jsVID[currentNum],0);
                 player.playVideo();
                //updateHTML("demo", currentNum);
            }
          }
          function onPlayerQuality(event){
            player.setPlaybackQuality("highres");
          }
            function updateHTML(elmId, value) {
                document.getElementById(elmId).innerHTML = value;
            }
          function updatePlayerInfo(){
            if (player && player.getDuration()) {
                $( "#progressbar" ).progressbar({ value: player.getCurrentTime() });
                updateHTML("CurrentTime", (player.getCurrentTime()).toFixed(2));
            }
          }
        function nextbybtn(s1,s2){
			$("#" + jsVID[currentNum]).removeClass('highlight');
			currentNum = s2;
			$("#" + jsVID[currentNum]).addClass('highlight');
			player.loadVideoById(s1,0);
			player.playVideo();
			updateHTML("demo", s2);
			
		}

        
	</script>