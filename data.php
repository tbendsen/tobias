<?php
date_default_timezone_set('Europe/Copenhagen');

// En meget simpel "database": beskederne gemmes i en JSON-fil på serveren.

const DATAFIL = __DIR__ . '/data/beskeder.json';

function hent_beskeder(): array
{
    if (!file_exists(DATAFIL)) {
        return [];
    }
    return json_decode(file_get_contents(DATAFIL), true) ?? [];
}

function gem_besked(string $navn, string $tekst): array
{
    $besked = [
        'navn' => $navn,
        'tekst' => $tekst,
        'tid' => date('d-m-Y H:i'),
    ];

    // Lås filen, så to samtidige brugere ikke overskriver hinanden.
    $fil = fopen(DATAFIL, 'c+');
    flock($fil, LOCK_EX);
    $beskeder = json_decode(stream_get_contents($fil), true) ?? [];
    array_unshift($beskeder, $besked); // Nyeste først.
    ftruncate($fil, 0);
    rewind($fil);
    fwrite($fil, json_encode($beskeder, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    flock($fil, LOCK_UN);
    fclose($fil);

    return $besked;
}
