<?php

//enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

//include config file
include 'config.php';

//set LOAD DATA INFILE local variable to 1
$sql = "SET GLOBAL local_infile = 1";
if (mysqli_query($conn, $sql)) {
    echo "local_infile set to 1<br />";
} else {
    echo "Error setting local_infile: " . mysqli_error($conn);
}


//If dont exist create a log file
$myfile = fopen("log.txt", "w") or die("Unable to open file!");


//Create mysqli connection to database server with credentials from config.php
$conn = mysqli_connect($hostname, $username, $password, $dbname);

//show error message if connection failed and write a new line in log file with date-time and error message else show message in browser window and write it to log file
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error()); 
    fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") .  mysqli_connect_error()); 
}
echo "Connected successfully<br />";
fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " Connected successfully<br />");


//Create table inventoryTest if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS inventoryTest (
ITEMID varchar(50) NOT NULL PRIMARY KEY,
ITEMNAME varchar(190),
PDSDISPOSITIONCODE varchar(30),
INVENTBATCHID varchar(60),
PRODDATE varchar(30),
EXPDATE varchar(10),
unitid varchar(10)
)";

//IF table creation failed then show error message in browser window and write it to log file else show message in browser window and write it to log file
if (mysqli_query($conn, $sql)) {
    echo "Table inventoryTest created successfully<br />";
    fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " Table inventoryTest created successfully<br />");
} else {
    echo "Error creating table: " . mysqli_error($conn);
    fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " Error creating table: " . mysqli_error($conn));
}



//Open data file and show register count in browser window and write it to log file
$myfile = fopen($dbfile, "r") or die("Unable to open file!");
echo "Register count: " . count(file($dbfile)) . "<br />";
//fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " Register count: " . count(file($dbfile)) . "<br />");
 
//Read data file and create an insert statement for each line
 

//Create a progress bar from 0 to 100% in browser window and set css style
echo '<div id="progress" style="width:500px;border:1px solid #ccc;"></div>';
echo '<div id="information" style="width"></div>';


//Set php time limit to 0 to avoid script timeout
set_time_limit(0);


//Delete all records from table inventoryTest
$sql = "DELETE FROM inventoryTest";
if (mysqli_query($conn, $sql)) {
    echo "All records deleted successfully<br />";
    fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " All records deleted successfully<br />");
} else {
    echo "Error deleting records: " . mysqli_error($conn);
    fwrite($myfile, "date-time: " . date("Y-m-d h:i:sa") . " Error deleting records: " . mysqli_error($conn));
}   


//Count number of lines in data file and set it to $lines variable. 
$lines = count(file($dbfile));
//Show number of lines in browser
echo "Number of lines: " . $lines . "<br />";

//Show start message in browser window
echo "Start importing data...<br />";


// Leer el archivo CSV
if (($handle = fopen($dbfile, "r")) !== false) {
    while (($data = fgetcsv($handle, 0, ",")) !== false) {

        //show data in browser window
        echo "ITEMID: " . $data[0] . " - ITEMNAME: " . $data[1] . " - PDSDISPOSITIONCODE: " . $data[2] . " - INVENTBATCHID: " . $data[3] . " - PRODDATE: " . $data[4] . " - EXPDATE: " . $data[5] . " - unitid: " . $data[6] . "<br />";
        $query = "INSERT INTO inventoryTest (ITEMID, ITEMNAME, PDSDISPOSITIONCODE, INVENTBATCHID, PRODDATE, EXPDATE, unitid) VALUES ('" . $data[0] . "','" . $data[1] . "','" . $data[2] . "','" . $data[3] . "','" . $data[4] . "','" . $data[5] . "','" . $data[6] . "')";
        $result = mysqli_query($conn, $query);
        if ($result === true) {
            echo "La inserción se realizó correctamente." . "<br />" ;
        } else {
            echo "Error al insertar datos: " . mysqli_error($conn) . "<br />";
        }

        // Escapar los valores de las filas para evitar inyección de SQL
       //  $data = array_map('mysqli_real_escape_string', $conn, $data);
        
        // Generar la consulta INSERT in a batch    
        // $query = "INSERT INTO inventoryTest (ITEMID, ITEMNAME, PDSDISPOSITIONCODE, INVENTBATCHID, PRODDATE, EXPDATE, unitid) VALUES ('" . implode("','", $data) . "')";
         
        
        // //commit query
        // mysqli_query($conn, $query);
        // //Get current line number
        // $line = count(file($dbfile)) - $lines;
        // //Calculate progress
        // $progress = round($line / $lines * 100);
        // //Print progress bar
        // echo '<script language="javascript">
        // document.getElementById("progress").innerHTML="<div style=\"width:' . $progress . '%;background-color:#ddd;\">&nbsp;</div>";
        // document.getElementById("information").innerHTML="' . $line . ' row(s) processed.";
        // </script>';
        // //Flush output buffer
        // ob_flush();
        // flush();
        // //Sleep one second
        // sleep(1);

         
    }

}


//Write end message
echo "Data imported successfully<br />";

//Close connection
mysqli_close($conn);
//Close log file
fclose($myfile);
  
?>
