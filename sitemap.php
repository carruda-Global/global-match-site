<?php
// Proxy reverso em PHP para /sitemap.xml -> backend Render.
$backendUrl = 'https://engenheiro-producao-ai.onrender.com/api/seo/sitemap.xml';

$ch = curl_init($backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($body === false || $httpCode === 0) {
    http_response_code(502);
    header('Content-Type: text/plain');
    echo "Erro ao buscar sitemap do backend: $curlError";
    exit;
}

http_response_code($httpCode === 200 ? 200 : $httpCode);
header('Content-Type: application/xml; charset=UTF-8');
echo $body;
