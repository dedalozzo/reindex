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

  <link rel="shortcut icon" href="{{ baseUri }}/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="{{ controllerPath }}/pit-bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="{{ controllerPath }}/codemirror/lib/codemirror.css">

  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  {% include "partials/codemirror-js.volt" %}
  <style>
    body {
      background-image: url("{{ baseUri }}/pit-bootstrap/img/backgrounds/bg_px.png");
      background-repeat: repeat;
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
    });
  </script>

  {% include "partials/bootstrap-js.volt" %}

</body>
</html>