<?php

error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 1);

define('ERR_LOG_FILE', dirname(__FILE__) . '/investing_scraper_error.txt');
define('URL', 'https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7');

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

curl_setopt($ch, CURLOPT_URL, URL);
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
if (!$doc->loadHTML($out)) {
    errLog("Не удалось загрузить документ " . URL);
    exit;
}

if (!$xpath = new DOMXpath($doc)) {
    errLog('Создание DOMXpath() не удалось');
    exit;
}

if (!$rows = $xpath->query("//table[contains(@class, 'technicalSummaryTbl')]/tbody/tr")) {
    errLog('xpath не верный !!!');
    exit;
}

// Данные о таблице
$table = [];

//foreach ($rows as $row) {
for ($r = 0; $r < $rows->length; ++$r) {

    $pos = $r % 3;
    if ($pos == 0) {
        $column = ['head' => '', 'data' => []];
    }

    $row = $rows->item($r);
    if ($tr = $row->getElementsByTagName('td')) {

        if ($pos == 0) {
            $td = $tr->item(0);
            if (strpos($td->textContent, 'RUB') === false) {
                $column['head'] = $aElem->textContent;
            } else {
                $r += 2;
                continue;
            }
        } elseif ($pos == 2) {
            for ($c = 2; $c < 4; ++$c) {
                if ($td = $tr->item($c)) {
                    //$column['data'][] =
                } else {
                    errLog('Не удалось получить ячейку элемента ' . print_r($tr, true));
                }
            }
        }

        if ($pos == 2) {
            $table[] = $column;
        }

    } else {
        errLog('Не удалось получить getElementsByTagName("td"). Элемент: ' . print_r($row, true));
    }
}

print_r($table);

function errLog($text)
{
    file_put_contents(ERR_LOG_FILE, date(DATE_ATOM) . ": $text\n", FILE_APPEND);
}
