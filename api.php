<?php
// Denne fil returnerer ikke HTML, men data (JSON).
// Den kaldes af JavaScript i browseren med fetch().

require __DIR__ . '/data.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // GET: send alle beskeder.
    echo json_encode(hent_beskeder());
    exit;
}

// POST: gem en ny besked.
$navn = trim($_POST['navn'] ?? '');
$tekst = trim($_POST['tekst'] ?? '');

// Valider altid på serveren. Kontrollen i browseren kan omgås.
if ($navn === '' || $tekst === '' || mb_strlen($navn) > 40 || mb_strlen($tekst) > 200) {
    http_response_code(400);
    echo json_encode(['fejl' => 'Navn og besked skal udfyldes (maks. 40 og 200 tegn).']);
    exit;
}

echo json_encode(gem_besked($navn, $tekst));
