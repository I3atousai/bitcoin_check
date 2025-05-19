<?php 
class DB
{
    public static function connect(): PDO
    {
        try {
            // подключаемся к серверу
            $conn = new PDO("mysql:host=localhost;dbname=host1879893", "host1879893", "EazCg2Zzch", [
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            return $conn;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    

}
  function add(array $data, string $mode = "count"): string|null
    {
        try {
            $connect = DB::connect();
            $table = 'host1879893'; // название таблицы
            $sql = "INSERT INTO $table ";
            $keys = array_keys($data);
            $count_keys = count($keys);
            $keys_str = implode(",", $keys);
            $sql .= " ($keys_str) VALUES (";
            for ($i = 0; $i < $count_keys; $i++) {
                $sql .= "?,";
            }
            $sql = substr($sql, 0, -1); // убираем лишнюю ,
            $sql .= ")";

            $req = $connect->prepare($sql);
            $req->execute(array_values($data));
            if ($mode == "id") {
                return $connect->lastInsertId();
            } else if ($mode == "count") {
                return $req->rowCount();
            }
            return $sql;
        } catch (\Throwable $th) {
            echo $th->getMessage();
            return null;
        }
    }

    function getBtc () :int|null{
       try {$connect = DB::connect();
        $sql = "select high from candles where id=(select max(id) from candles )";
        $req = $connect->prepare($sql);
            $req->execute();
        return $req->fetch()['high'];
        }
        catch (\Throwable $th) {
        echo $th->getMessage();
        return null;
        }
    }

    


// доступ к API
$start = strtotime(date("Y-m-d H:i:s")).'000';
$end = strtotime(date("Y-m-d H:i:s"))-86400 .'000';
$symbol = "BTCUSDT";
$interval = "5"; //интервал 5 минут, можно: 3,5,15,60,день,неделя
$limit = 199;
$url = "https://api.bybit.com/v5/market/kline?category=spot&symbol=$symbol&interval=$interval&limit=$limit&start=$start&end=$end";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
$candle_amount = null;
$cource = 0;
echo( "<pre>");
// print_r($data);
// 198 свечек за ~16 часов с перерывом 5 минут
$usd_to_btc = '';
if (isset($data['result']['list'])) {
    $candle_amount = sizeof($data['result']['list']);
    $candles = $data['result']['list'];
    $cource = getBtc();
    $usd_to_btc = "Currently 1 bitcoin is equal to {$cource} dollars";

    // foreach ($candles as $candle) {
    //     echo "Time: " . date('Y-m-d H:i:s', $candle[0] / 1000) . "\n";
    //     echo "Open: " . $candle[1] . "\n";
    //     echo "High: " . $candle[2] . "\n";
    //     echo "Low: " . $candle[3] . "\n";
    //     echo "Close: " . $candle[4] . "\n\n";
    // }
    
    // foreach ($candles as $candle) {
    //     add([
    //           'time' => date('Y-m-d H:i:s', $candle[0] / 1000),
    //           "open " => $candle[1],
    //      "high " => $candle[2],
    //      "low " => $candle[3],
    //      "close " => $candle[4] ,
    //      'volume' => $candle[5]
    //     ]);
    // }


} else {
    echo "Error fetching data: " . print_r($data, true);
}

echo( "</pre>");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        #chart {
            height: 60vh;
            width: 95vw;
            background-color: #00000;
        }
    </style>
    <script src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <p><?php echo $usd_to_btc ?></p>
    <div id="chart"></div>
    <script src="script.js"></script>
</body>
</html>