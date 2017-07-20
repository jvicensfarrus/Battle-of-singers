<?php
/*
* Developed and updated by Jordi Vicens Farrús
* http://www.jordivicensfarrus.com/
* Little explanation about this WebApp: How to play with classes and OOP in PHP, using an existing API (LastFM).
*/
	$examples = array("Ariana Grande", "Aesop Rock", "The B-52's", "Benga", "Chumbawumba", "Chris Brown", "De La Soul", "Deltron 3030", "Eels", "Eminem", "Fort Minor", "Frank Sinatra", "Gorillaz", "Grimes", "House Of Pain", "Hans Zimmer", "Icona Pop", "Iggy Azalea", "JME", "Jurassic 5", "Katy Perry", "Kids In Glass Houses", "Linkin Park", "Lethal Bizzle", "Magnetic Man", "Meg Myers", "Nero", "Nirvana", "Ozomatli", "Omi", "Pixie Lott", "Panic! At The Disco", "Queens of the Stone Age", "Quasimoto", "Ratatat", "Red Hot Chili Peppers", "Sex Pistols", "Scissor Sisters", "Tori Amos", "t.A.T.u.", "Uffie", "U2", "Vapors", "Vanessa Carlton", "The White Stripes", "Wu Tang Clan", "Xscape", "Xzibit", "Ylvis", "Young Money", "Zebrahead", "The Zutons");

	$artists = array("", "");
	$battle = false;

	if (isset($_GET['artist1'], $_GET['artist2'])) {
		require_once("task.php");
		$artists = array(
			LastFM::getArtist( $_GET['artist1'] ),
			LastFM::getArtist( $_GET['artist2'] )
		);


		if ($artists[0]->getError() === false && $artists[1]->getError() === false) {
			$battle = new Battle($artists);
		} else {
			$artists = array("", "");
		}

	}

?>

<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Battle of singers.</title>
		<link rel="stylesheet" type="text/css" href="css/style.css" />

		<!-- Google Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Hammersmith+One' rel='stylesheet' type='text/css'>

		<!-- jQuery -->
		<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>

		<!-- Twitter Bootstrap -->
		<link rel="stylesheet" href="bootstrap-3.1.1-dist/css/bootstrap.min.css">
		<script src="bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>

		<!-- CDN of Font awesome -->
		<link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'>


	</head>
	<body>

		<div id="header" class="navbar-fixed-top">
			<div class="content">
				<h1><?php echo $battle ? implode(" <span class='vs'>VS.</span> ", $artists) : "Music Battler";?></h1>
			</div>
		</div>

		<div id="main">
			<div class="content">

				<?php if ($battle){ ?>

					<h2>
						<?php if ($battle->getWinner() !== false){ ?>
							The Winner Is <?php echo $artists[$battle->getWinner()];?>!
						<?php } else{ ?>
							Draw!
						<?php } ?>
					</h2>

					<div class="row">
						<div class="col-md-5 artist-score" style="background-image: url('<?php echo $artists[0]->getImage();?>')"><?php $battle->getScores()[0];?></div>
						<div class="col-md-2"></div>
						<div class="col-md-5 artist-score" style="background-image: url('<?php echo $artists[1]->getImage();?>')"><?php $battle->getScores()[1];?></div>
					</div>

					<div class="row">
						<div class="col-md-5 artist-description" id="linkContainer"><?php echo nl2br($artists[0]->getDescription());?></div>
						<div class="col-md-2"></div>
						<div class="col-md-5 artist-description" id="linkContainer"><?php echo nl2br($artists[1]->getDescription());?></div>
					</div>

					<h2>Breakdown</h2>

					<?php foreach ($battle->getBreakdown() as $round){ ?>

						<hr/>

						<h3><?php $round['category']?></h3>
						<div class="row">
							<div class="col-md-6 center">
								<!-- Show the listeners and the plays for each artist -->
								<h3><?php echo $artists[0]?> <hr><br> <?php echo number_format($round['scores'][0]);?> <i class="fa fa-headphones" aria-hidden="true"></i> <br> <?php echo number_format($round['plays'][0]); ?> <i class="fa fa-play" aria-hidden="true"></i></h3>
							</div>
							<div class="col-md-6 center">
								<h3><?php echo $artists[1]?> <hr><br> <?php echo number_format($round['scores'][1]);?> <i class="fa fa-headphones" aria-hidden="true"></i> <br> <?php echo number_format($round['plays'][1]); ?> <i class="fa fa-play" aria-hidden="true"></i> </h3>
							</div>
						</div>

					<?php }?>

					<h2>Top Tracks</h2>


					<?php foreach ($artists as $artist){ ?>
						<div class="col-md-6 center">
							<?php foreach ($artist->getTopTracks() as $track){ ?>

								<div class="track block" data-mbid="<?php echo isset($track['mbid']) ? $track['mbid'] : ''?>">
									<img src="<?php echo $track['image'][count($track['image']) - 1]['#text']?>" class="picture" />
									<h3><?php echo $track['@attr']['rank']?>. <?php echo $track['name']?></h3>
									<p>
										Plays: <?php echo number_format($track['playcount']);?><br/>
										Listeners: <?php echo number_format($track['listeners']);?>
									</p>

									<p class="task2" style="display:none;">
										<a class="link" href="<?php echo $track['url']; ?>"  >More about this song</a>
									</p>

									<div class="clear"></div>
								</div>

							<?php } ?>
						</div>
					<?php } ?>
					&nbsp;
					<h2 class="clear margin_top">Another Battle?</h3>

				<?php } ?>

				<form method="GET" class="center row">
					<div class="col-md-5"><input name="artist1" type="text" class="input" placeholder="Example: <?php echo $examples[rand(0, count($examples) - 1)]?>" /></div>
					<div class="col-md-2"><span class="vs">VS.</span></div>
					<div class="col-md-5"><input name="artist2" type="text" class="input" placeholder="Example: <?php echo $examples[rand(0, count($examples) - 1)]?>" /></div>
					<div class="col-md-12"><input type="submit" class="btn btn-lg btn-primary" value="Fight" /></div>
				</form>
				<div class="center lucky"><a href=".?artist1=<?php echo $examples[rand(0, count($examples) - 1)]?>&amp;artist2=<?php echo $examples[rand(0, count($examples) - 1)]?>">I'm feeling lucky!</a></div>
			</div>
		</div>

		<div id="footer">
			<div class="content">
				Developed by <a href="http://www.jordivicensfarrus.com/" target="_blank">Jordi Vicens Farrús</a>.
			</div>
		</div>

		<script type="text/javascript" src="js/main.js"></script>
		<!-- Adding the Font Awesome CDN  -->
		<script src="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"></script>



	</body>
</html>
