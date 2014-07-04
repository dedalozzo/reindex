<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/html">
<!-- Copyright (c) 2000-{{ year }} 3F sas All rights reserved. -->
<!-- Version {{ version }} -->
<head>
  <title>{{ title }} - {{ domainName|capitalize }}</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

  <link rel="shortcut icon" href="//{{ domainName }}/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="//{{ serverName }}/pit-bootstrap/dist/css/pit.css">

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.11/jquery.scrollTo.min.js"></script> -->
  <script src="//{{ serverName }}/pit-bootstrap/dist/js/tab.min.js"></script>
  <script src="//{{ serverName }}/pit-bootstrap/dist/js/dropdown.min.js"></script>
  <script src="//{{ serverName }}/pit-bootstrap/dist/js/selectize.min.js"></script>
  <script src="//{{ serverName }}/pit-bootstrap/dist/js/pit.min.js"></script>

{% include "partials/codemirror.volt" %}

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <style>
    body {
      background-image: url(//{{ domainName }}/pit-bootstrap/dist/img/backgrounds/bg_px.png);
      background-repeat: repeat;
    }
  </style>
</head>
<body onload="localStorage.clear();">

  {% block topbar %}
    {% set sectionName = controllerName %}
    {% include "partials/navigation/topbar.volt" %}
  {% endblock %}

  <a id="skin" href="#"></a>

  {% block scrollable %}
  <div id="scrollable">
    <div class="banner"><a href="#"><img src="/img/970x180.jpg" /></a></div>

    <div id="content">
      {{ content() }}
    </div> <!-- /content -->

    {% include "partials/navigation/footer.volt" %}

  </div> <!-- /scrollable -->
  {% endblock %}

  <script>
    $(function () {
      $('#myTab a:last').tab('show');
    });
  </script>

</body>
</html>