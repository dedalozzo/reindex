{% if postType == 'question' %}
  {% set section = 'domande' %}
  {% set sectionUrl = '//domande.'~domainName %}
{% elseif postType == 'link' %}
  {% set section = 'links' %}
  {% set sectionUrl = '//links.'~domainName %}
{% elseif postType == 'article' %}
  {% set section = 'articoli' %}
  {% set sectionUrl = '//blog.'~domainName~'/articoli/' %}
{% elseif postType == 'book' %}
  {% set section = 'libri' %}
  {% set sectionUrl = '//blog.'~domainName~'/libri/' %}
{% else %}
  {% set section = 'guide' %}
  {% set sectionUrl = '//blog.'~domainName~'/guide/' %}
{% endif %}