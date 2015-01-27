{# Menu bar #}
<nav class="menubar">
  <ul class="list gutter">
    <li><a class="tag alt" href="//{{ domainName }}"><i class="icon-home"></i>&nbsp;home</a></li>
    {% include 'partials/types.volt' %}
    {% for name, path in types %}
    <li><a class="tag {{ name }}" href="//{{ domainName~'/'~path }}/">{{ path }}</a></li>
    {% endfor %}
    <li><a class="tag alt" href="//{{ domainName }}/tags/"><i class="icon-tags"></i>&nbsp;tags</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/badges/"><i class="icon-certificate"></i>&nbsp;badges</a></li>
    <li><a class="tag alt" href="//{{ domainName }}/utenti/"><i class="icon-group"></i>&nbsp;utenti</a></li>
    <li class="space"></li>
    <li><a class="tag alt twitter" href="http://twitter.com/prg_it"><i class="icon-twitter nameless"></i></a></li>
    <li><a class="tag alt facebook" href="http://facebook.com/programmazione.it"><i class="icon-facebook nameless"></i></a></li>
    <li><a class="tag alt google" href="#"><i class="icon-google-plus nameless"></i></a></li>
    <li><a class="tag alt linkedin" href="https://www.linkedin.com/company/programmazione.it"><i class="icon-linkedin nameless"></i></a></li>
    <li><a class="tag alt rss" href="http://programmazione.it/rss"><i class="icon-rss nameless"></i></a></li>
  </ul>
</nav>