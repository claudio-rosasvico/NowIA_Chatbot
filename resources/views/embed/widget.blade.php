<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $botName }} — Chat</title>
  <style>
    :root {
      --cb-primary:
        {{ $primary }}
      ;
      --cb-secondary:
        {{ $secondary }}
      ;
      --cb-radius:
        {{ $rounded ? '10px' : '0px' }}
      ;
    }

    * {
      box-sizing: border-box;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, sans-serif
    }

    html,
    body {
      height: 100%;
      margin: 0
    }

    .wrap {
      display: flex;
      flex-direction: column;
      height: 100%;
      background: #fff;
      border-radius: var(--cb-radius);
      overflow: hidden
    }

    /* Header Nuevo */
    .header {
      background: var(--cb-primary);
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0
    }

    .header-logo {
      height: 32px;
      width: auto;
      border-radius: 4px;
      object-fit: contain;
    }

    .header-title {
      color: var(--cb-secondary);
      font-weight: 700;
      font-size: 16px;
      margin: 0;
      line-height: 1.2
    }

    .messages {
      flex: 1;
      overflow-y: auto;
      padding: 14px;
      background: var(--cb-secondary);
      display: flex;
      flex-direction: column;
      gap: 12px
    }

    .bubble {
      max-width: 85%;
      padding: 10px 14px;
      border-radius: 12px;
      line-height: 1.4;
      white-space: pre-wrap;
      font-size: 14px;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05)
    }

    .me {
      background: #e1f5fe;
      align-self: flex-end;
      color: #0f172a;
      border-bottom-right-radius: 2px
    }

    .bot {
      background: #fff;
      align-self: flex-start;
      border: 1px solid #e2e8f0;
      border-bottom-left-radius: 2px;
      color: #1e293b
    }

    .inputbar {
      display: flex;
      gap: 8px;
      border-top: 1px solid #e5e7eb;
      padding: 10px;
      background: #fff
    }

    .inputbar input {
      flex: 1;
      padding: 10px 12px;
      border: 1px solid #d1d5db;
      border-radius: 20px;
      font-size: 14px;
      outline: none;
      transition: border-color .2s
    }

    .inputbar input:focus {
      border-color: var(--cb-primary)
    }

    .inputbar button {
      padding: 10px 16px;
      border: 0;
      border-radius: 20px;
      background: var(--cb-primary);
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: opacity .2s
    }

    .inputbar button:hover {
      opacity: 0.9
    }

    .suggests {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      padding: 0 14px 10px;
      background: var(--cb-secondary)
    }

    .chip {
      background: #fff;
      border: 1px solid #e2e8f0;
      color: var(--cb-primary);
      border-radius: 16px;
      padding: 6px 12px;
      font-size: 12px;
      cursor: pointer;
      font-weight: 500;
      transition: all .2s;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03)
    }

    .chip:hover {
      background: #f8fafc;
      transform: translateY(-1px)
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="header">
      @if(!empty($logo))
        <img src="{{ $logo }}" alt="" class="header-logo">
      @endif
      <div class="header-title">{{ $botName }}</div>
    </div>

    <div id="msgs" class="messages"></div>
    <div id="sugs" class="suggests"></div>
    <form id="f" class="inputbar">
      <input id="q" autocomplete="off" placeholder="Escribí tu consulta...">
      <button type="submit">Enviar</button>
    </form>
  </div>

  <script>
    (function () {
      const API = '{{ route('api.embed.chat') }}';
      const PKEY = @json($publicKey);
      const STORE = 'cb_conv_' + PKEY;

      const msgs = document.getElementById('msgs');
      const sugs = document.getElementById('sugs');
      const form = document.getElementById('f');
      const inp = document.getElementById('q');

      let conversation_id = null;

      // restaurar conversación
      try {
        const saved = localStorage.getItem(STORE);
        if (saved) conversation_id = JSON.parse(saved);
      } catch (e) { }

      // bienvenida
      add('assistant', @json($welcomeText));

      // sugerencias
      const suggested = @json($suggested);
      if (Array.isArray(suggested) && suggested.length) {
        suggested.forEach(t => {
          const c = document.createElement('button');
          c.type = 'button';
          c.className = 'chip';
          c.textContent = t;
          c.addEventListener('click', () => { inp.value = t; form.dispatchEvent(new Event('submit', { cancelable: true })); });
          sugs.appendChild(c);
        });
      }

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const q = inp.value.trim();
        if (!q) return;
        add('user', q);
        inp.value = '';

        try {
          const r = await fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ public_key: PKEY, q, conversation_id })
          });
          const j = await r.json();
          if (j.conversation_id) {
            conversation_id = j.conversation_id;
            localStorage.setItem(STORE, JSON.stringify(conversation_id));
          }
          const last = (j.messages || []).slice(-1)[0];
          add('assistant', (last && last.content) ? last.content : (j.answer || '…'));
        } catch (err) {
          add('assistant', 'No pude responder ahora. Probá de nuevo.');
        }
      });

      function add(role, text) {
        const b = document.createElement('div');
        b.className = 'bubble ' + (role === 'user' ? 'me' : 'bot');
        b.textContent = text;
        msgs.appendChild(b);
        msgs.scrollTop = msgs.scrollHeight;
      }
    })();
  </script>
</body>

</html>