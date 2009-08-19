<?php
/*
 * EDK Feed Syndication v1.7
 * based on liq's feed syndication mod v1.5
 *
 */

@set_time_limit(0);
@ini_set('memory_limit',999999999);
$feedversion = "v1.7";

require_once( "common/includes/class.kill.php" );
require_once( "common/includes/class.parser.php" );
require_once( "common/includes/class.comments.php" );

//! EDK Feed Syndication fetcher class.

/*! This class is used to fetch the feed from another EDK board. It adds all
 * fetched kills to the board and returns the id of the highest kill fetched.
 */
class Fetcher
{
//! Construct the Fetcher class and initialise variables.
	function Fetcher()
	{
		$this->lastkllid_ = 0;
		$this->finalkllid_ = 0;
		//                $this->trackurl_ = '';
		$this->trackfriend_ = '';
		$this->trackkey_ = '';
		$this->tracklast_ = 0;
		$this->combined_ = false;
		$this->insideitem = false;
		$this->tag = "";
		$this->title = "";
		$this->description = "";
		$this->link = "";
		$this->x=0;
	}
	//! Fetch a new feed.

        /*! Use the input parameters to fetch a feed, parse it and add new kills
         * to the db.
         * \param $url The base URL of the feed to fetch
         * \param $str The query string to add to the base URL.
         * \param $trackfriend Either 'on' or blank. Defines whether to fetch
         * friendly kills
         * \param $trackkey The configuration key to use when storing feed in
         * the db.
         * \return HTML output summarising the results of the fetch.
         */

	function grab($url, $str, $trackfriend = '', $trackkey = '')
	{
		global $feedversion;
		//                $this->trackurl_ = $trackurl;
		$this->trackfriend_ = $trackfriend;
		$this->trackkey_ = $trackkey;
		$this->x=0;
		$fetchurl = $url.$str."&board=".urlencode(KB_TITLE);
		if(strpos($fetchurl, 'apikills=1')) $this->apikills = true;
		else $this->apikills = false;
		if(!strpos($fetchurl,'?')) $fetchurl =
				substr_replace($fetchurl,'?', strpos($fetchurl,'&'),0);
		$this->uurl = $url;
		// only lists fetched with lastkllid are ordered by id.
		if(strpos($fetchurl, 'lastkllid')) $this->idordered = true;
		else $this->idordered = false;
		$this->feedfilename = 'cache/data/feed'.md5($this->uurl).'.xml';
		$xml_parser = xml_parser_create("UTF-8");
		xml_set_object ( $xml_parser, $this );
		xml_set_element_handler($xml_parser, "startElement", "endElement");
		xml_set_character_data_handler ( $xml_parser, 'characterData' );

		if(file_exists($this->feedfilename))
		{
            // Give up trying to parse the cached file after a day.
            if (time() - filemtime($this->feedfilename) > 24 * 60 * 60 )
			{
				unlink($this->feedfilename);
				@unlink($this->feedfilename.'.stat');
				@unlink($this->feedfilename.'.tstat');
			}
		}
		if(!file_exists($this->feedfilename))
		{
			include_once('common/includes/class.http.php');

			$http = new http_request($fetchurl);
			$http->set_useragent("EDK Feedfetcher ".$feedversion);
			$http->set_timeout(120);
			$http->set_cookie('PHPSESSID', 'a2bb4a7485eaba91b9d8db6aafd8ec5d');
			$data = $http->get_content();
	//		$data = trim(preg_replace('<<!--.*?-->>', '', $data)); // remove <!-- Cached --> message, else it will break gzinflate
			$data = preg_replace('<<!--.*?-->>', '', $data); // remove <!-- Cached --> message, else it will break gzinflate
			if (!@gzinflate($data))
			{
				$cprs = "raw HTML stream";
			} else
			{
				$data = gzinflate($data);
				$cprs = "GZip compressed stream";
			}
			file_put_contents($this->feedfilename, $data);
		}
		else
		{
			$data = file_get_contents($this->feedfilename);
			if(file_exists($this->feedfilename.'.stat'))
			{
				$this->tracklast_ = intval(file_get_contents($this->feedfilename.'.stat'));
				$this->tracktime_ = 0;
			}
			elseif(file_exists($this->feedfilename.'.tstat'))
			{
				$this->tracklast_ = 0;
				$this->tracktime_ = intval(file_get_contents($this->feedfilename.'.tstat'));
			}
			else
			{
				$this->tracklast_ = 0;
				$this->tracktime_ = 0;
			}
		}
		if (!xml_parse($xml_parser, $data, true))
		{
			unlink($this->feedfilename);
			@unlink($this->feedfilename.'.stat');
			@unlink($this->feedfilename.'.tstat');
			return "<i>Error getting XML data from ".$fetchurl."</i><br><br>";
		}

		xml_parser_free($xml_parser);
		unlink($this->feedfilename);
		@unlink($this->feedfilename.'.stat');
		@unlink($this->feedfilename.'.tstat');
		
		if (config::get('fetch_verbose') )
		{
			if ($this->x)
				$this->html .= "<div class=block-header2>".$this->x." kills added from feed: ".$url."<br>".$str." <i><br>(".$cprs.")</i><br><br></div>";
			else
				$this->html .= "<div class=block-header2>No kills added from feed: ".$url."<br>".$str." <i><br>(".$cprs.")</i><br><br></div>";
		}
		else
		{
			if ($this->x)
				$this->html .= "<div class=block-header2>".$this->x." kills added from feed: ".$url." <i>(".$cprs.")</i><br><br></div>";
			else
				$this->html .= "<div class=block-header2>No kills added from feed: ".$url." <i>(".$cprs.")</i><br><br></div>";
		}

		return $this->html;
	}
	//! XML start of element parser.
	function startElement($parser, $name, $attrs)
	{
		//	if ($this->insideitem)
		$this->tag = $name;
		//else
		if ($name == "ITEM")
		{
			$this->insideitem = true;
			$this->description = '';
			$this->title = "";
			$this->link = "";
		}
	}

	//! XML end of element parser.
	function endElement($parser, $name)
	{
		//global $this->html;

		if ($name == "ITEM")
		{
			if ( isset( $this->description ))
			{
				$this->description = trim(str_replace("\r", '', $this->description));
				$year = substr($this->description, 0, 4);
				$month = substr($this->description, 5, 2);
				$day = substr($this->description, 8, 2);
				$killstamp = mktime(0, 0, 0, $month, $day, $year);
				if ( $this->idordered && $this->tracklast_ > intval($this->title))
				{
					$this->html .= "Killmail ".intval($this->title)." already processed <br>";
				}
				elseif (!$this->idordered && $this->tracktime_ > $killstamp)
				{
					$this->html .= "Killmail ".intval($this->title)." already processed. <br>";
				}
				else
				{
					//Check age of mail
					if(config::get('filter_apply'))
					{
						$filterdate = config::get('filter_date');
						if ($killstamp < $filterdate) $killid = -4;
					}
					if(config::get('filter_apply') && $killid == -4);
					// If the kill has an external id then check if it is already
					// on this board.
					elseif($this->apiID = intval($this->apiID))
					{
						$qry = new DBQuery();
						$qry->execute("SELECT 1 FROM kb3_kills WHERE kll_external_id = ".$this->apiID);
						if(!$qry->recordCount())
						{
							$parser = new Parser( $this->description );
							$killid = $parser->parse( true );
						}
						else $killid = -3;
					}
					elseif(!$this->apikills)
					{
						$parser = new Parser( $this->description );
						$killid = $parser->parse( true );
					}
					if ( $killid <= 0 )
					{
						if ( $killid == 0 && config::get('fetch_verbose') )
							$this->html .= "Killmail ".intval($this->title)." is malformed. ".$this->uurl." Kill ID = ".$this->title." <br>";
						if ( $killid == -1 && config::get('fetch_verbose') )
							$this->html .= "Killmail ".intval($this->title)." already posted <a href=\"?a=kill_detail&amp;kll_id=".$parser->dupeid_."\">here</a>.<br>";
						if ( $killid == -2 && config::get('fetch_verbose') )
							$this->html .= "Killmail ".intval($this->title)." is not related to ".KB_TITLE.".<br>";
						if ( $killid == -3 && config::get('fetch_verbose') )
							$this->html .= "Killmail ".intval($this->title)." already posted <a href=\"?a=kill_detail&amp;kll_external_id=".$this->apiID."\">here</a>.<br>";
						if ( $killid == -4 && config::get('fetch_verbose') )
							$this->html .= "Killmail ".intval($this->title)." too old to post with current settings.<br>";
					}
					else
					{
						$qry = new DBQuery();
						if(strpos($this->uurl, '?')) $logurl = substr($this->uurl,0,strpos($this->uurl, '?')).'?a=kill_detail&kll_id='.intval($this->title);
						else $logurl = uurl.'?a=kill_detail&kll_id='.intval($this->title);
						$qry->execute( "insert into kb3_log (log_kll_id, log_site, log_ip_address, log_timestamp) values( ".
							$killid.", '".KB_SITE."','".$logurl."',now() )" );
						$this->html .= "Killmail ".intval($this->title)." successfully posted <a href=\"?a=kill_detail&kll_id=".$killid."\">here</a>.<br>";

						if (config::get('fetch_comment'))
						{
							$comments = new Comments($killid);
							$comments->addComment("Feed Syndication", config::get('fetch_comment')." mail fetched from: ".$this->uurl.")");
						}
						$this->x++;
					}
					if( $this->idordered && intval($this->title) > 0)
					{
						$this->tracklast_ = intval($this->title);
						file_put_contents($this->feedfilename.'.stat', strval(intval($this->title)));
					}
					elseif( !$this->idordered && $killstamp > 0)
					{
						$this->tracktime_ = $killstamp;
						file_put_contents($this->feedfilename.'.tstat', strval($killstamp));
					}
				}
			}
			if($this->title && intval($this->title) > $this->lastkllid_) $this->lastkllid_ = intval($this->title);
			$this->title = "";
			$this->description = "";
			$this->link = "";
			$this->insideitem = false;
			$this->apiID = false;
		}
	}
	//! XML character data parser.
	function characterData($parser, $data)
	{
		if ($this->insideitem)
		{
			switch ($this->tag)
			{
				case "TITLE":
					$this->title .= $data;
					break;
				case "DESCRIPTION":
					$this->description .= $data;
					break;
				case "LINK":
					$this->link .= $data;
					break;
				case "APIID":
					$this->apiID .= $data;
			}
		}
		elseif($this->tag=="FINALKILL")
		{
			if(!($this->finalkllid_ > intval($data))) $this->finalkllid_ = intval($data);
		}
		elseif($this->tag=="COMBINED")
		{
			$this->combined_ = true;
		}
	}

}
?>