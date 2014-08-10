<nav class="menubar">
  <ul class="list types gutter">
    <li><a class="tag alt" href="//{{ domainName }}/home/"><i class="icon-home"></i>&nbsp;home</a></li>
    {% include 'partials/types.volt' %}
    {% for name, path in types %}
    <li><a class="tag {{ name }}" href="//{{ domainName~'/'~path }}/">{{ path }}</a></li>
    {% endfor %}
    <li><a class="tag alt" href="//{{ domainName }}/tags/"><i class="icon-tags"></i>&nbsp;tags</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/badges/"><i class="icon-certificate"></i>&nbsp;badges</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/utenti/"><i class="icon-group"></i>&nbsp;utenti</a></li>
    <li class="space"></li>
  </ul>

  {#
  <ul class="list pills no-gutter">
    <li><a href="//{{ domainName }}/tour/">Tour</a></li>
    <li><a href="//{{ domainName }}/aiuto/">Aiuto</a></li>
    <li class="icon"><a href="http://twitter.com/prg_it"><i class="icon-twitter icon-large"></i></a></li>
    <li class="icon"><a href="http://facebook.com/programmazione.it"><i class="icon-facebook icon-large"></i></a></li>
    <li class="icon"><a href="#"><i class="icon-google-plus icon-large"></i></a></li>
  </ul>
  #}
</nav>