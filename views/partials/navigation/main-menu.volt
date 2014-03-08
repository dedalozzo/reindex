<ul class="list pills large gutter-plus">
  {% for item in mainMenu %}
  <li{{ (item['name'] == controllerName) ? ' class="active"' : '' }}><a href="http://{{ item['path']~serverName }}"><i class="icon-{{ item['icon'] }}" ></i>&nbsp;{{ item['label'] }}</a></li>
  {% endfor %}
  <li class="space"></li>
  {% if controllerName == 'index' %}
  <li class="icon"><a href="http://twitter.com/prg_it"><i class="icon-twitter icon-large"></i></a></li>
  <li class="icon"><a href="http://facebook.com/programmazione.it"><i class="icon-facebook icon-large"></i></a></li>
  <li class="icon"><a href="#"><i class="icon-google-plus icon-large"></i></a></li>
  {% elseif controllerName == 'questions' %}
  <li><a class="orange" href="#"><i class="icon-question"></i> FAI UNA DOMANDA</a></li>
  {% elseif controllerName == 'links' %}
  <li><a class="orange" href="#"><i class="icon-link"></i> AGGIUNGI UN LINK</a></li>
  {% elseif controllerName == 'blog' %}
  <li><a class="orange" href="#"><i class="icon-code"></i> SCRIVI SUL BLOG</a></li>
  {% endif %}
</ul>