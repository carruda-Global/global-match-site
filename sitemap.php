<?php
// Proxy reverso em PHP para /sitemap.xml -> backend Render, com URLs estaticas
// do site (paginas hand-written que nao dependem da tabela seo_pages) sempre
// incluidas, mesmo que o backend esteja fora do ar.
$backendUrl = 'https://engenheiro-producao-ai.onrender.com/api/seo/sitemap.xml';

$staticUrls = [
    '/ecosystem/callreception',
    '/ecosystem/callreception/how-to-choose',
    '/ecosystem/callreception/real-cost-of-a-missed-call',
    '/ecosystem/callreception/vs-smith-ai',
    '/ecosystem/callreception/vs-goodcall',
    '/ecosystem/callreception/vs-retell-ai',
    '/ecosystem/callreception/vs-synthflow',
    '/ecosystem/callreception/ai-vs-answering-service',
    '/blog',
    '/blog/ai-receptionist-for-small-business-guide',
    '/blog/ai-receptionist-cost-2026',
    '/blog/signs-your-business-needs-ai-receptionist',
    '/blog/ai-receptionist-faq',
    '/blog/ai-receptionist-vs-voicemail',
    '/blog/ai-receptionist-data-security',
    '/blog/what-industries-use-ai-receptionists',
];
$staticItems = [];
foreach ($staticUrls as $path) {
    $staticItems[] = '  <url><loc>https://global-engenharia.com' . $path . '</loc><changefreq>weekly</changefreq><priority>0.8</priority></url>';
}

$ch = curl_init($backendUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
$body = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$dynamicItems = [];
if ($body !== false && $httpCode === 200) {
    if (preg_match_all('/<url>.*?<\/url>/s', $body, $matches)) {
        $dynamicItems = $matches[0];
    }
}

$allItems = array_merge($staticItems, $dynamicItems);
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
   . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n"
   . implode("\n", $allItems) . "\n"
   . '</urlset>';
