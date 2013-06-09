<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/html">
<!-- Copyright (c) 2000-2013 3F sas All rights reserved. -->
<!-- Version 7.0 -->
<head>
  <title>{{ title }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <!-- Bootstrap -->
  <link href="pit-bootstrap/css/bootstrap.css" rel="stylesheet/less" />
  <!-- <link href="bootstrap/less/responsive.less" rel="stylesheet/less"> -->
  <!-- <link href="google-theme.less" rel="stylesheet/less"> -->

  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

  <!-- <link href="pippo/less/google-bootstrap.less" rel="stylesheet/less"> -->

  <script src="http://cloud.github.com/downloads/cloudhead/less.js/less-1.3.1.min.js" type="text/javascript"></script>

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <style>
    body {
      background-image: url("img/background.jpg");
    }
  </style>
</head>
<body onload="localStorage.clear();">
  <div class="topbar">
    <ul class="list topbar-elements">
      <li class="topbar-brand">
        <ul class="list">
          <li>
            <a class="topbar-brand-logo" href="./"></a>
          </li>
          <li>
            <a href="./">PROGRAMMAZIONE.IT</a>
          </li>
        </ul>
      </li>
      <li class="search">
        <span><input style="width: 250px; height: 28px;" type="text" /></span>
      </li>
      <li><a href="#"><i class="icon-puzzle-piece"></i>&nbsp;Chi siamo</a></li>
      <li><a href="#"><i class="icon-h-sign"></i>&nbsp;Aiuto</a></li>
      <li class="space"></li>
      <li><a href="#"><img class="gravatar" src="http://www.gravatar.com/avatar/b6799a3261ca303c0b39f991fd9250b4.png" />&nbsp;dedalo</a></li>
    </ul>
  </div>

  <!-- <li class="dropdown-wrapper"><a id="blog" data-toggle="dropdown" href="#">BLOG&nbsp;<span class="toggle" /></a>
  <ul class="dropdown" role="list" aria-labelledby="drop3">
    <li><a tabindex="-1" href="#">Pensiero Digitale</a></li>
  </ul>
  </li> -->
  <!-- <li class="dropdown-wrapper clean"><a data-toggle="dropdown" class="icon-user"></i>pippo</a>
    <ul class="dropdown dropdown-arrow" role="list" aria-labelledby="user">
      <li><a tabindex="-1" href="#"><span class="icon-cog" />&nbsp;Impostazioni</a></li>
      <li><a tabindex="-1" href="#"><span class="icon-user" />&nbsp;Profilo</a></li>
      <li class="divider"></li>
      <li><a tabindex="-1" href="#"><span class="icon-signout" />&nbsp;Disconnetti</a></li>
    </ul>
  </li> -->
  <!-- <li class="dropdown-wrapper clean"><span data-toggle="dropdown" class="icon-search icon-large"></span>
    <ul class="dropdown dropdown-arrow" role="list" aria-labelledby="search">
      <li><a tabindex="-1" href="#"><span class="icon-cog" />&nbsp;Pippo</a></li>
    </ul>
  </li> -->
  <!-- <li class="clean"><a href="#" class="icon-inbox icon-large"></a></li> -->

  <a id="page-skin" href="#"></a>

  <div id="scrollable">
    <div class="banner"><a href="#"><img src="img/970x180.jpg" /></a></div>

    <div id="content">

{{ partial("partials/main-menu") }}

{{ content() }}

    </div> <!-- /content -->

  </div> <!-- /scrollable -->

  <script>
    $(function () {
      $('#myTab a:last').tab('show');
    })
  </script>

  <script src="http://code.jquery.com/jquery-latest.js"></script>

  <script src="pit-bootstrap/js/bootstrap-affix.js"></script>
  <script src="pit-bootstrap/js/bootstrap-alert.js"></script>
  <script src="pit-bootstrap/js/bootstrap-button.js"></script>
  <script src="pit-bootstrap/js/bootstrap-carousel.js"></script>
  <script src="pit-bootstrap/js/bootstrap-collapse.js"></script>
  <script src="pit-bootstrap/js/bootstrap-dropdown.js"></script>
  <script src="pit-bootstrap/js/bootstrap-modal.js"></script>
  <script src="pit-bootstrap/js/bootstrap-popover.js"></script>
  <script src="pit-bootstrap/js/bootstrap-scrollspy.js"></script>
  <script src="pit-bootstrap/js/bootstrap-tab.js"></script>
  <script src="pit-bootstrap/js/bootstrap-tooltip.js"></script>
  <script src="pit-bootstrap/js/bootstrap-transition.js"></script>
  <script src="pit-bootstrap/js/bootstrap-typeahead.js"></script>
</body>
</html>