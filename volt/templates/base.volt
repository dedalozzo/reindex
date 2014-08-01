<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/html">
<!-- Copyright (c) 2000-{{ year }} 3F sas All rights reserved. -->
<!-- Version {{ version }} -->
<head>
  <title>{{ title }} - {{ domainName|capitalize }}</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">

  {#
  <meta name="author" content="">
  <meta name="keywords" content="words">
  <meta name="description" content="{{ description }}">
  #}

  {# opengroup
  <meta property="og:description" content="{{ description }}">
  <meta property="og:title" content="{{ title }}">
  <meta property="og:type" content="{{ type }}">
  <meta property="og:url" content="{{ canonical }}">
  #}

  {# robots
    <meta name="robots" content="selection">
    <meta name="revisit-after" content="period">
    <meta name="googlebot" content="noodp">
  #}

  <link rel="icon" href="/favicon.ico" type="image/x-icon">

  {{ assets.outputCss() }}

  {# HTML5 shim, for IE6-8 support of HTML5 elements #}
  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  {{ assets.outputJs() }}

  <style>
    body {
      background-image: url(//{{ domainName }}/pit-bootstrap/dist/img/bg/bg_px.png);
      background-repeat: repeat;
    }
  </style>
</head>
<body onload="localStorage.clear();">

  {% block topbar %}
    {% include "partials/navigation/topbar.volt" %}
  {% endblock %}

  {% block skin %}
    <a id="skin" href="#"></a>
  {% endblock %}

  <div id="{% block container %}scrollable{% endblock %}">
  {% block billboard %}
    <div class="banner"><a href="#"><img src="/img/970x180.jpg" /></a></div>
  {% endblock %}

  {% block header %}
  {% endblock %}

  {% block content %}
    <div id="content">

    </div> <!-- /content -->
  {% endblock %}

  {% block footer %}
    {% include "partials/navigation/footer.volt" %}
  {% endblock %}
  </div>

  {% block script %}
  {% endblock %}

</body>
</html>