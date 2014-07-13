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

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
</head>
<body onload="localStorage.clear();">

  {% set sectionName = controllerName %}
  {% include "partials/navigation/topbar.volt" %}

  {% if currentUser is defined %}
    {% set username = currentUser.username %}
  {% else %}
    {% set username = 'ospite' %}
  {% endif %}

  <div id="scrollable">
    <section class="prompt gutter">
      <p><span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$ {{ method }} {{ url }}</p>
      <p><span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$ Errore 404 (Pagina non trovata)</p>
      <p><span class="at">{{ username }}@pit</span>:<span class="tilde">~</span>$ <blink>&#95</blink></p>
    </section>

    {% include "partials/navigation/footer.volt" %}
  </div>

</body>
</html>