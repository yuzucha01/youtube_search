<?php
session_start();

require_once (dirname(__FILE__) . '/vendor/autoload.php');

class TestYouTube
{
    const API_KEY = "XXXXXXXXXXX";
    public $youtube;

    public function __construct()
    {
        $this->youtube = new Google_Service_YouTube($this->getClient());
    }

    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName("youtubeTestApp");
        $client->setDeveloperKey(self::API_KEY);
        return $client;
    }

    public function getTop10()
    {
        $part = [
            'snippet',
            'statistics'
        ];
        $params = [
            'chart' => 'mostPopular',
            'maxResults' => 10,
            'regionCode' => 'JP',
        ];
        $search_results = $this->youtube->videos->listVideos($part, $params);
        $videos = [];
        foreach ($search_results['items'] as $search_result) {
            $videos[] = $search_result;
        }
        return $videos;
    }

}

$test_youtube = new TestYouTube();
$videos = $test_youtube->getTop10();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>動画検索</title>
    <link rel="stylesheet" href="./css/video.css">
</head>
<body>

<form method="GET">
  <div>
    Search Term: <input type="search" id="q" name="q" placeholder="Enter Search Term">
  </div>
  <div>
    Max Results: <input type="number" id="maxResults" name="maxResults" min="1" max="50" step="1" value="25">
  </div>
  <input type="submit" value="Search">
</form>

<div id="commons">
    <div class="contents">
        <table>
            <tr>
                <th>RANK</th>
                <th>TITLE</th>
                <th>再生回数</th>
            </tr>
            <?php
            $i = 1;
            foreach ($videos as $video):
            $view_count = number_format($video['statistics']['viewCount']);
            $rank = <<<TEXT
            {$i}位
            TEXT;
            $title = <<<TEXT
            {$video['snippet']['title']}
            TEXT;
            $num = <<<TEXT
            {$view_count} 回
            TEXT;
            $link = <<<TEXT
            https://www.youtube.com/watch?v={$video['id']}
            TEXT;
            ?>
                <tr>
                    <td><?php echo $rank; ?></td>
                    <td><a href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></td>
                    <td><?php echo $num; ?></td>
                </tr>
            <?php
            $i++;
            endforeach;
            ?>
            </table>
    </div>
</div>

</body>
</html>