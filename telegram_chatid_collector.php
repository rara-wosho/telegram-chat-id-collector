<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Telegram Chat ID Collector</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;600;700;800&display=swap');

  :root {
    --bg: #0d0f14;
    --surface: #151820;
    --surface2: #1c2030;
    --border: #252a3a;
    --accent: #4af0a0;
    --accent2: #4a9ff0;
    --warn: #f0a04a;
    --danger: #f05a4a;
    --text: #e2e8f0;
    --muted: #6b7a99;
    --code-bg: #0a0c10;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'Syne', sans-serif;
    min-height: 100vh;
    padding: 40px 20px;
  }

  .grid-bg {
    position: fixed; inset: 0; z-index: 0;
    background-image:
      linear-gradient(rgba(74,240,160,0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(74,240,160,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
  }

  .container {
    position: relative; z-index: 1;
    max-width: 860px;
    margin: 0 auto;
  }

  header {
    margin-bottom: 40px;
  }

  .badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(74,240,160,0.1);
    border: 1px solid rgba(74,240,160,0.25);
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 11px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent);
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin-bottom: 16px;
  }

  .badge::before { content: '●'; font-size: 8px; }

  h1 {
    font-size: clamp(28px, 5vw, 44px);
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
    margin-bottom: 10px;
  }

  h1 span { color: var(--accent); }

  .subtitle {
    color: var(--muted);
    font-size: 14px;
    font-family: 'JetBrains Mono', monospace;
  }

  .card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 28px;
    margin-bottom: 20px;
  }

  .card-header {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 20px;
  }

  .step-num {
    width: 28px; height: 28px;
    background: var(--accent);
    color: #000;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .card-title {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: -0.01em;
  }

  label {
    display: block;
    font-size: 12px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 8px;
  }

  input[type="text"] {
    width: 100%;
    background: var(--code-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--accent);
    font-family: 'JetBrains Mono', monospace;
    font-size: 14px;
    padding: 12px 16px;
    outline: none;
    transition: border-color 0.2s;
  }

  input[type="text"]:focus { border-color: var(--accent); }
  input[type="text"]::placeholder { color: var(--muted); }

  .hint {
    font-size: 11px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--muted);
    margin-top: 6px;
  }

  .hint a { color: var(--accent2); text-decoration: none; }
  .hint a:hover { text-decoration: underline; }

  .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-family: 'Syne', sans-serif;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.15s;
    letter-spacing: 0.01em;
  }

  .btn-primary {
    background: var(--accent);
    color: #000;
    width: 100%;
    justify-content: center;
    font-size: 15px;
    padding: 14px;
    margin-top: 20px;
  }

  .btn-primary:hover { background: #6af8b4; transform: translateY(-1px); }
  .btn-primary:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

  .btn-sm {
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    font-size: 12px;
    padding: 6px 12px;
  }

  .btn-sm:hover { border-color: var(--accent); color: var(--accent); }

  .loading {
    display: none;
    text-align: center;
    padding: 30px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--muted);
    font-size: 13px;
  }

  .spinner {
    display: inline-block;
    width: 20px; height: 20px;
    border: 2px solid var(--border);
    border-top-color: var(--accent);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin-right: 10px;
    vertical-align: middle;
  }

  @keyframes spin { to { transform: rotate(360deg); } }

  /* Results table */
  .results-wrap { display: none; }

  .results-meta {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 16px; flex-wrap: gap;
  }

  .count-badge {
    background: rgba(74,240,160,0.1);
    border: 1px solid rgba(74,240,160,0.2);
    border-radius: 6px;
    padding: 4px 12px;
    font-size: 12px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent);
  }

  .actions { display: flex; gap: 8px; flex-wrap: wrap; }

  table {
    width: 100%;
    border-collapse: collapse;
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
  }

  thead tr {
    background: var(--code-bg);
    border-bottom: 1px solid var(--border);
  }

  th {
    padding: 10px 14px;
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--muted);
    font-weight: 600;
  }

  tbody tr {
    border-bottom: 1px solid rgba(37,42,58,0.5);
    transition: background 0.1s;
  }

  tbody tr:hover { background: rgba(74,240,160,0.03); }

  td {
    padding: 11px 14px;
    color: var(--text);
  }

  td.chat-id {
    color: var(--accent);
    font-weight: 600;
  }

  td.username { color: var(--accent2); }

  .copy-cell { text-align: right; }

  .copy-btn {
    background: none;
    border: 1px solid var(--border);
    border-radius: 4px;
    color: var(--muted);
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    padding: 3px 8px;
    cursor: pointer;
    transition: all 0.15s;
  }

  .copy-btn:hover { border-color: var(--accent); color: var(--accent); }
  .copy-btn.copied { border-color: var(--accent); color: var(--accent); }

  .empty-state {
    display: none;
    text-align: center;
    padding: 50px 20px;
    color: var(--muted);
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
  }

  .empty-icon { font-size: 40px; margin-bottom: 12px; }

  .alert {
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 13px;
    font-family: 'JetBrains Mono', monospace;
    margin-top: 16px;
    display: none;
  }

  .alert-error {
    background: rgba(240,90,74,0.1);
    border: 1px solid rgba(240,90,74,0.3);
    color: #f08a80;
  }

  .alert-warn {
    background: rgba(240,160,74,0.1);
    border: 1px solid rgba(240,160,74,0.3);
    color: var(--warn);
  }

  .table-wrap {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid var(--border);
  }

  .how-to {
    background: var(--code-bg);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 16px 20px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 12px;
    color: var(--muted);
    line-height: 1.8;
    margin-top: 16px;
  }

  .how-to strong { color: var(--accent); }

  footer {
    text-align: center;
    margin-top: 40px;
    color: var(--muted);
    font-size: 12px;
    font-family: 'JetBrains Mono', monospace;
  }
</style>
</head>
<body>

<div class="grid-bg"></div>

<div class="container">

  <header>
    <div class="badge">Telegram Tool</div>
    <h1>Chat ID <span>Collector</span></h1>
    <p class="subtitle">// Fetch all user chat IDs from your bot's message history</p>
  </header>

  <!-- Step 1: Token Input -->
  <div class="card">
    <div class="card-header">
      <div class="step-num">1</div>
      <div class="card-title">Enter Your Bot Token</div>
    </div>
    <label for="token">Bot Token</label>
    <input type="text" id="token" placeholder="1234567890:ABCdefGHIjklMNOpqrSTUvwxYZ" autocomplete="off" />
    <p class="hint">
      Get your token from <a href="https://t.me/BotFather" target="_blank">@BotFather</a> on Telegram →
      /newbot or /token
    </p>

    <div class="how-to">
      <strong>How to run this locally in VS Code:</strong><br>
      1. Save this file as <strong>index.php</strong><br>
      2. Make sure PHP is installed — run <strong>php -v</strong> in terminal<br>
      3. In VS Code terminal: <strong>php -S localhost:8080</strong><br>
      4. Open browser: <strong>http://localhost:8080</strong><br>
      5. Paste your bot token above and click Fetch
    </div>

    <div class="alert alert-warn" id="alert-warn"></div>
    <div class="alert alert-error" id="alert-error"></div>

    <button class="btn btn-primary" id="fetch-btn" onclick="fetchChatIds()">
      ⚡ Fetch Chat IDs
    </button>
  </div>

  <!-- Loading -->
  <div class="loading" id="loading">
    <span class="spinner"></span> Fetching updates from Telegram API...
  </div>

  <!-- Results -->
  <div class="card results-wrap" id="results-wrap">
    <div class="card-header">
      <div class="step-num">2</div>
      <div class="card-title">Collected Chat IDs</div>
    </div>
    <div class="results-meta">
      <span class="count-badge" id="count-badge">0 users</span>
      <div class="actions">
        <button class="btn btn-sm" onclick="copyAll()">📋 Copy All IDs</button>
        <button class="btn btn-sm" onclick="exportCSV()">⬇ Export CSV</button>
        <button class="btn btn-sm" onclick="exportJSON()">⬇ Export JSON</button>
      </div>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Chat ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Type</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="results-body"></tbody>
      </table>
    </div>

    <div class="empty-state" id="empty-state">
      <div class="empty-icon">📭</div>
      No messages found. Make sure users have sent messages to your bot first.
    </div>
  </div>

  <footer>telegram chat id collector · runs fully local · no data stored externally</footer>

</div>

<?php
// ============================================================
// PHP BACKEND — handles the AJAX call from the browser
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch') {
    header('Content-Type: application/json');

    $token = trim($_POST['token'] ?? '');

    if (empty($token)) {
        echo json_encode(['ok' => false, 'error' => 'Bot token is required.']);
        exit;
    }

    // Basic token format validation
    if (!preg_match('/^\d+:[A-Za-z0-9_-]{35,}$/', $token)) {
        echo json_encode(['ok' => false, 'error' => 'Invalid token format. It should look like: 123456789:ABCdefGHI...']);
        exit;
    }

    $apiBase = "https://api.telegram.org/bot{$token}";
    $users   = [];
    $offset  = 0;
    $limit   = 100;

    do {
        $url = "{$apiBase}/getUpdates?offset={$offset}&limit={$limit}&timeout=0";
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            echo json_encode(['ok' => false, 'error' => "cURL error: {$curlErr}"]);
            exit;
        }

        $data = json_decode($response, true);

        if (!$data || !$data['ok']) {
            $desc = $data['description'] ?? 'Unknown Telegram API error.';
            echo json_encode(['ok' => false, 'error' => $desc]);
            exit;
        }

        $updates = $data['result'];

        foreach ($updates as $update) {
            $chatId   = null;
            $username = null;
            $fname    = null;
            $lname    = null;
            $type     = null;

            if (isset($update['message'])) {
                $chat     = $update['message']['chat'];
                $chatId   = $chat['id'];
                $username = $chat['username'] ?? '';
                $fname    = $chat['first_name'] ?? '';
                $lname    = $chat['last_name'] ?? '';
                $type     = $chat['type'] ?? 'private';
            } elseif (isset($update['callback_query'])) {
                $from     = $update['callback_query']['from'];
                $chatId   = $from['id'];
                $username = $from['username'] ?? '';
                $fname    = $from['first_name'] ?? '';
                $lname    = $from['last_name'] ?? '';
                $type     = 'private';
            } elseif (isset($update['edited_message'])) {
                $chat     = $update['edited_message']['chat'];
                $chatId   = $chat['id'];
                $username = $chat['username'] ?? '';
                $fname    = $chat['first_name'] ?? '';
                $lname    = $chat['last_name'] ?? '';
                $type     = $chat['type'] ?? 'private';
            } elseif (isset($update['channel_post'])) {
                $chat     = $update['channel_post']['chat'];
                $chatId   = $chat['id'];
                $username = $chat['username'] ?? '';
                $fname    = $chat['title'] ?? '';
                $lname    = '';
                $type     = 'channel';
            }

            if ($chatId !== null) {
                $key = (string)$chatId;
                if (!isset($users[$key])) {
                    $users[$key] = [
                        'chat_id'  => $chatId,
                        'username' => $username,
                        'name'     => trim("{$fname} {$lname}"),
                        'type'     => $type,
                    ];
                }
            }

            $offset = $update['update_id'] + 1;
        }

    } while (count($updates) === $limit);

    echo json_encode([
        'ok'    => true,
        'count' => count($users),
        'users' => array_values($users),
    ]);
    exit;
}
?>

<script>
let collectedUsers = [];

async function fetchChatIds() {
  const token = document.getElementById('token').value.trim();
  const errEl  = document.getElementById('alert-error');
  const warnEl = document.getElementById('alert-warn');

  errEl.style.display  = 'none';
  warnEl.style.display = 'none';

  if (!token) {
    showError('Please enter your bot token first.');
    return;
  }

  document.getElementById('fetch-btn').disabled = true;
  document.getElementById('loading').style.display = 'block';
  document.getElementById('results-wrap').style.display = 'none';

  try {
    const form = new FormData();
    form.append('action', 'fetch');
    form.append('token', token);

    const res  = await fetch(window.location.href, { method: 'POST', body: form });
    const data = await res.json();

    document.getElementById('loading').style.display = 'none';
    document.getElementById('fetch-btn').disabled = false;

    if (!data.ok) {
      showError(data.error || 'Something went wrong.');
      return;
    }

    collectedUsers = data.users;
    renderResults(data.users);

    if (data.count === 0) {
      warnEl.textContent = 'No messages found yet. Have users send a message to your bot first, then try again.';
      warnEl.style.display = 'block';
    }

  } catch (e) {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('fetch-btn').disabled = false;
    showError('Request failed. Make sure PHP server is running (php -S localhost:8080).');
  }
}

function renderResults(users) {
  const wrap  = document.getElementById('results-wrap');
  const tbody = document.getElementById('results-body');
  const badge = document.getElementById('count-badge');
  const empty = document.getElementById('empty-state');

  wrap.style.display = 'block';
  tbody.innerHTML = '';

  badge.textContent = `${users.length} user${users.length !== 1 ? 's' : ''}`;

  if (users.length === 0) {
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';

  users.forEach((u, i) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td style="color:var(--muted)">${i + 1}</td>
      <td class="chat-id">${u.chat_id}</td>
      <td class="username">${u.username ? '@' + u.username : '<span style="color:var(--muted)">—</span>'}</td>
      <td>${u.name || '<span style="color:var(--muted)">—</span>'}</td>
      <td><span style="color:var(--muted);font-size:11px">${u.type}</span></td>
      <td class="copy-cell">
        <button class="copy-btn" onclick="copySingle(this, '${u.chat_id}')">copy</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function copySingle(btn, id) {
  navigator.clipboard.writeText(String(id));
  btn.textContent = 'copied!';
  btn.classList.add('copied');
  setTimeout(() => { btn.textContent = 'copy'; btn.classList.remove('copied'); }, 1500);
}

function copyAll() {
  const ids = collectedUsers.map(u => u.chat_id).join('\n');
  navigator.clipboard.writeText(ids);
  alert('Copied ' + collectedUsers.length + ' chat IDs to clipboard!');
}

function exportCSV() {
  const rows = [['chat_id','username','name','type']];
  collectedUsers.forEach(u => rows.push([u.chat_id, u.username, u.name, u.type]));
  const csv = rows.map(r => r.map(v => `"${String(v).replace(/"/g,'""')}"`).join(',')).join('\n');
  download('telegram_chat_ids.csv', csv, 'text/csv');
}

function exportJSON() {
  download('telegram_chat_ids.json', JSON.stringify(collectedUsers, null, 2), 'application/json');
}

function download(filename, content, mime) {
  const a = document.createElement('a');
  a.href = URL.createObjectURL(new Blob([content], { type: mime }));
  a.download = filename;
  a.click();
}

function showError(msg) {
  const el = document.getElementById('alert-error');
  el.textContent = '⚠ ' + msg;
  el.style.display = 'block';
}

// Allow pressing Enter to fetch
document.getElementById('token').addEventListener('keydown', e => {
  if (e.key === 'Enter') fetchChatIds();
});
</script>
</body>
</html>
