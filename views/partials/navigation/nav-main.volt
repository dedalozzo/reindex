<ul class="list pills large">
  {% for i, item in controllermenu %}
  <li{{ (i == controllerindex) ? ' class="active"' : '' }}><a href="{{ item['link'] }}"><i class="icon-{{ item['icon'] }}" ></i>&nbsp;{{ item['name'] }}</a></li>
  {% endfor %}
  <li class="space"></li>
  {% if controllerindex == 0  %}
  <li class="icon"><a class="icon" href="http://twitter.com/prg_it"><i class="icon-twitter icon-large"></i></a></li>
  <li class="icon"><a class="icon" href="http://facebook.com/programmazione.it"><i class="icon-facebook icon-large"></i></a></li>
  <li class="icon"><a class="icon" href="#"><i class="icon-google-plus icon-large"></i></a></li>
  {% elseif controllerindex == 2 %}
  <li><a class="button" href="#"><i class="icon-question"></i>&nbsp;FAI UNA DOMANDA</a></li>
  {% elseif controllerindex == 3 %}
  <li><a class="button" href="#"><i class="icon-link"></i> AGGIUNGI UN LINK</a></li>
  {% endif %}
</ul>