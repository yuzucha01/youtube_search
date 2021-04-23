<?php

require_once __DIR__ . '/vendor/autoload.php';

//打ち込んだ検索ワードと検索数を取得
if (isset($_GET['q'])) {

  //youtubeのAPIkey
  $DEVELOPER_KEY = 'XXXXXXXXXXXXXXXXXXX';

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);

  //すべてのAPIリクエストに使用されるオブジェクトを定義する
  $youtube = new Google_Service_YouTube($client);

  //htmlを空にする
  $htmlBody = '';
  try {

    //search.listメソッドを呼び出し、指定した単語に一致する結果を取得する
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $_GET['q'],
      'maxResults' => 20,
      //再生回数の順番
      'order' => 'viewCount'
    ));

    $videos = '';
    $channels = '';

    //結果を適切なリストに追加し表示する
    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<ul> <li>%s<a href="https://www.youtube.com/watch?v=%s"></li> </ul>',
              $searchResult['snippet']['title'], $searchResult['id']['videoId'], $searchResult['statistics']['viewCount']);
          break;
      }
    }

  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RECIPE Video</title>
    <link rel="stylesheet" href="./css/video.css">
</head>
<body>

  <form method="GET">
  <div>
    Search Term: <input type="search" id="q" name="q" placeholder="Enter Search Term">
  </div>
  <input type="submit" value="Search">
  </form>

  <h3>ランキング</h3>
    <ul><?php echo $videos ?></ul>
</body>
</html>
