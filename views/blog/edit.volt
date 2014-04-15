<div class="column-left">
  <!-- <div class="alert alert-info">Le tue modifiche saranno poste in coda sino a che il processo di revisione paritaria (peer review) avrà luogo. Ogni modifica, purché costruttiva, è benvenuta. Grazie.</div> -->
  {{ flash.output() }}

  <div id="page-title"><span>{{ doc.title }}</span></div>

  <form class="frm frm-stacked" action="{{ baseUri }}/accedi/" id="signinform" name="signinform" method="post" role="form">
    <fieldset>

        <label for="select-nation">Revisione: </label>
        <select name="select-nation" id="select-nation" placeholder="Nazione...">
          <option value="">Seleziona nazione...</option>
          <optgroup label="North America">
            <option value="1">USA</option>
            <option value="9">Canada</option>
          </optgroup>
          <optgroup label="Europe">
            <option value="2">France</option>
            <option value="3">Spain</option>
            <option value="6">Bulgaria</option>
            <option value="7" disabled="disabled">Greece</option>
            <option value="8">Italy</option>
          </optgroup>
          <optgroup label="Asia" disabled="disabled">
            <option value="5">Japan</option>
            <option value="11">China</option>
          </optgroup>
          <option value="4">Brazil</option>
          <option value="10">South Africa</option>
        </select>

      <script>
        $('#select-nation').selectize({
          sortField: {
            field: 'text',
            direction: 'asc'
          },
          dropdownParent: 'body'
        });
      </script>
    </fieldset>
    <fieldset>
      <ul class="list tabs">
        <li><span><b>Corpo dell'articolo</b></span></li>
        <li class="pull-right"><a href="#preview" data-toggle="tab">ANTEPRIMA</a></li>
        <li class="active pull-right"><a href="#markdown" data-toggle="tab">MARKDOWN</a></li>
      </ul>
      <div class="notebook">
        <div class="notebook-page active" id="markdown">
          <ul class="list toolbar">
            <li class="toolgroup break">
              <a href="#" title="Grassetto"><i class="icon-bold"></i></a>
              <a href="#" title="Corsivo"><i class="icon-italic"></i></a>
            </li>
            <li class="toolgroup break">
              <a href="#" title="Aggiungi un link"><i class="icon-link"></i></a>
              <a href="#" title="Aggiungi un'immagine"><i class="icon-picture"></i></a>
            </li>
            <li class="toolgroup break">
              <a href="#" title="Quota una parte di testo"><i class="icon-angle-right"></i></a>
              <a href="#" title="Aggiungi un blocco di codice"><i class="icon-code"></i></a>
            </li>
            <li class="toolgroup">
              <a href="#" title="Aggiungi ai preferiti"><i class="icon-ellipsis-horizontal"></i></a>
              <a href="#" title="Lista puntata"><i class="icon-list-ul"></i></a>
              <a href="#" title="Lista numerata"><i class="icon-list-ol"></i></a>
            </li>
          </ul>
          {{ text_area("body", "class": "pure-input-1") }}
          <script type="text/javascript">
            var editor = CodeMirror.fromTextArea(document.getElementById("body"), {
              mode: 'gfm',
              lineNumbers: true,
              lineWrapping: true,
              theme: "default",
              viewportMargin: Infinity
            });

            var charWidth = editor.defaultCharWidth(), basePadding = 4;
            editor.on("renderLine", function(cm, line, elt) {
              var off = CodeMirror.countColumn(line.text, null, cm.getOption("tabSize")) * charWidth;
              elt.style.textIndent = "-" + off + "px";
              elt.style.paddingLeft = (basePadding + off) + "px";
            });

            editor.refresh();
          </script>
        </div>
        <div class="notebook-page" id="preview">
        </div>
      </div>
    </fieldset>
    <fieldset>
      <label for="select-to">Email: </label>
      <select id="select-to" class="contacts" placeholder="Pick some people..."></select>
      <script>
        var REGEX_EMAIL = '([a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@' +
          '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)';

        $('#select-to').selectize({
          persist: false,
          maxItems: null,
          valueField: 'email',
          labelField: 'name',
          searchField: ['name', 'email'],
          options: [
            {email: 'brian@thirdroute.com', name: 'Brian Reavis'},
            {email: 'nikola@tesla.com', name: 'Nikola Tesla'},
            {email: 'someone@gmail.com'}
          ],
          render: {
            item: function(item, escape) {
              return '<div>' +
              (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
              (item.email ? '<span class="email">' + escape(item.email) + '</span>' : '') +
              '</div>';
            },
            option: function(item, escape) {
              var label = item.name || item.email;
              var caption = item.name ? item.email : null;
              return '<div>' +
              '<span class="label">' + escape(label) + '</span>' +
              (caption ? '<span class="caption">' + escape(caption) + '</span>' : '') +
              '</div>';
            }
          },
          create: function(input) {
            if ((new RegExp('^' + REGEX_EMAIL + '$', 'i')).test(input)) {
              return {email: input};
            }
            var match = input.match(new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i'));
            if (match) {
              return {
                email : match[2],
                name  : $.trim(match[1])
              };
            }
            alert('Invalid email address.');
            return false;
          }
        });
      </script>
      <label for="select-movie">Movies: </label>
      <select id="select-movie" class="movies" placeholder="Find a movie..."></select>
      <script>
        $('#select-movie').selectize({
          persist: false,
          maxItems: null,
          valueField: 'title',
          labelField: 'title',
          searchField: 'title',
          options: [],
          create: false,
          render: {
            option: function(item, escape) {
              var actors = [];
              for (var i = 0, n = item.abridged_cast.length; i < n; i++) {
                actors.push('<span>' + escape(item.abridged_cast[i].name) + '</span>');
              }

              return '<div>' +
              '<img src="' + escape(item.posters.thumbnail) + '" alt="">' +
              '<span class="title">' +
              '<span class="name">' + escape(item.title) + '</span>' +
              '</span>' +
              '<span class="description">' + escape(item.synopsis || 'No synopsis available at this time.') + '</span>' +
              '<span class="actors">' + (actors.length ? 'Starring ' + actors.join(', ') : 'Actors unavailable') + '</span>' +
              '</div>';
            }
          },
          load: function(query, callback) {
            if (!query.length) return callback();
            $.ajax({
              url: 'http://api.rottentomatoes.com/api/public/v1.0/movies.json',
              type: 'GET',
              dataType: 'jsonp',
              data: {
                q: query,
                page_limit: 10,
                apikey: '3qqmdwbuswut94jv4eua3j85'
              },
              error: function() {
                callback();
              },
              success: function(res) {
                  callback(res.movies);
                }
              });
            }
        });
      </script>
    </fieldset>

    <div class="pure-controls">
      <button type="submit" name="signin" class="btn large red">Salva le modifiche</button>
      <a href="//{{ serverName~doc.getUrl() }}" class="btn large">Annulla</a>
    </div>
  </form>

</div> <!-- /column-left -->

<div class="column-right">

<div class="banner"><a href="#"><img src="/img/300x250cro.jpeg" /></a></div>

</div> <!-- /column-right -->