<?php
// Proxy reverso em PHP para /artigos/{slug} -> backend Render.
// Usado porque mod_proxy do Apache não está completando a conexão
// nesta hospedagem (retornava 503). cURL/PHP funciona em qualquer
// plano com PHP habilitado.

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$slug = preg_replace('/[^a-zA-Z0-9\-_]/', '', $slug);

if ($slug === '') {
    http_response_code(404);
    echo 'Not found';
    exit;
}

$backendUrl = 'https://engenheiro-producao-ai.onrender.com/api/seo/page/' . $slug;

$ch = curl_init($backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$curlError = curl_error($ch);
curl_close($ch);

if ($body === false || $httpCode === 0) {
    http_response_code(502);
    header('Content-Type: text/plain');
    echo "Erro ao buscar conteudo do backend: $curlError";
    exit;
}

http_response_code($httpCode === 200 ? 200 : $httpCode);
header('Content-Type: ' . ($contentType ?: 'text/html; charset=UTF-8'));
echo $body;
