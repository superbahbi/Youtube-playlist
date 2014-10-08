<?php

class echonest {
    private $apiKey = '';
	private $consumerKey = '';
	private $sharedSecret = '';
	
	private $_name;
	private $_id;
	
	private $_location;
	private $_country;
	private $_image;
	private $_officialUrl;
	private $_genre;
	private $_activeYears;
	private $_review = array();
	private $_reviewUrl = array();
	private $_reviewDate = array();
	private $_reviewImage = array();
	private $_reviewSummary = array();
	private $_reviewNum = 0;
	private $_video;
    private $_similar = array();
    
    
	function echonest($artist, $num, $amount){
	    $artist = preg_replace('/\s+/', '', $artist);
	    
	    try {
	        if($num == 0) {
	            $seacrh = $this->GetData('http://developer.echonest.com/api/v4/artist/search?api_key='.$this->apiKey.'&format=json&name='.$artist.'&results=1', 'search');
	            foreach ( $seacrh->response->artists as $s){
			        $this->_id = $s->id;
		        }
	        } else if($num = 1){
                $this->_id = $artist;
	        }
		    		} catch (Exception  $e ) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
		if($this->_id){
            try {
            $profile = $this->GetData('http://developer.echonest.com/api/v4/artist/profile?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&bucket=artist_location&bucket=genre&bucket=biographies&bucket=blogs&bucket=familiarity&bucket=hotttnesss&bucket=images&bucket=news&bucket=reviews&bucket=terms&bucket=urls&bucket=video&bucket=years_active&bucket=id:musicbrainz', 'profile');
            
            $similar = $this->GetData('http://developer.echonest.com/api/v4/artist/similar?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&results=20&start=0', 'similar');
            //$twitter = $this->GetData('http://developer.echonest.com/api/v4/artist/twitter?api_key='.$this->apiKey.'&id='.$this->id.'&format=json', 'twitter');
    		} catch (Exception  $e ) {
    			echo 'Caught exception: ',  $e->getMessage(), "\n";
    		}
            $videos = $this->GetData('http://developer.echonest.com/api/v4/artist/video?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&results='.$amount.'&start=0', 'videos');
            
            //$url = $this->GetData('http://developer.echonest.com/api/v4/artist/urls?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json', 'url');
            //$news = $this->GetData('http://developer.echonest.com/api/v4/artist/news?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&results=3&start=0', 'news');
            //$songs = $this->GetData('http://developer.echonest.com/api/v4/artist/songs?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&start=0&results=2', 'songs');
            //$bio = $this->GetData('http://developer.echonest.com/api/v4/artist/biographies?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&results=1&start=0&license=cc-by-sa', 'bio');
            //$image = $this->GetData('http://developer.echonest.com/api/v4/artist/images?api_key='.$this->apiKey.'&id='.$this->_id.'&format=json&results=1&start=0&license=unknown', 'image');

            $this->_name = $profile->response->artist->name;
            $this->_location = $profile->response->artist->artist_location->location;
            $this->_country = $profile->response->artist->artist_location->country;
            $this->_image = $profile->response->artist->images[rand(1, count($profile->response->artist->images))]->url;
            $this->_officialUrl = $profile->response->artist->urls->official_url;
            $this->_genre = $profile->response->artist->genres;
            foreach($profile->response->artist->years_active as $s){
                $this->_activeYears = $s->start;
            }
            $this->_video = $videos->response->video;
            
            foreach($profile->response->artist->reviews as $s){
                    array_push($this->_review, $s->name);
                    array_push($this->_reviewUrl, $s->url);
                    array_push($this->_reviewDate, $s->date_reviewed);
                    array_push($this->_reviewImage, $s->image_url);
                    array_push($this->_reviewSummary, $s->summary);
                    $this->_reviewNum++;
            }
            $this->_similar = $similar->response->artists;
		}
	}


	public function getID(){
	    return $this->_id;
	}
	public function getLocation(){
	    return $this->_location;
	}
	public function getCountry(){
	    return $this->_country;
	}
	public function getName(){
        return $this->_name;
	}
	public function getImage(){
	    return $this->_image;
	}
	public function getUrl(){
	    return $this->_officialUrl;
	}
	public function getGenre(){
        foreach($this->_genre as $g){
            $tmp .= ucfirst($g->name) . ', ';
        }
	    return substr($tmp, 0, strlen($tmp) - 2);
	}
	public function getActiveYears(){
	    return $this->_activeYears;
	}
	public function getReview($i){
	    return $this->_review[$i];
	}
	public function getReviewUrl($i){
	    return $this->_reviewUrl[$i];
	}
	public function getReviewDate($i){
	    return substr($this->_reviewDate[$i], 0, strlen($this->_reviewDate[$i])-9);
	}
	public function getReviewImage($i){
	    return $this->_reviewImage[$i];
	}
	public function getreviewSummary($i){
	    return $this->SummaryLimit($this->_reviewSummary[$i]);
	}
	public function getReviewNum(){
	    return $this->_reviewNum;
	}
	public function getVideo(){
	    $v = array();
	    foreach($this->_video as $s){
    	    $url = parse_url($s->url);
    		parse_str($url['query'], $query);
    		if($query){
    		    array_push($v, $query['v']);
    		}
		}
	    return $v;
	}
	public function getSimilar(){
	    return $this->_similar;
	}
	
	function GetData($url, $s){
	   $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
		$data = json_decode($data);
		if (!$data) {
			throw new Exception($s .' Echonest: No data found. ');
			return;
		}
		if($data->response->status->code != 0){
		    throw new Exception('Echonest error code '.$data->response->status->code .' '. $s);
		} 
		return $data;
	}	

	
	function SummaryLimit($x)
    {
      if(strlen($x)<=200)
      {
        return $x;
      }
      else
      {
        $y=substr($x,0,200) . ' ';
        return $y;
      }
    }


}
?>