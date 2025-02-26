<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$dsn      = "mysql:host=localhost;dbname=college_ngo";  //data source host and db name
$username = "root";
$password = "";
$msg = "";
 
// if(($_SERVER['REQUEST_METHOD'] == 'POST') && ($_POST["city"] != "none")) {

    // Create connection
    $conn = new PDO($dsn, $username, $password); // creates PDO object

    // Check connection using try/catch statement

    try  {
        $conn = new PDO($dsn, $username, $password);
        echo "Connection is successful<br><br>";
    }

    catch (PDOException $e) {
        $error_message = $e->getMessage();
        echo "An error occurred: $error_message" ;
    }

    // sql statement set up
    $sql = "SELECT lname, fname, address, city, postal_code, phone, email, city
            FROM members 
            ORDER BY postal_code, lname";
    $statement = $conn->prepare($sql);

    // execute (create) the result set
    $statement->execute();

    // row count
    $rowcount = $statement->rowCount();

    // just to test
    echo "Row count is $rowcount<br>";

    //get a list of unique cities 
    $cities = [];

    $city_select = "SELECT DISTINCT city FROM members";
    $city_query = $conn->prepare($city_select);
    $city_query->execute();

    $data = $city_query->fetchAll();
    foreach($data as $da) {
        array_push($cities, $da['city']);
    }
    
    $rowcount2 = $city_query->rowCount();
    echo "Select count is $rowcount2<br>";

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (($_POST["city"] != "none")) {
            $selection = $_POST['city'];
            $sql = "SELECT lname, fname, address, city, postal_code, phone, email, city
                    FROM members 
                    WHERE city = '$selection';
                    ORDER BY postal_code, lname";
            $statement = $conn->prepare($sql);
            $statement->execute();
            $rowcount = $statement->rowCount();
            echo "Row count is $rowcount<br>";
        } else {
            $selection = "All Cities";
        }
    }  
?>

<!DOCTYPE html>
<!-- Khang Ngo -->
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Student Population</title>

    <style>
        body {
            font-family: arial, sans-serif;
            font-size: 100%;
        }   

         h1 {
            text-align: center;
            font-size: 1.5em;
        }

         h2 {
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.25em;
        }    


          td {
            border: 1px solid #000; 
            padding: 10px; 
            vertical-align: top;
            width: 10%;
            font-size: .8em; 
        }

        th {
            background: #000;
            color: #fff;
            height: 20px;
            padding: 10px;
            font-size: 1em;
            width: 10%;
        }


        table {
            border-collapse: collapse;
            border: 2px solid #000;
            width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        tbody tr:nth-of-type(odd) {
            background: #eee;
        }

        .center {
            text-align: center;
        }

        .long {
            width: 15%;
        }

        .address {
            width: 20%;
        }
    </style>
</head>

<body>
   
 <header>  
    <h1>Student Population by City of Residence</h1>
    <h2>Number of Students Living in <?php echo "$selection: $rowcount";?></h2>
 </header>

 <form action='<?php echo $_SERVER['PHP_SELF'] ?>' method='post'>
    <label for='city'>Select a City:</label>
    <select name='city' id='city'>
        <option value='none'>Make a Selection</option>
        <?php
            foreach ($cities as $city) {
                echo "<option value='$city'>$city</option>";
            }
        ?>
    </select>
    <input type='submit' value='Display Cities'>
 </form>
 
 <?php
     
 // check to make sure we have records returned
if ($rowcount != 0){
    
    // header row of table
  echo "<table>\n\r";  
  echo "<tr>\n\r"; 
  echo "<th>Last Name</th>\n\r"; 
  echo "<th>First Name</th>\n\r"; 
  echo "<th class='long'>Address</th>\n\r";
  echo "<th>City</th>\n\r";
  echo "<th>Postal Code</th>\n\r";
  echo "<th>Phone</th>\n\r";
  echo "<th class='long'>Email</th>\n\r"; 
  echo "</tr>\n\r\n\r"; 
    

  // output data of each row as associative array in result set
  $rows = $statement->fetchAll();

  
    //  body of table 
 foreach($rows as $row) {
   echo "<tr>\n\r";
   echo "<td>" . $row["lname"] . "</td>\n\r";
   echo "<td>" . $row["fname"] . "</td>\n\r";
   echo "<td class='address'>" . $row["address"] . "</td>\n\r";
   echo "<td class='center long'>" . $row["city"] . "</td>\n\r";
   echo "<td class='center'>" . $row["postal_code"] . "</td>\n\r";
   echo "<td class='center'>" . $row["phone"] . "</td>\n\r";
   echo "<td class='long'>" . $row["email"] . "</td>\n\r";
   echo "</tr>\n\r\n\r";         
 } // end foreach
    
    // end table
   echo "</table>\n\r";
    
}  // end if 
     
else {
     echo "<p>Sorry, there were no results</p>";
} // end else


// close the connection
$conn = null;        

?>



</body>
</html>
