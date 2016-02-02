 
 <?php

 //echo "v10 removes - IMDb addition from IMDB -sniffed titles";
 
 class WPMT_Imdb
    {
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
            $url  = "http://www.google.com/search?hl=en&q=" . urlencode($str) . "+site%3Aimdb.com&btnI=I%27m+Feeling+Lucky";
            $html = $this->geturl($url);
            if(stripos($html, "302 Moved") !== false)
                $html = $this->geturl($this->match('/HREF="(.*?)"/ms', $html, 1));

            $arr = array();
            $arr['kind'] = 'movie';
            $arr['id'] = $this->match('/poster.*?(tt[0-9]+)/ms', $html, 1);
            $arr['title'] = $this->match('/<title>(.*?)<\/title>/ms', $html, 1);
            $arr['title'] = preg_replace('/\([0-9]+\)/', '', $arr['title']);
			//remove - IMDb
			$arr['title'] = preg_replace('/ - IMDb/', '', $arr['title']);
			//update to how google displays results
			$arr['title'] = preg_replace('/ site:imdb.com - Google Search/', '', $arr['title']);
            $arr['title'] = trim($arr['title']);
            $arr['rating'] = $this->match('/([0-9]\.[0-9])\/10/ms', $html, 1);
            $arr['director'] = trim(strip_tags($this->match('/Director:(.*?)<\/a>/ms', $html, 1)));
            $arr['release_date'] = $this->match('/([0-9][0-9]? (January|February|March|April|May|June|July|August|September|October|November|December) (19|20)[0-9][0-9])/ms', $html, 1);
            $arr['plot'] = trim(strip_tags($this->match('/Plot:(.*?)<a/ms', $html, 1)));
            $arr['genres'] = $this->match_all('/Sections\/Genres\/(.*?)[\/">]/ms', $html, 1);
            $arr['genres'] = array_unique($arr['genres']);
            $theposter = $this->match('/<a.*?name=.poster.*?src=.(.*?)(\'|")/ms', $html, 1);
			$theposter = explode("@@", $theposter);
			$arr['poster'] = $theposter[0] . "@@._V1._SX134_SY196_.jpg";
			$theimage = $this->match('/<img.*?width=.90.*?src=.(.*?)(\'|")/ms', $html, 1);
			$theimage = explode("@@", $theimage);
			if($theimage) {
			$arr['image640'] = $theimage[0] . "@@._V1._SX640_SY400_.jpg";
			$arr['image480'] = $theimage[0] . "@@._V1._SX480_SY300_.jpg";
			}
/*
 * SAMPLE OUTPUTS:
 * MV5BMTI3MzgxNTY3MF5BMl5BanBnXkFtZTcwMjEwMjU0Mw@@._V1._CR170,0,683,683_SS90_.jpg
 * MV5BMTI3MzgxNTY3MF5BMl5BanBnXkFtZTcwMjEwMjU0Mw@@._V1._SX640_SY427_.jpg
 *
 * MV5BMTMzMjczNTU2NV5BMl5BanBnXkFtZTcwNzAwMjU0Mw@@._V1._CR341,0,1365,1365_SS90_.jpg
 * MV5BMTMzMjczNTU2NV5BMl5BanBnXkFtZTcwNzAwMjU0Mw@@._V1._SX640_SY427_.jpg
 *
 * http://ia.media-imdb.com/images/M/MV5BMTEwNzQ2ODY5MzBeQTJeQWpwZ15BbWU3MDU1MjQ4NDM@._V1._CR44,33,177,133_SX120_SY90_BO120,0,0,0_PIimdb-blackband,BottomLeft,120,-119_PIimdb-bluebutton,BottomLeft,213,-121_CR120,120,120,90_ZAClip,4,61,19,120,verdenab,8,255,255,255,1_FMpng_.png
 * http://ia.media-imdb.com/images/M/MV5BMjQ3NTkyNjQzM15BMl5BanBnXkFtZTcwODc1NDI4Mg@@._V1._SX97_SY140_.jpg
 * http://ia.media-imdb.com/images/M/MV5BMjQ3NTkyNjQzM15BMl5BanBnXkFtZTcwODc1NDI4Mg@@._V1._SX640_SY927_.jpg
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
    }
	
	?>