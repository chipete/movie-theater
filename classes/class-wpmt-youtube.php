 
 <?php


 class WPMT_Youtube {

//get trailer from youtube

     function get_youtube_url($post_type, $query) {
         $pre_query = $post_query = '';

         if ($post_type == 'WPMT_Film') {
             $pre_query  = 'movieclips+trailers+';
             $post_query = '+official+trailer';
         }

         $url  = "http://www.google.com/search?hl=en&q=youtube+" . $pre_query . urlencode( $query ) . $post_query . "&btnI=I%27m+Feeling+Lucky";
         $html = $this->get_url( $url );

         if ( stripos( $html, "302 Moved" ) ) {
             //if bounce back, get the html anyway
             $html = $this->get_url( $this->get_match( '/HREF="(.*?)"/ms', $html, 1 ) );
         }

         return "" . $this->get_match( '/<link.rel=.canonical.*?href=.(.*?)(".)/m', $html, 1 );
     }



     /**
      *
      * Helper Functions
      *
      */

     function get_url( $url, $username = null, $password = null )
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

    // if there is a match, return the first match. otherwise return false
     function get_match( $regex, $str, $i = 0 )
     {
         if(preg_match($regex, $str, $match) == 1)
             return $match[$i];
         else
             return false;
     }

 } //end class