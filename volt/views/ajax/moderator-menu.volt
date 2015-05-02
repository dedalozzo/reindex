<ul class="dropdown-menu">
  {% if post.canBeProtected() %}
    {% set showDiv = TRUE %}
    <li><button id='m-close' title="impedisci che vengono aggiunte ulteriori risposte alla domanda"><i class="icon-lock"></i>Chiudi</button></li>
    <li><button id='m-lock' title="proteggi la domanda da eventuali modifiche"><i class="icon-umbrella"></i>Proteggi</button></li>
  {% elseif post.canBeUnprotected() %}
    {% set showDiv = TRUE %}
    <li><button id='m-unprotect' title="sproteggi"><i class="icon-sun"></i>Sproteggi</button></li>
  {% endif %}
  {% if post.canVisibilityBeChanged() %}
    {% set showDiv = TRUE %}
    {% if post.isVisible() %}
      <li><button id="m-hide" title="nascondi"><i class="icon-eye-close"></i>Nascondi</button></li>
    {% else %}
      <li><button id="m-show" title="mostra"><i class="icon-eye-open"></i>Mostra</button></li>
    {% endif %}
  {% endif %}
  {% if showDiv is defined %}
    <li class="dropdown-divider"></li>
  {% endif %}
  {% if post.canBeMovedToTrash() %}
    <li><button id="m-trash"><i class="icon-trash"></i>Butta nel cestino</button></li>
  {% elseif post.canBeRestored() %}
    <li><button id="m-undo"><i class="icon-undo"></i>Recupera dal cestino</button></li>
  {% endif %}
</ul>