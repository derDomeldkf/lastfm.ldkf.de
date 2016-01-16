<?php
$user_in = "";
$page = "";
$totalPages = "";
$perPage = "";
$totalTracks = "";
$image = "";
$totaltracks = "";
$starttime = "";
include "include/config.php";
include "include/functions.php";

$token = filter_input(INPUT_GET, "token");
if ($token === NULL) {
    $method_in = filter_input(INPUT_GET, "method");
    if ($method_in === NULL) {
        $method_in = filter_input(INPUT_POST, "method");
    }
} else {
    $method_in = 3;
}
$usr=  filter_input(INPUT_POST, "username");
$usr2=  filter_input(INPUT_GET, "username");
if($usr!=NULL && $usr) {
    $user_in=$usr;
    if ($method_in == 3) {
        header('Location: http://www.last.fm/api/auth?api_key='.$api_key.'&cb=https://lastfm.ldkf.de/lastfm.php');
    }
}
elseif ($usr2!=NULL && $usr2) {
    $user_in=$usr2;
    if ($method_in == 3) {
        header('Location: http://www.last.fm/api/auth?api_key='.$api_key.'&cb=https://lastfm.ldkf.de/lastfm.php');
    }
//}
//if (isset($_POST['username'])) {
//    $user_in = $_POST['username'];
//    if ($method_in == 3) {
//        header('Location: http://www.last.fm/api/auth?api_key='.$api_key.'&cb=https://lastfm.ldkf.de/lastfm.php');
//    }
} else {
    if (isset($_GET['token']) and $_GET['token'] != "") {
        $method_in = 3;
        $sig = md5("api_key" . $api_key . "methodauth.getSessiontoken" . $token . $secret);
        $methode = "'method=auth.getSession&token='" . $token . "'&api_sig='" . $sig;
        $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
        $decode = json_decode($out);
        $info_array = get_object_vars($decode);
        if (isset($info_array["error"])) {
            $error = 1; //fehler bei übermittlung
        } else {
            $info = get_object_vars($info_array['session']);
            $user_in = $info['name'];
            $getname = mysql_query("SELECT `id` FROM `ldkf_lastfm` WHERE `username` LIKE '$user_in'");
            $namecheck = mysql_fetch_row($getname);
            $user = $namecheck[0];
            if (!isset($user) or $user == "") {
                $eintrag = "INSERT INTO ldkf_lastfm (username) VALUES ('$user_in')";
                $eintragen = mysql_query($eintrag);
                $error = 2; //ERFOLG
            } else {
                $error = 3; //Bereits Mitglied				
            }
        }
    } else {
        if (isset($_GET['method'])) {
            $method_in = $_GET['method'];
        } elseif ($method_in == 1 or $method_in == 4 or $method_in == 8) {
            if ($method_in == 1) {
                header('Location: https://telegram.me/ldkf_bot');
            }
        } else {
            header('Location: ./');
        }
    }
}

if (isset($_POST['pagein'])) {
    $page_in = $_POST['pagein'];
} else {
    $page_in = 1;
}
if (isset($_POST['limitin'])) {
    $limit_in = $_POST['limitin'];
} elseif ($method_in == 2 or $method_in == 5) {
    $limit_in = 15;
} else {
    $limit_in = 20;
}
if (isset($user_in) and $user_in != "") {
    $methode = "method=user.getInfo&user=" . $user_in;
    $out_user = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
    if ($out_user != '{"error":6,"message":"User not found","links":[]}') {
        $decode_Info_User = json_decode($out_user);
        $user_info_forimage_array = get_object_vars($decode_Info_User)['user'];
        $user_name_info = get_object_vars($decode_Info_User)['user']->name;
        $totalTracks = get_object_vars($decode_Info_User)['user']->playcount;
        $starttime = get_object_vars($decode_Info_User)['user']->registered->unixtime;
        $user_info_forimage = get_object_vars($user_info_forimage_array)['image'];
        $userimage = get_object_vars($user_info_forimage[1]);
        $account_image = $userimage['#text'];
        if (!isset($account_image) or $account_image == "") {
            $image = "pic/empty.png";
        } else {
            $image_db = str_replace(".png", "", $account_image);
            $image_db = str_replace("http://img2-ak.lst.fm/i/u/64s/", "", $image_db);
            $getimage = mysql_query("SELECT `id` FROM `last_fm_user_pics` WHERE name LIKE '$image_db'");
            $getimages = mysql_fetch_row($getimage);
            $getimage_row = $getimages[0];
            if (!isset($getimage_row) or $getimage_row == "") {
                $pfad = "user_pics/" . $image_db . ".png";
                copy($account_image, $pfad);
                $eintrag = "INSERT INTO last_fm_user_pics (name) VALUES ('$image_db')";
                $eintragen = mysql_query($eintrag);
            }
            $image = "user_pics/" . $image_db . ".png";
        }
        if ($method_in == 2) {
            if (!isset($_COOKIE['login']) and isset($_POST['start']) and $_POST['start'] == 1) {
                setcookie("login", $user_in, time() + (3600 * 24 * 365));
            }
            $methode = "method=user.getRecentTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
            $decode = json_decode($out);
            $user_info_array = get_object_vars($decode->recenttracks);
            $user_decode = $user_info_array['@attr'];
            $username = $user_decode->user;
            $page = $user_decode->page;
            $perPage = $user_decode->perPage;
            $totalPages = $user_decode->totalPages;
            $tracks = $decode->recenttracks->track;
            $totaltracks = $totalTracks;
        }
        if ($method_in == 5) {
            $methode = "method=user.getLovedTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->lovedtracks);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['track'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
        if ($method_in == 6) {
            $methode = "method=user.getTopArtists&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->topartists);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['artist'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
        if ($method_in == 7) {
            $methode = "method=user.getTopTracks&user=" . $user_in . "&page=" . $page_in . "&limit=" . $limit_in . "&extended=1&nowplaying=true";
            $out = file_get_contents("https://ws.audioscrobbler.com/2.0/?format=json&api_key=" . $api_key . "&" . $methode);
            if (isset($out)) {
                $decode = json_decode($out);
                $user_info_array_love = get_object_vars($decode->toptracks);
                $user = $user_info_array_love['@attr'];
                $tracks = $user_info_array_love['track'];
                $username = $user->user;
                $page = $user->page;
                $perPage = $user->perPage;
                $totalPages = $user->totalPages;
                $totaltracks = $user->total;
            }
        }
    } else {
        $method_in = 0;
    }
}
?>
<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="favicon.png">
        <link href="https://msn.ldkf.de/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="css/main.css" rel="stylesheet">
        <title><?php
            if ($method_in == 4 or $method_in == 8 or $method_in == 9) {
                echo "LDKF-Gruppe";
            } elseif ($method_in == 2) {
                echo "Musikprofil";
            } elseif (isset($user_in)) {
                echo $user_in;
            }
            ?></title>
    </head>
    <body style="font-family: ubuntu-m;">
        <div id="content" class="main-content" role="main" style="">
            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container-fluid" id="navigation" style="display:block;">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="http://ldkf.de" target="_blank">LDKF.de</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="./">Startseite<span class="sr-only">(current)</span></a></li>
                            <?php
                            echo nav($method_in, $user_in, $image, $totalTracks, $starttime, $totaltracks);
                            ?>					
                    </div>
                </div>
            </nav>
            <div class="main" style="margin-left:0; padding-bottom:70px;">
                <section class="tracklist-section">
                    <?php
                    switch ($method_in) {
                        case 0:
                            echo '<div style="margin:40px;"><h3>Benutzer "' . $user_in . '" existiert nicht.</h3></div>';
                        case 1:
                            break;
                        case 2:
                            include "user_tracks.php";
                            break;
                        case 3:
                            switch ($error) {
                                case 1:
                                    echo "Es gab einen Fehler, versuche es noch einmal.";
                                    break;
                                case 2:
                                    echo "Du wurdest erfolgreich zu dieser Gruppe hinzugef&uuml;gt.";
                                    break;
                                case 3:
                                    echo "Du bist bereits Mitglied in dieser Gruppe.";
                                    break;
                                default:
                                    echo "";
                                    break;
                            }
                            break;
                        case 4:
                            $db_name = "last_fm_charts";
                            $period = "In der letzten Woche gehört von";
                            echo group($db_name, $period);
                            break;
                        case 5:
                            include "user_love_track.php";
                            break;
                        case 6:
                            include "user_topartist.php";
                            break;
                        case 7:
                            include "user_toptrack.php";
                            break;
                        case 8:
                            $db_name = "last_fm_charts_all";
                            $period = "Gehört von";
                            echo group($db_name, $period);
                            break;
                        default:
                            break;
                    }
                    ?>
                </section>
            </div>
            <?php
            echo footer($method_in, $page, $totalPages, $user_in, $limit_in, $perPage);
            ?>
        </div>
        <script type="text/javascript" src="https://msn.ldkf.de/js/jquery-1.11.2.min.js"></script>
        <script type="text/javascript" src="https://msn.ldkf.de/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('body').on('hidden.bs.modal', '.modal', function () {
                    $(this).removeData('bs.modal');
                });
            });
        </script>
    </body>
</html> 