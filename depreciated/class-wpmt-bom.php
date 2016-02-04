 
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
			 include_once(ABSPATH . WPINC . '/class-http.php');

		//$photo = new WP_Http();
		//$photo = $photo->request( 'http://example.com/photos/directory/' . $photo_name . '.jpg' );
		//$photo = $photo->request( $photo_url );
		//if( $photo['response']['code'] != 200 )
		//	return false;

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


    function get_rotten_tomatoes( $title )
     {

         $api_key = 'mwwaecxfrzbnstpj7tya9b7k';
         $q = urlencode('Toy Story'); // make sure to url encode an query parameters

         // construct the query with our api_key and the query we want to make
         $endpoint = 'http://api.rottentomatoes.com/api/public/v1.0/movies.json?api_key=' . $api_key . '&q=' . $q;

         // setup curl to make a call to the endpoint
         $session = curl_init($endpoint);

         // indicates that we want the response back
         curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

         // exec curl and get the data back
         $data = curl_exec($session);

         // remember to close the curl session once we are finished retrieveing the data
         curl_close($session);

         // decode the json data to make it easier to parse the php
         $search_results = json_decode($data);
         if ($search_results === NULL) die('Error parsing json');

         // play with the data!
         $movies = $search_results->movies;

         $arr = array ();

         foreach ($movies as $movie) {
             if ($movie->title == $title) {
                 $arr['rt_rating']    = $movie->critics_score;
                 $arr['rt_title']     = $movie->title;
                 $arr['rt_consensus'] = $movie->critics_consensus;
                 $arr['rt_synopsis']  = $movie->synopsis;
             }
         }

         return $arr;
     }//end function get_rotten_tomatoes


    function update_external_fields ( $post_id ) {

         $title      = get_the_title( $post_id );

         $bom        = new WPMT_Bom();
         $bom_data   = $bom->get_movie_info( $title );

         $yt         = new WPMT_Youtube();
         $yt_data    = $yt->get_youtube_url( 'WPMT_Film', $title );

         //$rt_data    = $this->get_rotten_tomatoes( $title );

         if ( $yt_data ) {
             update_field( 'field_56a1178eb02a7', $yt_data, $post_id );          //youtube_url
         }

         if ( $bom_data['poster'] ) {
             $bom_poster_id = $bom->import_photo( $post_id, $bom_data['poster'] );
             update_field( 'field_56a113b2b02a2', $bom_poster_id, $post_id );    //poster
         }

         /*
          * This image call does not work, BOM no longer has photos. Looking into Rotten Tomatoes API (See below) or omDb api or iMdb api instead

          if ( $bom_data['image640'] ) {
             $bom_image_id   = $bom->import_photo( $post_id, $bom_data['image640'] );
             update_field( 'field_56a1147cb02a3', $bom_image_id, $post_id );     //image
         }

         //waiting for RT API key to be approved (pending)

         if ( $rt_data ) {
             update_field( 'field_56b0a4dd84ab1', $rt_data['rt_rating'], $post_id );     //rt rating
             update_field( 'field_56b0a4f084ab2', $rt_data['rt_consensus'], $post_id );  //rt consensus
         }
         */

     } //end function

 }//end class
