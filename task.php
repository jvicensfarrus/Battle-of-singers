<?php
/*
* Developed by Jordi Vicens Farrús.
* http://www.jordivicensfarrus.com/
*
* This class fetch all the information about one artist like top tracks, how many plays it has, history of the artist, etc and keep the information
*/
class LastFM {

	//My API key for get the special information that I wanted to get on last.fm
	const API_KEY = "032e1472e060ac8cc4bd8cda2100ccdf";

	public static function getArtist($name) {
		//Get the top tracks from the artist that it have through API calls
		$result = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getTopTracks&artist=" . urlencode($name) . "&autocorrect=1&format=json&limit=10&api_key=" . self::API_KEY);
		if ($result !== false) {
			$artist = new Artist($name);

			return $artist->setTopTracks(json_decode($result, true));
		}
		return false;

	}
	//Get Information about each artist
	public static function getArtistInfo($name) {
		$result = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getInfo&artist=" . urlencode($name) . "&autocorrect=1&format=json&limit=10&api_key=" . self::API_KEY);
		if ($result !== false) {

			return json_decode($result, true);
		}
		return false;
	}

	//Get information about the tags of the artist
	public static function getTopTags($name) {
		$result = file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.gettoptags&artist=" . urlencode($name) . "&autocorrect=1&format=json&limit=10&api_key=" . self::API_KEY);
		if ($result !== false) {
			return json_decode($result, true);
		}
		return false;
	}

}

/**
 * Class for classify the information about each artist.
 */
class Artist {

	//Declarate the private variables, where they just can access by his own class
	private $name;
	private $info;
	private $top_tags;
	private $top_tracks;
	private $error = false;


	/**
	 *Function constructor, where variables are initialized
	 */
	public function __construct($name) {
		$info = LastFM::getArtistInfo($name);
		if (isset($info['error']) && $info['error']) {
			$this->error = $info['error'];
		} else {
			$this->name = $info['artist']['name'];
			$this->info = $info['artist'];
		}

		//I call the method from another class and when I set the information inside this class.
		$top_tags = LastFM::getTopTags($name);
		$this->setTopTags($top_tags);


	}

	/**
	 *Just the "magic method",  getters and setters.
	 */
	public function __toString() {
		return $this->name;
	}

	public function getName() {
		return $this->name;
	}

	public function getError() {
		return $this->error;
	}

	 public function setTopTracks($top_tracks) {
 		$this->top_tracks = $top_tracks;
 		return $this;
 	}
	public function setTopTags($top_tags) {
		$this->top_tags = $top_tags;
		return $this;
	}


	public function getTopTags() {
		return $this->top_tags["toptags"]["tag"];
	}

	public function getTopTracks() {
		return $this->top_tracks['toptracks']['track'];
	}


	public function getImage() {
		return $this->info['image'][count($this->info['image']) - 2]['#text'];
	}

	public function getDescription() {
		return $this->info['bio']['summary'];
	}

	public function getListeners() {
		return $this->info['stats']['listeners'];
	}
	public function getPlays() {
		return $this->info['stats']['playcount'];
	}

}

/**
 * Class where it do the battle between the two artists and fetch the winner of them
 */
class Battle {

	//Declarate the private variables, where they just can access by his own class
	private $artists;
	private $breakdown;
	private $breakdown2;
	private $manyTags = array();
	private $winner = false;
	private $scores = array();

	/**
	 * A constructor where it will start the "battle of artists"
	 */
	public function __construct(Array $artists) {
		$this->artists = $artists;
		$this->runBattle();
	}

	public function runBattle() {

	/*
	*	Comparison between, which artist have more tags over 70 times(this comparison could give us a kind of popularity of the artist)
	*/
		$breakdown3 = $this->getBetterTags();
		$totalMostTags = 0;

		$this->manyTags[0] = 0;
		$this->manyTags[1] = 0;
		$flag=0;
		foreach ($breakdown3 as $manyTags) {
			foreach ($manyTags as $tag) {
				if($tag["count"] >= 70){
					$totalMostTags++;
				}
			}

			$this->manyTags[$flag] = $totalMostTags;
			$totalMostTags = 0;
			$flag++;
		}


		$this->breakdown = array();
		//It will get the most listeners and most plays accessing in these methods
		$most_listeners = $this->getMostListeners();
		$most_plays = $this->getMostPlays();

		$this->breakdown[] = array(
 			"category" => "Most Listeners",
 			"scores" => $most_listeners,
			"plays" => $most_plays,
 			"winner" => $most_listeners[0] > $most_listeners[1] ? 0 : ($most_listeners[0] < $most_listeners[1] ? 1 : false)
		);

		//Comparison where it is guided for the plays, who will get more total plays, will get point on the score.
		$this->breakdown2 = array();

		$this->breakdown2[] = array(
 			"category" => "Most Listeners",
 			"scores" => $most_listeners,
			"plays" => $most_plays,
 			"winner" => $most_plays[0] > $most_plays[1] ? 0 : ($most_plays[0] < $most_plays[1] ? 1 : false)
		);



		//Initializes the score at 0 for the both artists an it will start counting who is the winner
		$this->scores[0] = 0;
		$this->scores[1] = 0;
		foreach ($this->breakdown as $round) {
			if ($round['winner'] !== false) {
				$this->scores[$round['winner']]++;
			}
		}
		foreach ($this->breakdown2 as $round) {
			if ($round['winner'] !== false) {
				$this->scores[$round['winner']]++;
			}
		}
		//Comparison where who have more tags better than 70.
		if($this->manyTags[0] > $this->manyTags[1]){
			$this->scores[0]++;
		}
		elseif ($this->manyTags[0] < $this->manyTags[1]) {
			$this->scores[1]++;
		}

		//If scores[0] is bigget than scores[1] then the winner is 0, if not, the winner is 1
		$this->winner = $this->scores[0] > $this->scores[1] ? 0 : ($this->scores[0] < $this->scores[1] ? 1 : false);
	}

	public function getWinner() {

		return $this->winner;
	}

	public function getScores() {
		return $this->scores;
	}

	public function getBreakdown() {
		return $this->breakdown;
	}

	private function getMostListeners() {
		return array($this->artists[0]->getListeners(), $this->artists[1]->getListeners());
	}
	private function getMostPlays() {
		return array($this->artists[0]->getPlays(), $this->artists[1]->getPlays());
	}
	private function getBetterTags() {
		return array($this->artists[0]->getTopTags(), $this->artists[1]->getTopTags());
	}

}
