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

  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="//{{ serverName }}/pit-bootstrap/dist/css/pit.css">

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
  <script src="//{{ serverName }}/pit-bootstrap/dist/js/dropdown.min.js"></script>

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <style>
    body {
      background-image: url(//{{ domainName }}/pit-bootstrap/dist/img/bg/bg_px.png);
      background-repeat: repeat;
    }
  </style>
</head>
<body onload="localStorage.clear();">

  {% if currentUser is defined %}
    {% set username = currentUser.username %}
  {% else %}
    {% set username = 'ospite' %}
  {% endif %}

  <div id="fixed">
    <ul class="list auto">
      {% include "partials/brand.volt" %}
    </ul>

    <div id="content">

      <div id="page-title">{{ title }}</div>
      <hr class="fade-long">

      {{ flash.output() }}

      <div class="welcome">
        <img src="//{{ serverName }}/pit-bootstrap/dist/img/404.jpg" width="530" height="261">
        <p>
          La pagina richiesta non è disponibile. Il link che hai seguito non è funzionante o la pagina è stata rimossa.
        </p>
      </div>

      <aside class="prompt gutter">
        <p>
          <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;{{ method }}&nbsp;{{ url }}<br>
          <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;Error&nbsp;404&nbsp;(Page&nbsp;Not&nbsp;Found)<br>
          <span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$&nbsp;<blink>&#95</blink>
        </p>
      </aside>

    </div> <!-- /content -->

    {% include "partials/navigation/footer.volt" %}
  </div> <!-- /fixed -->

</body>
</html>