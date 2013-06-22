<?php 
	include 'api.php';
	include 'sort.php';
	$apiKey = '<echo-net-api-here';
	
	$api = new api();
	
	//TODO : If search not found do suggest	
	//http://developer.echonest.com/api/v4/artist/suggest?api_key=7EYFZFIYDBJKNOL3R&name=rad&results=5
	//Stop loading if artist doesnt exist
	
	//search artist
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		"artists": {
	//		  "artist": [
	//			{
	//			  "name": "Radiohead",
	//			  "id": "ARH6W4X1187B99274F"
	//			}
	//		  ]
	//		}
	//	  }
	//	}
	if ( $artist ) {
		$artist = preg_replace('/\s+/', '', $artist);
		$api->url = 'http://developer.echonest.com/api/v4/artist/search?api_key='.$apiKey.'&format=json&name='.$artist.'&results=1';
		try {
		$search = $api->get_api_data();
		} catch (Exception  $e ) {
				echo 'Caught exception: ',  $e->getMessage(), "\n";
		} 

		foreach ( $search->response->artists as $sec){
			$artistID = $sec->id;
		}
	}

	//biographies response
	//	{
	//	"response": {
	//		"status": {
	//			"version": "4.2",
	//			"code": 0,
	//			"message": "Success"
	//		},
	//		"start": 0,
	//		"total": 12,
	//		"biographies": [{
	//			"text": "\"Radiohead\" are an English alternative rock band from Abingdon, Oxfordshire, formed in 1985. The band consists of Thom Yorke (vocals, guitars, piano), Jonny Greenwood (guitars, keyboards, other instruments), Ed O'Brien (guitars, backing vocals), Colin Greenwood (bass, synthesisers) and Phil Selway (drums, percussion).",
	//			"site": "wikipedia",
	//			"url": "http://en.wikipedia.org/wiki/Radiohead",
	//			"license": {
	//				"type": "cc-by-sa",
	//				"attribution": "n/a",
	//				"url": ""
	//			}
	//		}]
	//	}
	//}
	//	foreach ( $data->response->biographies as $bio){
	//	echo $bio->text;
	//	echo $bio->text;
	//}
	
	$api->url = 'http://developer.echonest.com/api/v4/artist/biographies?api_key='.$apiKey.'&id='.$artistID.'&format=json&results=1&start=0&license=cc-by-sa';
	try {
	$bioData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}

	//images reponse
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		  "start": 0,
	//		  "total": 121,
	//		  "images": [
	//			{
	//			  "url": "http://userserve-ak.last.fm/serve/_/174456.jpg",
	//			  "license": {
	//				"url": "",
	//				"attribution": "",
	//				"type": "unknown"
	//			  }
	//			}
	//		  ]
	//		}
	//	}
	$api->url = 'http://developer.echonest.com/api/v4/artist/images?api_key='.$apiKey.'&id='.$artistID.'&format=json&results=1&start=0&license=unknown';
	//result = number of picture
	try {
	$imagesData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//foreach ($imagesData->response->images as $img){
	//	echo '<img src='.$img->url.' ><br>';
	//}
	
	//profile response
	//	{
	//		"response": {
	//			"artist": {
	//				"artist_location": {
	//					"city": "Abingdon",
	//					"country": "United Kingdom",
	//					"location": "Abingdon, England, GB",
	//					"region": "England"
	//				},
	//				"id": "ARH6W4X1187B99274F",
	//				"name": "Radiohead"
	//			},
	//			"status": {
	//				"code": 0,
	//				"message": "Success",
	//				"version": "4.2"
	//			}
	//		}
	//	}
	$api->url = 'http://developer.echonest.com/api/v4/artist/profile?api_key='.$apiKey.'&id='.$artistID.'&format=json&bucket=artist_location';
	try {
	$profileData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	$artist_name = $profileData->response->artist->name;
	//$loc = $locationData->response->artist->artist_location;
	//echo $loc->city;
	//echo $loc->country;
	//echo $loc->location;
	//echo $loc->region;
	
	//similar response
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		  "artists": [
	//			{
	//			  "name": "Thom Yorke",
	//			  "id": "ARH1N081187B9AC562"
	//			}
	//		  ]
	//	  }
	//	}
	$api->url = 'http://developer.echonest.com/api/v4/artist/similar?api_key='.$apiKey.'&id='.$artistID.'&format=json&results=20&start=0';
	try {
	$similarData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//foreach( $similarData->response->artists as $sim){
	//	echo $sim->name.'<br>';
	//}
	
	//twitter handle response
	//	{
	//		"response": {
	//			"status": {
	//				"version": "4.2",
	//				"code": 0,
	//				"message": "Success"
	//			},
	//			"artist": {
	//				"id": "ARH6W4X1187B99274F",
	//				"name": "Radiohead"
	//				"twitter": "radiohead"
	//			}
	//		}
	//	}
	
	$api->url = 'http://developer.echonest.com/api/v4/artist/twitter?api_key='.$apiKey.'&id='.$artistID.'&format=json';
	try {
	$twitter = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//echo $twitter->response->artist->twitter;
	
	//url response
	//	{
	//		  "response": {
	//			"status": {
	//			  "code": "0",
	//			  "message": "Success",
	//			  "version": "4.2"
	//			},
	//			"urls": {
	//				"lastfm_url": "http://www.last.fm/music/Radiohead",
	//				"aolmusic_url": "http://music.aol.com/artist/radiohead",
	//				"myspace_url": "http://www.myspace.com/radiohead",
	//				"amazon_url": "http://www.amazon.com/gp/search?ie=UTF8&keywords=Radiohead&tag=httpechonecom-20&index=music",
	//				"itunes_url": "http://itunes.com/Radiohead",
	//				"mb_url": "http://musicbrainz.org/artist/a74b1b7f-71a5-4011-9441-d0b5e4122711.html"
	//			}
	//		  }
	//	}
	$api->url = 'http://developer.echonest.com/api/v4/artist/urls?api_key='.$apiKey.'&id='.$artistID.'&format=json';
	try {
	$urlData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//echo 'Official : '.$urlData->response->urls->official_url.'<br>';
	//echo 'Last.fm : '.$urlData->response->urls->lastfm_url.'<br>';
	//echo 'Myspace : '.$urlData->response->urls->myspace_url.'<br>';
	//echo 'Amazon : '.$urlData->response->urls->amazon_url.'<br>';
	//echo 'iTunes : '.$urlData->response->urls->itunes_url.'<br>';
	//echo 'Music Brainz : '.$urlData->response->urls->mb_url.'<br>';
	
	// video response
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		  "start": 0,
	//		  "total": 121,
	//		  "video": [
	//			{
	//			  "title": "Radiohead - Pyramid Song",
	//			  "url": "http://youtube.com/watch?v=3M_Gg1xAHE4",
	//			  "site": "youtube",
	//			  "date_found": "2009-12-13T16:33:21",
	//			  "image_url": "http://i4.ytimg.com/vi/3M_Gg1xAHE4/default.jpg",
	//			  "id": "5da7ac8c7a1483af73eedf6f7f498307"
	//			}
	//		  ]
	//		}
	//	}


	$api->url = 'http://developer.echonest.com/api/v4/artist/video?api_key='.$apiKey.'&id='.$artistID.'&format=json&results=30&start=0';
	try {
	$videoData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	

		
	//foreach ( $videoData->response->video as $vid){
	//	echo $vid->url.'<br>';
	//}
	
	
	//news response
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		  "start": 0,
	//		  "total": 121,
	//		  "news": [
	//			{
	//			  "name": "First Listen: The National High Violet",
	//			  "url": "http://www.drownedinsound.com/news/4139479-first-listen--the-national-high-violet",
	//			  "date_posted": "2010-04-12T00:00:00",
	//			  "date_found": "2010-04-12T06:15:29",
	//			  "summary": " off a new-found sophistication (think of how <span>Radiohead</span>'s rhythm section developed over their first five records). Menacing cellos saw a triplet at a time, rising up like tall buildings around you, then rest. The contrast between Berninger's minimal, one-note vocals and the rising volume of the layers and layers of instruments builds tension until finally the singer shifts key, recognizing the urgency... and then the music dissolves into an atonal coda.  'Afraid of Everyone' Opening ... ",
	//			  "id": "484a6108e649f0251c182381e401f0c6"
	//			}
	//		  ]
	//		}
	//	}	
	$api->url = 'http://developer.echonest.com/api/v4/artist/news?api_key='.$apiKey.'&id='.$artistID.'&format=json&results=3&start=0';
	try {
	$newsData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//foreach ( $newsData->response->news as $news) {
	//	echo $news->url.'<br>';
	//	echo $news->summary.'<br><br>';
	//}
	
	//song response
	//	{
	//	  "response": {
	//		"status": {
	//		  "code": "0",
	//		  "message": "Success",
	//		  "version": "4.2"
	//		},
	//		  "start": 0,
	//		  "total": 121,
	//		  "songs": [
	//			{
	//			  "id": "SOXZYYG127F3E1B7A2",
	//			  "title": "Karma police"
	//			},
	//			{
	//			  "id": "SOXZABD127F3E1B7A2",
	//			  "title" : "Creep"
	//			}
	//		  ]
	//		}
	//	  }
	//	}
	$api->url ='http://developer.echonest.com/api/v4/artist/songs?api_key=7EYFZFIYDBJKNOL3R&id=ARH6W4X1187B99274F&format=json&start=0&results=2';
	try {
	$songData = $api->get_api_data();
	} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
?>