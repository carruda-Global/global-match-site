<?php
// GA4 proxy via Measurement Protocol (contorna ad blockers)
// Uso: POST com form-encoded (compativel com g/collect) ou JSON (MP nativo)

$GA4_ID = 'G-V13C09C3DT';
$API_SECRET = 'GnCF0rxyREq1nDL3YiE0RQ';

$raw = file_get_contents('php://input');
if (empty($raw)) {
    $raw = http_build_query($_POST);
}
if (empty($raw)) {
    http_response_code(400);
    exit;
}

// Tenta decodificar como JSON (MP nativo)
$mpPayload = null;
$json = json_decode($raw, true);
if ($json && isset($json['client_id']) && isset($json['events'])) {
    $mpPayload = $json;
}

// Se nao for JSON, converte de form-encoded (g/collect -> MP)
if (!$mpPayload) {
    parse_str($raw, $params);

    $clientId = $params['cid'] ?? '00000000-0000-0000-0000-000000000000';
    $eventName = $params['en'] ?? 'page_view';

    $eventParams = [];

    // Mapeia parametros padrao g/collect -> MP
    if (!empty($params['dl'])) $eventParams['page_location'] = $params['dl'];
    if (!empty($params['dr'])) $eventParams['page_referrer'] = $params['dr'];
    if (!empty($params['dt'])) $eventParams['page_title'] = $params['dt'];

    // Parametros personalizados (ep.*)
    foreach ($params as $key => $value) {
        if (str_starts_with($key, 'ep.') || str_starts_with($key, 'ep_')) {
            $clean = preg_replace('/^(ep\.|ep_)/', '', $key);
            $eventParams[$clean] = $value;
        }
    }

    $eventParams['session_id'] = strval(time());
    $eventParams['engagement_time_msec'] = '1';

    $mpPayload = [
        'client_id' => $clientId,
        'events' => [
            [
                'name' => $eventName,
                'params' => $eventParams
            ]
        ]
    ];
}

// Envia para Measurement Protocol
$mpUrl = "https://www.google-analytics.com/mp/collect?measurement_id={$GA4_ID}&api_secret={$API_SECRET}";

$ch = curl_init($mpUrl);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($mpPayload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ]
]);

$body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// 204 = No Content (sucesso), 2xx = sucesso
http_response_code(($httpCode >= 200 && $httpCode < 300) ? 204 : 502);
