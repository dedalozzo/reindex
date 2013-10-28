<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/html">
<!-- Copyright (c) 2000-2013 3F sas All rights reserved. -->
<!-- Version 7.0 -->
<head>
  <title>{{ title }} - {{ serverName|capitalize }}</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Bootstrap -->
  <link rel="stylesheet" href="{{ controllerPath }}/pit-bootstrap/css/bootstrap.css" rel="text/css" />

  <link rel="shortcut icon" href="{{ controllerPath }}/favicon.ico" type="image/x-icon" />

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <style>
    body {
      background-image: url("/img/background.jpg");
    }
  </style>
</head>
<body onload="localStorage.clear();">

  {% include "partials/navigation/topbar.volt" %}

  <a id="page-skin" href="#"></a>

  <div id="scrollable">
    <div class="banner"><a href="#"><img src="/img/970x180.jpg" /></a></div>

    <div id="content">

      {% include "partials/navigation/main-menu.volt" %}

      {{ content() }}

    </div> <!-- /content -->

    {% include "partials/navigation/footer.volt" %}

  </div> <!-- /scrollable -->

  <script>
    $(function () {
      $('#myTab a:last').tab('show');
    })
  </script>

  {% include "partials/bootstrap-js.volt" %}

</body>
</html>