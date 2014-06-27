{% if badges is defined %}
<table class="badges gutter">
  <thead>
    <tr>
      <th>Nome</th>
      <th>Descrizione</th>
      <th>Assegnati</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  {% for name, badge in badges %}
    <tr>
      <td><a href="//{{ serverName~'/'~name|lower }}" class="badge"><i class="icon-certificate {{ badge['metal'] }}"></i> {{ name }}</td>
      <td>{{ badge['brief'] }}</td>
      <td>0</td>
      <td><i class="icon-ok icon-large"></i>&nbsp;</td>
    </tr>
  {% elsefor %}
    <div class="alert alert-info">Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
  {% endfor %}
  </tbody>
</table>
{% endif %}
