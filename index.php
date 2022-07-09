<?php
/**
 * API Url : https://movie-s.nhk.or.jp/ws/ws_asset/api/67f5b750-b419-11e9-8a16-0e45e8988f42/mode/json/apiv/5
 * Sample API Query: https://movie-s.nhk.or.jp/ws/ws_asset/api/67f5b750-b419-11e9-8a16-0e45e8988f42/mode/json/apiv/5&title=%25kamakura%25&sortdir=asc&start=0&end=4
 * @title: kamakura
 * @sortdir: asc
 * @start: 0
 * @end: 4
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div class="container-fluid">
        <div class="main" id="main">
            <div class="main-container">
                <h2 class="text-left">NHK Video-on-Demand Browser</h2>
                <p>This browser helps display the results from the NHK VOD API, which usually returns as <b>json</b>, and might overwhelm ordinary users from using the direct api,</p>
                <p>so this browser is designed to be used as a <b>search engine</b> for the NHK VOD API.</p>
                <p>Please remember that this is currently on beta, as the title table might be blank due to poor filter</p>
                <!-- Search box, and query api button -->
                <div class="search-box">
                    <form action="index.php" method="GET">
                        <!-- Searchbox, Get query of url if exists -->
                        <input type="text" name="query" value="<?php echo $_GET['query'] ?>" placeholder="Search for a title">
                        <!-- Checkbox for removing filter flag -->
                        <input type="checkbox" name="filter" value="1" <?php if ($_GET['filter'] == 1) { echo "checked"; } ?>> <span>Filter Title (Beta)</span>
                        <!-- Extended Results -->
                        <input type="checkbox" name="extended" value="1" <?php if ($_GET['extended'] == 1) { echo "checked"; } ?>> <span>Extended Results</span>
                        <input type="submit" value="Search">
                    </form>
                </div>
                <!-- End of search box -->
                <div class="resultdata">
                <?php
                    // Get the query from the url, and check for extended results
                    $query = $_GET['query'];
                    $extended = $_GET['extended'];
                    // Search using api, If extended is checked, show all results, otherwise show 4 results
                    if ($extended == 1) {
                        $api_url = "https://movie-s.nhk.or.jp/ws/ws_asset/api/67f5b750-b419-11e9-8a16-0e45e8988f42/mode/json/apiv/5&title=%25$query%25&sortdir=asc&start=0&end=25";
                    } else {
                        $api_url = "https://movie-s.nhk.or.jp/ws/ws_asset/api/67f5b750-b419-11e9-8a16-0e45e8988f42/mode/json/apiv/5&title=%25$query%25&sortdir=asc&start=0&end=5";
                    }
                    // Get the json from the api
                    $json = file_get_contents($api_url);
                    // Decode the json
                    $json_decoded = json_decode($json, true);
                    // Get the results from the json, display as table
                    $results = $json_decoded['response']['WsAssetResponse']['asset'];
                    // Display the results as a table, and remove '[nhkworld]:vod' from the title
                    echo '<table class="table table-striped" id="results">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Title</th>';
                    echo '<th>Description</th>';
                    echo '<th>Thumbnail</th>';
                    echo '<th>Link</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    foreach ($results as $result) {
                        $filter = $_GET['filter'];
                        if (!isset($filter)) { // If filter is not set, default to true
                            $filter = "0";
                        }
                        if ($filter == "1") { // If filter is true, remove '[nhkworld]:vod' from the title
                            $r_title = str_replace('[nhkworld]vod:', '', $result['title']);
                            $clean_title = substr($r_title, 0, strpos($r_title, ":en"));
                        } elseif ($filter == "0") { // If filter is false, keep the title as is
                            $clean_title = $result['title'];
                        }
                        $filtered_title = str_replace('_', ': ', $clean_title); // Replace _ with :
                        echo '<tr>';
                        echo '<td>' . $filtered_title . '</td>';
                        // Description, if there description has no characters, display "No description"
                        if (strlen($result['description']) == 0) {
                            echo '<td>No description</td>';
                        } else {
                            echo '<td>' . $result['description'] . '</td>';
                        }
                        echo '<td><img src="' . $result['thumbnailUrl'] . '" alt="' . $result['thumbnailUrl'] . $result['assetid'] . '" height="100"></td>';
                        echo '<td><button onclick="copytoclipboard(' . $result['m3u8AndroidURL'] . ')">m3u8 Link</button></td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                ?>
                <!-- Credits -->
                <p>Created by NWJ9PB, Source Code is available GitHub</p>
                <!-- Link to Repo -->
                <p><a href="https://github.com/NWJ9PB/nhkvod-browser">Source Code</a></p>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>