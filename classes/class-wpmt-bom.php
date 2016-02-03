 
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

    function get_movie_info( $str )
    {
        $str  = str_ireplace( 'the ', '', $str );
        $url  = "http://www.google.com/search?hl=en&q=" . urlencode( $str ) . "+site%3Aboxofficemojo.com/movies&btnI=I%27m+Feeling+Lucky";
        $html = $this->get_url( $url );

        if( stripos( $html, "302 Moved" ) !== false )
            $html = $this->get_url( $this->match( '/HREF="(.*?)"/ms', $html, 1 ) );

        $arr = array();

        $the_poster = $this->match( '/(src=".*_)AL.\w+"/', $html, 1 );

		if ( $the_poster ) {
			$the_poster     = preg_replace( '/src="/', "", $the_poster ) . "AL.jpg";
			$arr['poster']  = $the_poster;
		}

		$the_image  = $this->match( '/<img.*?width=.90.*?src=.(.*?)(\'|")/ms', $html, 1 );

		if( $the_image ) {
		    $the_image       = "http://boxofficemojo.com" . preg_replace( "/idx\//", "", $the_image );
            $arr['image640'] = $the_image;
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

    function get_url($url, $username = null, $password = null)
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


	function import_photo($post_id, $photo_url ) {
		//$post_id = get_post( $post_id );
		$photo_name = 'test';

		if( empty( $post_id ) )
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
			'post_title'		=> $post_id->post_title . ' BOMojo',
			'post_content'		=> '',
			'post_status'		=> 'inherit',
		);
		$filename = $attachment['file'];
		$attach_id = wp_insert_attachment( $postinfo, $filename, $post_id );

		if( !function_exists( 'wp_generate_attachment_data' ) )
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
		return $attach_id;
	}//end function import_photos

 }//end class
