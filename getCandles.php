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
    $data = [];

       try {$connect = DB::connect();
        $sql = "SELECT time, open, high, low, close FROM `candles` WHERE 1 ORDER BY id DESC;";
        $req = $connect->prepare($sql);
            $req->execute();
            $data = $req->fetchAll();
        }
        catch (\Throwable $th) {
        echo $th->getMessage();
        return null;
        }

    // echo "<pre>";
    // print_r($data);
    // echo "</pre>";
http_response_code(200);
echo json_encode([
    'candles' => $data,
]);


?>
    