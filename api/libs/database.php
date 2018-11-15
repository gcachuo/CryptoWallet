<?php
/**
 * Created by PhpStorm.
 * User: memo
 * Date: 15/11/18
 * Time: 05:16 PM
 */

$mysqli = db_connect();

/**
 * @return mysqli
 */
function db_connect()
{
    $config = [
        "user" => getenv('CONFIG_DB_USER'),
        "password" => getenv('CONFIG_DB_PASSWORD'),
        "database" => getenv('CONFIG_DB_DATABASE')
    ];
    $mysqli = new mysqli("localhost", $config['user'], $config['password'], $config['database']);
    return $mysqli;
}

/**
 * @param $sql
 * @return bool|mysqli_result
 */
function db_query($sql)
{
    global $mysqli;
    return $mysqli->query($sql);
}

/**
 * @param $sql
 * @return array|null
 * @throws Exception
 */
function db_result($sql)
{
    global $mysqli;
    $mysqli_result = db_query($sql);
    $result = null;
    if ($mysqli_result) {
        $result = $mysqli_result->fetch_assoc();
    } else {
        set_error($mysqli->error);
    }
    return $result;
}

/**
 * @return int
 */
function db_last_id()
{
    global $mysqli;
    return $mysqli->insert_id;
}

/**
 * @param string $sql
 * @param int $type
 * @return array|null
 * @throws Exception
 */
function db_all_results($sql, $type = MYSQLI_ASSOC)
{
    global $mysqli;
    $mysqli_result = db_query($sql);
    $result = null;
    if ($mysqli_result) {
        $result = $mysqli_result->fetch_all($type);
    } else {
        set_error($mysqli->error);
    }
    return $result;
}

/**
 * @throws Exception
 */
function db_error()
{
    global $mysqli;
    set_error("(" . $mysqli->errno . ") " . $mysqli->error, 500);
}