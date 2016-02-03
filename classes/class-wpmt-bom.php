 
 <?php


 class WPMT_Bom {
    public $info;

    function __construct($str = null)
    {
        if(!is_null($str))
            $this->autodetect($str);
    }

    function autodetect($str)
    {
        // Attempt to cleanup $str in case it's a filename ;-)
        $str = pathinfo($str, PATHINFO_FILENAME);
        $str = $this->normalize($str);

        // Is it a movie or tv show?
        if(preg_match('/s[0-9][0-9]?.?e[0-9][0-9]?/i', $str) == 1)
            $this->info = $this->getEpisodeInfo($str);
        else
            $this->info = $this->get_movie_info($str);

        return $this->info;
    }

    function getEpisodeInfo($str)
    {
        $arr = array();
        $arr['kind'] = 'tv';
        return $arr;
    }

    function get_movie_info($str)
    {
        $str  = str_ireplace('the ', '', $str);
        $url  = "http://www.google.com/search?hl=en&q=" . urlencode($str) . "+site%3Aboxofficemojo.com/movies&btnI=I%27m+Feeling+Lucky";
        $html = $this->geturl($url);
        if(stripos($html, "302 Moved") !== false)
            $html = $this->geturl($this->match('/HREF="(.*?)"/ms', $html, 1));

        $arr = array();
        $arr['kind'] = 'movie';
        $arr['id'] = $this->match('/poster.*?(tt[0-9]+)/ms', $html, 1);
        $arr['title'] = $this->match('/<title>(.*?)<\/title>/ms', $html, 1);
        $arr['title'] = preg_replace('/\([0-9]+\)/', '', $arr['title']);
        $arr['title'] = trim($arr['title']);
        $arr['rating'] = $this->match('/([0-9]\.[0-9])\/10/ms', $html, 1);
        $arr['director'] = trim(strip_tags($this->match('/Director:(.*?)<\/a>/ms', $html, 1)));
        $arr['release_date'] = $this->match('/([0-9][0-9]? (January|February|March|April|May|June|July|August|September|October|November|December) (19|20)[0-9][0-9])/ms', $html, 1);
        $arr['plot'] = trim(strip_tags($this->match('/Plot:(.*?)<a/ms', $html, 1)));
        $arr['genres'] = $this->match_all('/Sections\/Genres\/(.*?)[\/">]/ms', $html, 1);
        $arr['genres'] = array_unique($arr['genres']);
        $theposter = $this->match('/(src=".*_)poster.\w+"/', $html, 1);
		//$theposter = $this->match('/src="[^\s]*?poster/', $html, 1);
		//$theposter = explode("@@", $theposter);
		if ($theposter) {
			$theposter = preg_replace('/src="/', "", $theposter) . "poster.jpg";
			$arr['poster'] = $theposter;
			echo "auto-poster: " . $theposter . "<br />";
		}

		$theimage = $this->match('/<img.*?width=.90.*?src=.(.*?)(\'|")/ms', $html, 1);
		//$theimage = explode("src=", $theimage);
		if($theimage) {
		$arr['image640'] = "http://boxofficemojo.com" . preg_replace("/idx\//", "", $theimage);
		echo "auto-image: http://boxofficemojo.com" . preg_replace("/idx\//", "", $theimage) . "<br />";
		}
/*MV5BMTI3MzgxNTY3MF5BMl5BanBnXkFtZTcwMjEwMjU0Mw@@._V1._CR170,0,683,683_SS90_.jpg
MV5BMTI3MzgxNTY3MF5BMl5BanBnXkFtZTcwMjEwMjU0Mw@@._V1._SX640_SY427_.jpg

MV5BMTMzMjczNTU2NV5BMl5BanBnXkFtZTcwNzAwMjU0Mw@@._V1._CR341,0,1365,1365_SS90_.jpg
MV5BMTMzMjczNTU2NV5BMl5BanBnXkFtZTcwNzAwMjU0Mw@@._V1._SX640_SY427_.jpg

http://ia.media-imdb.com/images/M/MV5BMTEwNzQ2ODY5MzBeQTJeQWpwZ15BbWU3MDU1MjQ4NDM@._V1._CR44,33,177,133_SX120_SY90_BO120,0,0,0_PIimdb-blackband,BottomLeft,120,-119_PIimdb-bluebutton,BottomLeft,213,-121_CR120,120,120,90_ZAClip,4,61,19,120,verdenab,8,255,255,255,1_FMpng_.png


http://ia.media-imdb.com/images/M/MV5BMjQ3NTkyNjQzM15BMl5BanBnXkFtZTcwODc1NDI4Mg@@._V1._SX97_SY140_.jpg
http://ia.media-imdb.com/images/M/MV5BMjQ3NTkyNjQzM15BMl5BanBnXkFtZTcwODc1NDI4Mg@@._V1._SX640_SY927_.jpg

*/

        $arr['cast'] = array();
        foreach($this->match_all('/class="nm">(.*?\.\.\..*?)<\/tr>/ms', $html, 1) as $m)
        {
            list($actor, $character) = explode('...', strip_tags($m));
            $arr['cast'][trim($actor)] = trim($character);
        }

        return $arr;
    }

    // ****************************************************************

    function normalize($str)
    {
        $str = str_replace('_', ' ', $str);
        $str = str_replace('.', ' ', $str);
        $str = preg_replace('/ +/', ' ', $str);
        return $str;
    }

    function geturl($url, $username = null, $password = null)
    {
        $ch = curl_init();
        if(!is_null($username) && !is_null($password))
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' .  base64_encode("$username:$password")));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    function match_all($regex, $str, $i = 0)
    {
        if(preg_match_all($regex, $str, $matches) === false)
            return false;
        else
            return $matches[$i];

    }

    function match($regex, $str, $i = 0)
    {
        if(preg_match($regex, $str, $match) == 1)
            return $match[$i];
        else
            return false;
    }

	function attach_image ( $post_id ) {
	// $filename should be the path to a file in the upload directory.
		$filename = '/path/to/uploads/2013/03/filename.jpg';

	// The ID of the post this attachment is for.
		$parent_post_id = 37;

	// Check the type of file. We'll use this as the 'post_mime_type'.
		$filetype = wp_check_filetype( basename( $filename ), null );

	// Get the path to the upload directory.
		$wp_upload_dir = wp_upload_dir();

	// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

	// Insert the attachment.
		$attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

	// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

	// Generate the metadata for the attachment, and update the database record.
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $parent_post_id, $attach_id );
	}

	function _import_photo( $postid, $photo_url ) {
		$post = get_post( $postid );
		$photo_name = 'test';

		if( empty( $post ) )
			return false;

		if( !class_exists( 'WP_Http' ) )
			 include_once( ABSPATH . WPINC. '/class-http.php' );

		$photo = new WP_Http();
		//$photo = $photo->request( 'http://example.com/photos/directory/' . $photo_name . '.jpg' );
		$photo = $photo->request( $photo_url );
		if( $photo['response']['code'] != 200 )
			return false;

		$attachment = wp_upload_bits( $photo_name . '.jpg', null, $photo['body'], date("Y-m", strtotime( $photo['headers']['last-modified'] ) ) );
		if( !empty( $attachment['error'] ) )
			return false;

		$filetype = wp_check_filetype( basename( $attachment['file'] ), null );

		$postinfo = array(
			'post_mime_type'	=> $filetype['type'],
			'post_title'		=> $post->post_title . ' BOMojo',
			'post_content'		=> '',
			'post_status'		=> 'inherit',
		);
		$filename = $attachment['file'];
		$attach_id = wp_insert_attachment( $postinfo, $filename, $postid );

		if( !function_exists( 'wp_generate_attachment_data' ) )
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
		return $attach_id;
	}//end function import_photos

 }//end class
