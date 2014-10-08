<?php
class youtube {
    private $_apiKey = '';
    
    private $_artistName;
    private $_id = array();
	private $_title = array();
	private $_likeCount = array();
	private $_dislikeCount = array();
	private $_viewCount = array();
	private $_image = array();
    private $_player = array();
    private $_numVid;
    private $_error;
	function youtube($v, $artistName){
	    //$v array of id
	    $this->_artistName = $artistName;
	    foreach($v as $s){
	        $tmp .= $s .',';
	    }
	    $tmp = substr($tmp, 0, strlen($tmp) - 1);
	    $url = 'https://www.googleapis.com/youtube/v3/videos?id='.$tmp.'&key='.$this->_apiKey.'&part=snippet,statistics,player';

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $data = curl_exec($ch);
        curl_close($ch);
		$data = json_decode($data);
		if (!$data) {
			throw new Exception('Youtube: No data found. ');
			return;
		}
		if($data->error){

		}
        
		foreach($data->items as $v){
		    $c = $this->sanitizeName($v->snippet->title);
	    	if($c){
    		    array_push($this->_id, $v->id);
    		    array_push($this->_title, $c);
    		    array_push($this->_likeCount, $v->statistics->likeCount);
    		    array_push($this->_dislikeCount, $v->statistics->dislikeCount);
    		    array_push($this->_viewCount, $v->statistics->viewCount);
    		    array_push($this->_image, $v->snippet->thumbnails->default->url);
    		    $num++;
	    	}
		}
		$this->_numVid = $num;
        
	}
	public function getID($i){
	    return $this->_id[$i];
	}
    public function getTitle($i){
        return $this->_title[$i];
    }
    public function getLikeCount($i){
        return $this->_likeCount[$i];
    }
    public function getDislikeCount($i){
        return $this->_dislikeCount[$i];
    }
    public function getViewCount($i){
        return $this->_viewCount[$i];
    }
    public function getImage($i){
        return $this->_image[$i];
    }
    public function getPlayer($i){
        return $this->_player[$i];
    }
    public function getNumVid(){
        return $this->_numVid;
    }
    function check_str($arr, $title) {
    	foreach ( $arr as $ar ) {
    		if (strpos($title, $ar)) {
    			return 1;
        		}
        	}
    }
    function sanitizeName($title){
        //title filter
		$title = strtolower($title);																// lower case the whole title string
		$title = preg_replace('/[^A-Za-z0-9\. ]/', '', $title);																// delete all special characters
		$title = str_replace (strtolower($this->_artistName), "", $title);
		$del_song_arr = array ('cover','reply','epk','live','interview','making','remix');																// delete the song from the list if words match on the title
		$remove_str_arr = array ('by','official','music','video','with','lyrics','description','hd');			// remove the word from the title if word/s match on the title
		// Check if the video is good, no cover and other not okay content.
		if ( $this->check_str($del_song_arr, $title) ) {
			return 0;
		}
		// Remove some words we don't to be display in the title.
		foreach ($remove_str_arr as &$ar) {
			$ar = '/\b' . preg_quote($ar, '/') . '\b/';
		}
		$title = preg_replace($remove_str_arr, '', $title);
	    return ucwords($title);
    }

    function del_str($arr, &$title) {
    	foreach ( $arr as $ar ) {
    		str_replace ($ar, "", $title);
    	}
    }
}
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}
function sort_array_of_array(&$array, $subfield)
{
	$sortarray = array();
	foreach ($array as $key => $row)
	{
		$sortarray[$key] = $row[$subfield];
	}
		array_multisort($sortarray, SORT_DESC , $array);
}
?>