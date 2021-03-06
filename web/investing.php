<?php

error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 1);

define('ERR_LOG_FILE', dirname(__FILE__) . '/investing_scraper_error.txt');
define('URL', 'https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7');
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
/*curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'POST /technical/Service/GetSummaryTable HTTP/1.1',
    'Host: ru.investing.com',
    'Connection: keep-alive',
    'Origin: https://ru.investing.com',
//'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
    'Content-Type: application/x-www-form-urlencoded',
    //'X-Requested-With: XMLHttpRequest',
    //'Referer: https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7',
    //'Accept-Encoding: gzip, deflate, br',
    //'Accept-Language: en-US,en;q=0.8,ru;q=0.6,uk;q=0.4',
]);*/

//curl_setopt($ch, CURLOPT_HTTPHEADER, ["X-Requested-With: XMLHttpRequest", "Content-Type: application/x-www-form-urlencoded"]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'GET /technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7 HTTP/1.1',
    'Host: ru.investing.com',
    'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.5',
    //'Accept-Encoding: gzip, deflate, br',
    'Referer: https://ru.investing.com/technical/%D0%A1%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9-%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B9-%D0%90%D0%BD%D0%B0%D0%BB%D0%B8%D0%B7',
    'Cookie: adBlockerNewUserDomains=1497241017; gtmFired=OK; optimizelyEndUserId=oeu1497241025700r0.5154706410810994; optimizelySegments=%7B%224225444387%22%3A%22ff%22%2C%224226973206%22%3A%22referral%22%2C%224232593061%22%3A%22false%22%2C%225010352657%22%3A%22none%22%7D; optimizelyBuckets=%7B%7D; travelDistance=3; _ga=GA1.2.1511620097.1497241027; _gid=GA1.2.450395781.1497241027; _ym_uid=1497241028715373296; _ym_isad=2; __qca=P0-451479020-1497241030186; __gads=ID=9a7de7a028bd9660:T=1497241030:S=ALNI_MZRseBAMJV65XwqEBBzVXOSAQFHzA; geoC=RU; last_visit=1497270918025::1497281718025; nyxDorf=Njs%2FbDV9P2FjNm5lYC0yMTZlZjxheGBgYGNiZg%3D%3D; _ym_visorc_23341048=w; PHPSESSID=5dgkmrbkir51sgm5ga6vihs412; StickySession=id.14764842635.237ru.investing.com; billboardCounter_7=0; ses_id=MX9lJGBvZm5kIDo8NGVhYzNjNWphYWFrPT9kYmBnZHJkcD4wNGM%2FeTY5YC4wMzcrYGgyYTFhYWBlYTU%2FOzhvbzEzZWRgN2Y5ZGc6YDRhYTEzZzVmYWZhaj1tZGdgZ2Q9ZGY%2BaTQ7P282Y2BuMD43P2ByMi4xdWFwZTc1ZTt6bygxPmUkYDNmM2QxOmA0Z2FiMzM1amEzYTY9PGRhYGdkfGQv',
    'Connection: keep-alive',
    'Upgrade-Insecure-Requests: 1',
]);


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
//curl_setopt($ch, CURLOPT_POST, 1);
//curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
//curl_setopt($ch, CURLOPT_POSTFIELDS, 'tab=forex&options%5Bperiods%5D%5B%5D=300&options%5Bperiods%5D%5B%5D=900&options%5Bperiods%5D%5B%5D=1800&options%5Bperiods%5D%5B%5D=86400&options%5Breceive_email%5D=false&options%5Bcurrencies%5D%5B%5D=1&options%5Bcurrencies%5D%5B%5D=2&options%5Bcurrencies%5D%5B%5D=3&options%5Bcurrencies%5D%5B%5D=4&options%5Bcurrencies%5D%5B%5D=5&options%5Bcurrencies%5D%5B%5D=6&options%5Bcurrencies%5D%5B%5D=7&options%5Bcurrencies%5D%5B%5D=8&options%5Bcurrencies%5D%5B%5D=9&options%5Bcurrencies%5D%5B%5D=10&options%5Bcurrencies%5D%5B%5D=11&options%5Bcurrencies%5D%5B%5D=12');

$out = curl_exec($ch);

#file_put_contents('tt_cookies_4.html', $out);
#exit;

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

    // Header
    $t .= "<thead>\n<tr>\n";
    foreach ($info as $el) {
        $t .= '<td>' . $el['head'] . "</td>\n";

    }
    $t .= "</tr>\n</thead>\n";

    // Body
    $t .= "<tbody>\n";
    for ($i = 0; $i < 3; ++$i) {
        $t .= "<tr>\n";
        $textExpire = 'экспирация: ' . ($TIME_MAPPING[$i] * EXPIRE_RATIO);
        foreach ($info as $el) {
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
        $t .= "</tr>\n";
    }
    $t .= "</tbody>\n";

    $t .= "</table>";

    return $t;
}
