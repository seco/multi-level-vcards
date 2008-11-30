<?php

if ( isset($_POST['passcode']) ) {
  $server = $_SERVER['SERVER_NAME'];
  $port = isset($_SERVER['SERVER_PORT']) && 80 != $_SERVER['SERVER_PORT']
        ? ':'.$_SERVER['SERVER_PORT']
        : '';
  $page = $_SERVER['REQUEST_URI'];
  if ( FALSE !== ( $pos = strpos( $page, '?' ) ) ) {
    $page = substr( $page, 0, $pos );
  }
  $query = '?passcode='.md5($_POST['passcode'].date('YmdHi'));
  header("Location: http://$server$port$page$query");
  exit();
}

include_once('VCard.php');
class hCardWithLevels {

    const UNKNOWN      = 0;
    const BAD_CODE     = 1;
    const ACQUAINTANCE = 5;
    const FRIEND       = 10;
    
    public function __construct( $passcodes, $data=null ) {
        $this->passcodes = $passcodes;
        $this->data = $data;
    }
    
    public function getUserLevel( $userPasscode = null ) {
      if ( !$userPasscode ) {
        return self::UNKNOWN;
      }
      
      if ( $userPasscode ) {
        for ( $i=0; $i < 5; $i++) {
          foreach ( $this->passcodes as $passcode=>$level ) {
              if ( $userPasscode == md5($passcode.date( 'YmdHi', time() - 60 * $i )) ) {
                  return $level;
              }
          }
        }
      }
      
      return self::BAD_CODE;
    }

    public function toString( $userPasscode = null ) {
        $userLevel = $this->getUserLevel( $userPasscode );

        $vCard = new VCard('vcard.vcf');
        
        $vCard->setLevel($userLevel);
        return $vCard->toHCard();
    }
}

$hCard = new hCardWithLevels(
    array(
        'artichoke' => hCardWithLevels::ACQUAINTANCE,
        'melon'     => hCardWithLevels::FRIEND
    )
);
    
if ( isset( $_GET['vcf'] ) ) {
    if ( hCardWithLevels::BAD_CODE != $hCard->getUserLevel( isset($_GET['passcode']) ? $_GET['passcode'] : null ) ) {
        $userLevel = $hCard->getUserLevel($_GET['passcode']);
        $vCard = new VCard('vcard.vcf');
        $vCard->setLevel($userLevel);
        
        header('Content-type: text/x-vcard');
        header('Content-Disposition: attachment; filename="NeilCrosby.vcf"');
        echo $vCard->toVCard();
        exit;
    }
}

$server = $_SERVER['SERVER_NAME'];
$port = isset($_SERVER['SERVER_PORT']) && 80 != $_SERVER['SERVER_PORT']
     ? ':'.$_SERVER['SERVER_PORT']
     : '';
$page = $_SERVER['REQUEST_URI'];
$joiner = ('/' == $page) ? '?' : '&amp;';
$url = "http://$server$port$page{$joiner}vcf=1";

$success = '';
if ( isset( $_GET['passcode'] ) ) {
    if ( hCardWithLevels::BAD_CODE == $hCard->getUserLevel( isset($_GET['passcode']) ? $_GET['passcode'] : null ) ) {
        $success = "<p class='error'>
                Oh dear, the passcode you gave doesn't seem to be valid.
                Maybe it's gone past its 5 minute timeout, or maybe something
                went wrong whilst you were typing it in.  
                Why not try typing it again?
              </p>";
    } else {
        $success = "<p class='success'>
                   This URL will only remain viable for 5 minutes.  After that, 
                   it will revert back to displaying the publically available
                   information.  Still, that's plenty enough time for you
                   to click on the 
                   <a href='$url'>Download as VCF</a>
                   link.
                 </p>";
    }
}

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
  "http://www.w3.org/TR/html4/strict.dtd">
<html lang='en'>
  <head>
    <title>Neil Crosby's vCard</title>
    <link rel="stylesheet" type="text/css" href="reset-fonts-grids-min.css">
    <style type='text/css'>
        h1 {
            font-size: 2em;
            font-weight: bold;
        }

        h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin: 0.83em 0;
        }
        
        p {
            margin-bottom: 1em;
        }

        dl {
            overflow: hidden;
        }
        
        dt {
            font-weight:bold;
            clear:left;
            float:left;
            min-width: 17em;
        }
        dd {
            float:left;
            margin-left: 0;
            padding-left: 0;
            width: 30em;
        }
        
        .cta {
            text-align: center;
        }
        
        .cta a {
            display: block;
            padding: 1em;
            border: 3px solid red;
            background: #ee0000;
            color: #eee;
            width: 9em;
            font-size: 200%;
            font-weight: bold;
            font-family: sans-serif;
            text-decoration: none;
        }

        .cta a:hover {
            background: #aa0000;
        }

        table td {
            padding: 1px;
            vertical-align: top;
        }
        
        table thead {
            display: none;
        }
        
        table th {
            text-align: right;
            font-weight: bold;
            padding: 1px;
            vertical-align: top;
        }

        table th.last {
            text-align: left;
        }

        .adr p {
            margin: 0;
        }
        
        #hd {
            background: #eee;
            padding: 0.5em;
        }

        ul.profiles {
            overflow: hidden;
        }

        ul.profiles li {
            list-style-type: disc;
            clear: both;
            padding: 0;
        }

        ul.profiles li.known {
            background: url(favicons.png);
            width: 16px;
            height: 16px;
            text-indent: -9999em;
            list-style-type: none;
            float: left;
            clear:none;
            margin: 0.25em;
        }

        ul.profiles li.known a {
            display: block;
            width: 16px;
            height: 16px;
        }

        ul.profiles li.brightkite {
            background-position: 0 -16px;
        }
        ul.profiles li.geocaching {
            background-position: 0 -32px;
        }
        ul.profiles li.flickr {
            background-position: 0 -48px;
        }
        ul.profiles li.lovefilm {
            background-position: 0 -64px;
        }
        ul.profiles li.thetenwordreview {
            background-position: 0 -80px;
        }
        ul.profiles li.slideshare {
            background-position: 0 -96px;
        }
        ul.profiles li.twitter {
            background-position: 0 -112px;
        }
        ul.profiles li.linkedin {
            background-position: 0 -128px;
        }
        ul.profiles li.lastfm {
            background-position: 0 -144px;
        }
        ul.profiles li.technorati {
            background-position: 0 -160px;
        }
        ul.profiles li.mybloglog {
            background-position: 0 -176px;
        }
        ul.profiles li.pixish {
            background-position: 0 -192px;
        }
        ul.profiles li.wikipedia {
            background-position: 0 -208px;
        }
        ul.profiles li.delicious {
            background-position: 0 -224px;
        }
        ul.profiles li.facebook {
            background-position: 0 -240px;
        }
        ul.profiles li.etsy {
            background-position: 0 -256px;
        }
        ul.profiles li.threadless {
            background-position: 0 -272px;
        }
        ul.profiles li.upcoming {
            background-position: 0 -288px;
        }
        ul.profiles li.stumbleupon {
            background-position: 0 -304px;
        }
        ul.profiles li.xbox {
            background-position: 0 -320px;
        }
        ul.profiles li.pownce {
            background-position: 0 -336px;
        }
        ul.profiles li.youtube {
            background-position: 0 -352px;
        }
        ul.profiles li.digg {
            background-position: 0 -368px;
        }
        ul.profiles li.amazon {
            background-position: 0 -384px;
        }
        ul.profiles li.github {
            background-position: 0 -400px;
        }
        ul.profiles li.calendaraboutnothing {
            background-position: 0 -416px;
        }
        ul.profiles li.threesixtyvoice {
            background-position: 0 -432px;
        }
        ul.profiles li.lighthouseapp {
            background-position: 0 -448px;
        }
        
        .mod {
            overflow: hidden;
        }
        
        img.photo {
            float: right;
            max-width: 150px;
        }
    </style>
  </head>
  <body>
    <div id="doc3">
        <div id="hd">
            <div class="yui-g">                 
                <div class="yui-u">
                    <p class='cta'>
                        <a href='<?php echo $url; ?>'>Download as VCF</a>
                    </p>
                </div>
                <div class="yui-u first">
                    <p>For more information about me, and to be able to download a better VCF file, enter a passcode.</p>
                    <form method='post' action=''>
                      <p>
                        <label for='passcode'>Passcode</label>
                        <input type='password' name='passcode' id='passcode'>
                      </p>
                      <p>
                        <input type='submit' value='Show me more?'>
                      </p>
                    </form>
                </div>
            </div>
            <?php echo $success; ?>
        </div>
        <div id="bd">
            <div id="yui-main">
                <div class="yui-b">
                    <?php echo $hCard->toString( isset($_GET['passcode']) ? $_GET['passcode'] : null ); ?>
                </div>
            </div>
        </div>
    </div>
  </body>
</html>