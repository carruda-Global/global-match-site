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
    '/ai-receptionist-guide',
    '/blog/can-ai-receptionist-answer-all-calls',
    '/blog/ai-receptionist-cost-vs-hiring-staff',
    '/blog/will-customers-notice-ai-receptionist',
    '/blog/what-happens-when-ai-cant-answer-a-call',
    '/blog/can-ai-receptionist-book-appointments',
    '/blog/is-ai-receptionist-available-24-7',
    '/blog/how-to-set-up-ai-receptionist-small-business',
    '/blog/can-ai-receptionist-answer-pricing-hours-questions',
    '/blog/will-ai-receptionist-reduce-missed-calls',
    '/blog/how-to-train-ai-receptionist-to-sound-professional',
    '/blog/ai-receptionist-for-field-service-businesses',
    '/blog/best-ai-voice-for-a-receptionist',
    '/blog/do-i-still-need-a-human-receptionist',
    '/blog/how-fast-does-ai-receptionist-answer-calls',
    '/blog/can-ai-receptionist-handle-multiple-calls-at-once',
    '/blog/industries-that-use-ai-receptionists',
    '/blog/how-does-ai-receptionist-learn-my-business-info',
    '/blog/can-i-cancel-ai-receptionist-anytime',
    '/blog/will-ai-receptionist-work-with-my-phone-system',
    '/blog/ai-receptionist-data-privacy-faq',
    '/blog/can-ai-receptionist-answer-appointment-questions',
    '/blog/ai-receptionist-setup-time',
    '/blog/can-ai-receptionist-give-directions',
    '/blog/how-customers-reach-a-human-if-ai-cant-help',
    '/blog/ai-receptionist-for-medical-offices',
    '/blog/can-i-customize-what-ai-receptionist-says',
    '/blog/what-do-reviews-say-about-ai-receptionists',
    '/blog/can-ai-receptionist-handle-angry-customers',
    '/blog/ai-receptionist-vs-virtual-assistant-service',
    '/blog/can-ai-receptionist-answer-faqs',
    '/blog/does-ai-receptionist-improve-professional-image',
    '/blog/how-much-time-does-ai-receptionist-save',
    '/blog/can-i-listen-to-ai-receptionist-call-recordings',
    '/blog/ai-receptionist-or-voicemail-comparison',
    '/blog/ai-receptionist-for-home-service-businesses',
    '/blog/is-ai-receptionist-right-for-my-business',
    '/blog/can-ai-receptionist-share-provider-information',
    '/blog/what-happens-if-ai-receptionist-makes-a-mistake',
    '/blog/ai-receptionist-vs-part-time-receptionist',
    '/blog/ai-receptionist-for-low-call-volume',
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
