<nav class="menubar">
  <ul class="list gutter">
    <li><a class="tag alt" href="//{{ domainName }}/home/"><i class="icon-home"></i>&nbsp;home</a></li>
    {% include 'partials/types.volt' %}
    {% for name, path in types %}
    <li><a class="tag {{ name }}" href="//{{ domainName~'/'~path }}/">{{ path }}</a></li>
    {% endfor %}
    <li><a class="tag alt" href="//{{ domainName }}/tags/"><i class="icon-tags"></i>&nbsp;tags</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/badges/"><i class="icon-certificate"></i>&nbsp;badges</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/utenti/"><i class="icon-group"></i>&nbsp;utenti</a></li>
    <li class="space"></li>
    <li><a class="tag twitter" href="http://twitter.com/prg_it"><i class="icon-twitter no-text"></i></a></li>
    <li><a class="tag facebook" href="http://facebook.com/programmazione.it"><i class="icon-facebook no-text"></i></a></li>
    <li><a class="tag google" href="#"><i class="icon-google-plus no-text"></i></a></li>
  </ul>
</nav>