{% set mainMenu = [
  ['name': 'index', 'path': '', 'label': 'P.IT', 'icon': 'home'],
  ['name': 'questions', 'path': 'domande.', 'label': 'DOMANDE', 'icon': 'question'],
  ['name': 'links', 'path': 'links.', 'label': 'LINKS', 'icon': 'link'],
  ['name': 'blog', 'path': 'blog.', 'label': 'BLOG', 'icon': 'code'],
  ['name': 'tags', 'path': 'tags.', 'label': 'TAGS', 'icon': 'tags'],
  ['name': 'badges', 'path': 'badges.', 'label': 'BADGES', 'icon': 'certificate'],
  ['name': 'users', 'path': 'utenti.', 'label': 'UTENTI', 'icon': 'group']
] %}

<ul class="list pills large gutter-plus">
  {% for item in mainMenu %}
  <li{{ (item['name'] == controllerName) ? ' class="active"' : '' }}><a href="//{{ item['path']~domainName }}"><i class="icon-{{ item['icon'] }}" ></i>&nbsp;{{ item['label'] }}</a></li>
  {% endfor %}
  <li class="space"></li>
  {% if controllerName == 'index' %}
  <li class="icon"><a href="http://twitter.com/prg_it"><i class="icon-twitter icon-large"></i></a></li>
  <li class="icon"><a href="http://facebook.com/programmazione.it"><i class="icon-facebook icon-large"></i></a></li>
  <li class="icon"><a href="#"><i class="icon-google-plus icon-large"></i></a></li>
  {% elseif controllerName == 'questions' %}
    <li><button class="btn mini orange"><i class="icon-plus"></i> FAI UNA DOMANDA</button></li>
  {% elseif controllerName == 'links' %}
  <li><button class="btn mini orange"><i class="icon-plus"></i> AGGIUNGI UN LINK</button></li>
  {% elseif controllerName == 'blog' %}
    <li><button class="btn mini orange"><i class="icon-plus"></i> SCRIVI UN ARTICOLO</button></li>
  {% endif %}
</ul>