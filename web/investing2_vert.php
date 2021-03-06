<?php

error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 1);

define('ERR_LOG_FILE', dirname(__FILE__) . '/investing_scraper_error.txt');
//define('URL', 'https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7');
define('URL', 'https://ru.investing.com/technical/Service/GetSummaryTable');
define('TABLE_FILE', dirname(__FILE__) . '/gen_table.html');

define('ACTIVE_SELL_TEXT', 'Активно продавать');
define('ACTIVE_BUY_TEXT', 'Активно покупать');

define('EXPIRE_RATIO', 3);
$TIME_MAPPING = [5, 15, 30];

$post_fields = [
    'tab' => 'forex',
    'options' => [
        'periods' => [
            '300', '900', '1800', '86400'
        ],
        'receive_email' => 'false',
        'currencies' => [
            '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12',
        ],
    ],
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Requested-With: XMLHttpRequest", "Content-Type: application/x-www-form-urlencoded"]);

curl_setopt($ch, CURLOPT_URL, URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_NOBODY, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_FAILONERROR, 0);

curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_COOKIESESSION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/investing-cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/investing-cookie.txt');

curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
//curl_setopt($ch, CURLOPT_POSTFIELDS, 'tab=forex&options%5Bperiods%5D%5B%5D=300&options%5Bperiods%5D%5B%5D=900&options%5Bperiods%5D%5B%5D=1800&options%5Bperiods%5D%5B%5D=86400&options%5Breceive_email%5D=false&options%5Bcurrencies%5D%5B%5D=1&options%5Bcurrencies%5D%5B%5D=2&options%5Bcurrencies%5D%5B%5D=3&options%5Bcurrencies%5D%5B%5D=4&options%5Bcurrencies%5D%5B%5D=5&options%5Bcurrencies%5D%5B%5D=6&options%5Bcurrencies%5D%5B%5D=7&options%5Bcurrencies%5D%5B%5D=8&options%5Bcurrencies%5D%5B%5D=9&options%5Bcurrencies%5D%5B%5D=10&options%5Bcurrencies%5D%5B%5D=11&options%5Bcurrencies%5D%5B%5D=12');

$outJson = curl_exec($ch);

$out = json_decode($outJson);
//print_r($out->html);
#file_put_contents('tt_cookies_7.html', $out->html);
//exit;

$doc = new DOMDocument();
if (!$doc->loadHTML('<?xml encoding="utf-8" ?>' . $out->html)) {
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

for ($r = 0; $r < $rows->length; ++$r) {

    $pos = $r % 3;
    if ($pos == 0) {
        $column = ['head' => '', 'data' => []];
    }

    $row = $rows->item($r);
    if ($tr = $row->getElementsByTagName('td')) {

        if ($pos == 0) {
            $td = $tr->item(0);
            if (($a = $td->getElementsByTagName('a')) && $a->length) {
                if (strpos($a->item(0)->textContent, 'RUB') === false) {
                    $column['head'] = $a->item(0)->textContent;
                } else {
                    $r += 2;
                    continue;
                }
            } else {
                errLog('Нет тега <a> для элемента <td>: ' . print_r($td, true));
            }
        } elseif ($pos == 2) {
            for ($c = 1; $c <= 3; ++$c) {
                if ($td = $tr->item($c)) {
                    if (strpos($td->textContent, ACTIVE_SELL_TEXT) !== false
                        || strpos($td->textContent, ACTIVE_BUY_TEXT) !== false
                    ) {
                        $column['data'][] = trim($td->textContent);
                    } else {
                        $column['data'][] = '';
                    }
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

//print_r($table);

$html = createTable($table);
file_put_contents(TABLE_FILE, $html);

echo 'Investing script is over';

function errLog($text)
{
    file_put_contents(ERR_LOG_FILE, date(DATE_ATOM) . ": $text\n", FILE_APPEND);
    echo "There were errors: $text\n";
}

function createTable($info)
{
    global $TIME_MAPPING;

    $t = "<table>\n";

    foreach ($info as $el) {
        $t .= '<tr>';
        $t .= '<td>' . $el['head'] . "</td>\n";
        for ($i = 0; $i < 3; ++$i) {
            $textExpire = 'экспирация: ' . ($TIME_MAPPING[$i] * EXPIRE_RATIO);
            switch ($el['data'][$i]) {
                case ACTIVE_SELL_TEXT:
                    $cellText = "<span class='arrow down'>&#x25BC</span> вниз<br>$textExpire";
                    break;
                case ACTIVE_BUY_TEXT:
                    $cellText = "<span class='arrow up'>&#x25B2</span> вверх<br>$textExpire";
                    break;
                default:
                    $cellText = '&nbsp;';
                    break;
            }
            $t .= "<td>$cellText</td>\n";
        }
        $t .= '</tr>';
    }

    $t .= "</table>\n";

    return $t;
}
