<?php

if(isset($_REQUEST['channel']))
	$url = "http://www.siriusxm.com/metadata/pdt/en-us/json/channels/" . $_REQUEST['channel'] . "/timestamp/";
else
	$url = "http://www.siriusxm.com/metadata/pdt/en-us/json/channels/thebeat/timestamp/";

date_default_timezone_set("GMT");
$url .= date("m-d-H:i:s", strtotime("-3 seconds"));

$data = json_decode(file_get_contents($url),1);

$channel = $data['channelMetadataResponse']['metaData']['channelName'];
$song = $data['channelMetadataResponse']['metaData']['currentEvent']['song']['name'];
$artist = $data['channelMetadataResponse']['metaData']['currentEvent']['artists']['name'];
$artist = str_replace("+", " ", $artist);
?>
Now playing <?=$song ?> by <?=$artist ?> on <?=$channel ?>
<?php
$spotifyURL = "http://ws.spotify.com/search/1/track.json?q=" . urlencode($song) . "+artist:" . urlencode($artist);
$spotify = json_decode(file_get_contents($spotifyURL), 1);
	if($spotify['info']['num_results'] == 0)
	{
		$artist = substr($artist, 0, strpos($artist, "+"));
		$spotifyURL = "http://ws.spotify.com/search/1/track.json?q=" . urlencode($song) . "+artist:" . urlencode($artist);
		$spotify = json_decode(file_get_contents($spotifyURL), 1);
	}
?>
<br><br>
<a href="<?=$spotifyURL ?> " target="_blank"><?=$spotifyURL ?> </a>
<br><br>
<?php


foreach($spotify['tracks'] as $track)
{
	if (strpos($track['album']['availability']['territories'],'US') !== false) {
		$foundTrack = $track;
		break;
	}
}

if($foundTrack != null)
{
	$httpURL = str_replace("spotify:", "http://open.spotify.com/", $foundTrack['href']);
	$httpURL = str_replace("track:", "track/", $httpURL);
?>

We found a Spotify song <a href="<?= $foundTrack['href'] ?>"><?= $foundTrack['name'] ?></a> (<a href="<?= $httpURL ?>">HTTP</a>)

<?php
}