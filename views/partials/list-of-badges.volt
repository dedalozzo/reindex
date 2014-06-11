{% set badgesBaseUrl = '//badges.'~domainName~'/' %}
{% if badges is defined %}
<table class="pluto gutter">
  <thead>
    <tr>
      <th colspan="2">Nome</th>
      <th>Descrizione</th>
      <th>Assegnati</th>
    </tr>
  </thead>
  <tbody>
  {% for badge in badges %}
    <tr>
      <td><i class="icon-ok"></i>&nbsp;</td>
      <td><a href="//{{ serverName~'/'~badge['name']|lower }}" class="badge"><i class="icon-certificate {{ badge['metal'] }}"></i> {{ badge['name'] }}</td>
      <td>{{ badge['brief'] }}</td>
      <td style="text-align: center;">0</td>
    </tr>
  {% elsefor %}
    <div class="alert alert-info">Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endfor %}
  </tbody>
</table>
{% endif %}
