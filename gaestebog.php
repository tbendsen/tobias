<?php
// ===== SERVER-SIDE (PHP) =====
// Alt i PHP-blokke køres på SERVEREN, før siden sendes til browseren.
// Browseren ser aldrig denne kode, kun den HTML, den producerer.

require __DIR__ . '/data.php';

$beskeder = hent_beskeder();
$serverTid = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="da">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gæstebog</title>
  <style>
    body { font-family: sans-serif; max-width: 600px; margin: 2em auto; padding: 0 1em; }
    .boks { border: 2px solid; padding: 0.5em 1em; margin: 1em 0; border-radius: 6px; }
    .server { border-color: #3a7bd5; background: #eef4fc; }
    .klient { border-color: #2e9e5b; background: #edf8f1; }
    li { margin: 0.4em 0; }
    small { color: #666; }
  </style>
</head>
<body>
  <h1>Gæstebog</h1>

  <div class="boks server">
    <strong>Server (PHP):</strong> Siden blev bygget kl. <?= $serverTid ?>.
    Tiden står stille, fordi PHP kun kører én gang, når siden hentes.
  </div>

  <div class="boks klient">
    <strong>Browser (JavaScript):</strong> Klokken er nu <span id="klientTid">?</span>.
    Tiden tæller, fordi JavaScript kører hele tiden i din browser.
  </div>

  <h2>Skriv en besked</h2>
  <form id="formular">
    <input id="navn" placeholder="Dit navn" required maxlength="40">
    <input id="tekst" placeholder="Din besked" required maxlength="200">
    <button>Send</button>
  </form>

  <h2>Beskeder</h2>
  <!-- Listen herunder er lavet af PHP på serveren. -->
  <ul id="liste">
    <?php foreach ($beskeder as $b): ?>
      <li>
        <strong><?= htmlspecialchars($b['navn']) ?>:</strong>
        <?= htmlspecialchars($b['tekst']) ?>
        <small>(<?= htmlspecialchars($b['tid']) ?>)</small>
      </li>
    <?php endforeach; ?>
  </ul>

  <p><a href="index.html">Tilbage til forsiden</a></p>

  <script>
    // ===== CLIENT-SIDE (JavaScript) =====
    // Denne kode sendes som tekst til browseren og køres DÉR.

    // 1) Et ur, der opdateres hvert sekund, uden at kontakte serveren.
    function visTid() {
      document.getElementById("klientTid").textContent =
        new Date().toLocaleTimeString("da-DK");
    }
    visTid();
    setInterval(visTid, 1000);

    // 2) Send en besked til serveren i baggrunden (uden at genindlæse siden).
    document.getElementById("formular").addEventListener("submit", async function (e) {
      e.preventDefault(); // Stop browserens normale "genindlæs siden".

      const data = new FormData();
      data.append("navn", document.getElementById("navn").value);
      data.append("tekst", document.getElementById("tekst").value);

      // fetch() sender en forespørgsel til api.php, som PHP behandler på serveren.
      const svar = await fetch("api.php", { method: "POST", body: data });
      const besked = await svar.json();

      if (!svar.ok) {
        alert(besked.fejl);
        return;
      }

      // Serveren har gemt beskeden; nu tilføjer JavaScript den til siden.
      const li = document.createElement("li");
      const navn = document.createElement("strong");
      navn.textContent = besked.navn + ": ";
      const tid = document.createElement("small");
      tid.textContent = " (" + besked.tid + ")";
      li.append(navn, besked.tekst, tid);
      document.getElementById("liste").prepend(li);

      document.getElementById("tekst").value = "";
    });
  </script>
</body>
</html>
