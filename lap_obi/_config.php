<?php

class DBConfig
{

    private const DBHOST = '172.31.146.253'; // Update with your PostgreSQL host
    private const DBUSER = 'edp'; // Update with your PostgreSQL username
    private const DBPASS = '3dp1grVIEW'; // Update with your PostgreSQL password
    private const DBNAME = 'spibdl1r'; // Update with your PostgreSQL database name
    private const DBPORT = '5432'; // Default PostgreSQL port is 5432

    protected $conn;

    private $dsn = "pgsql:host=" . self::DBHOST . ";port=" . self::DBPORT . ";dbname=" . self::DBNAME;

    function __construct()
    {
        try {
            $this->conn = new PDO($this->dsn, self::DBUSER, self::DBPASS, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT => true // Optional: Add persistent connection
            ));
			//echo "Connected successfully to PostgreSQL database.";
        } catch (PDOException $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    function __destruct()
    {
        $this->conn = NULL;
    }
}
?>
