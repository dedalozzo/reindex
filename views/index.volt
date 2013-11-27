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

    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div style="width: 500px;" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <a href="#" class="close" data-dismiss="modal" aria-hidden="true">&times;</a>
            <h4 class="modal-title" id="myModalLabel">Accedi</h4>
          </div>
          <div class="modal-body">
            <ul class="list">
              <li class="top">
                <form action="{{ baseUri }}/accedi/" id="signinform" name="signinform" method="post" role="form">
                  <ul class="list vertical mbottom10">
                    <li><input type="email" name="email" placeholder="E-mail"></li>
                    <li><input type="password" name="password" placeholder="Password"></li>
                    <li><button type="submit" name="signin" class="btn">Accedi</button></li>
                    <li>
                      <a href="#" target="_self">Hai dimenticato la password?</a><br>
                      <a href="#" target="_self">Non hai ricevuto l'e-mail di attivazione?</a>
                    </li>
                  </ul>
                </form>
              </li>
              <li class="space top"><div class="vr"></div></li>
              <li class="top">
                <ul class="list vertical social-buttons">
                  <li><a id="facebook-btn" rel="facebook" href="#"><span class="logo"></span>Facebook</a></li>
                  <li><a id="twitter-btn" rel="twitter" href="#"><span class="logo"></span>Twitter</a></li>
                  <li><a id="google-btn" rel="google" href="#"><span class="logo"></span>Google+</a></li>
                  <li><a id="linkedin-btn" rel="linkedin" href="#"><span class="logo"></span>LinkedIn</a></li>
                  <li><a id="github-btn" rel="github" href="#"><span class="logo"></span>GitHub</a></li>
                </ul>
              </li>
            </ul>
          </div><!-- /.modal-body -->
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

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