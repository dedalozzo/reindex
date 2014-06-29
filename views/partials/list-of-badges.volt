{% if !(badges is empty) %}
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
      <td><a href="//{{ serverName~'/'~badge['name']|lower }}" class="badge"><i class="icon-certificate {{ badge['metal'] }}"></i> {{ badge['name'] }}</td>
      <td>{{ badge['brief'] }}</td>
      <td>{{ badge['awarded'] }}</td>
      <td>{% if badge['earned'] > 0 %}<i class="icon-ok icon-large"></i>&nbsp;{% endif %}</td>
    </tr>
  {% endfor %}
  </tbody>
</table>
{% else %}
<div class="alert alert-info">Siamo spiacenti, la ricerca non ha prodotto alcun risultato.</div>
{% endif %}
