<?php

define('ERR_LOG_FILE', dirname(__FILE__), 'investing_scraper_error.txt');

$url = 'https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7';

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, 0);
//curl_setopt($ch, CURLOPT_COOKIEJAR, 'paypal-cookie.txt');

curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)");
curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

$out = curl_exec($ch);


$doc = new DOMDocument();
if (!$doc->loadHTMLFile($url))
{
    file_put_contents(ERR_LOG_FILE, "Не удалось загрузить документ $url\n", FILE_APPEND);
    exit;
}
$xpath = new DOMXpath($doc);
//"//levelone[myfield[attributes/myatt='a]]]"
//$cols = $xpath->query('//table/tr/td');
$cols = $xpath->query("//table[@myatt='technicalSummaryTbl']");


