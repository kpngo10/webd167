<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

$dsn      = "mysql:host=localhost;dbname=college_ngo";  
$username = "root";
$password = "";
$msg = "";
$selection = "All Cities";

//create and check connection
$conn = new PDO($dsn, $username, $password);
try  {
  $conn = new PDO($dsn, $username, $password);
  echo "Connection is successful<br><br>";
  } catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo "An error occurred: $error_message" ;
  }

  //get a list of cities for option values
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
 
  //display table on first load
  $sql = "SELECT lname, fname, address, city, postal_code, phone, email, city
          FROM members
          ORDER BY postal_code, lname";
  $statement = $conn->prepare($sql);
  $statement->execute();
  $rowcount = $statement->rowCount();

  //display table based on button selection
  if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
    if ((isset($_POST['select']))) {
      if ($_POST['city'] != 'none') {
        $selection = $_POST['city'];
        $sql = "SELECT lname, fname, address, city, postal_code, phone, email, city
                FROM members
                WHERE city = '$selection'; 
                ORDER BY postal_code, lname";
      } else {
        $msg = "<p class='red'>Please make a selection</><br>";
      }
    }
    if ((isset($_POST['reset']))) {
      $sql = "SELECT lname, fname, address, city, postal_code, phone, email, city
              FROM members
              ORDER BY postal_code, lname";
    }
    $statement = $conn->prepare($sql);
    $statement->execute();
    $rowcount = $statement->rowCount();
  }
?>
<!DOCTYPE html>
<!-- Khang Ngo -->
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Draft</title>
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

        .red {
          color: red;
        }
    </style>
</head>
<body>
<header>
    <h1>Student Population by City of Residence</h1>
    <h2>Number of Students Living in <?php echo "$selection: $rowcount";?></h2>
 </header>
</body>

<?php
//form
echo $msg;
if ($rowcount2 != 0) {

$action = $_SERVER['PHP_SELF'];

echo "<form action='$action' method='post'>";
echo "<label for='city'>Select a City:</label>";
echo "<select name='city' id='city'>";
echo "<option value='none'>Make a Selection</option>";

foreach ($cities as $city) {
  echo "<option value='$city'>$city</option>";
}

echo "</select>";
echo "<input type='submit' name='select' value='Display Cities'>";
echo "<input type='submit' name='reset' value='Display ALL Cities'>";
echo "</form>";
} else {
  echo "<p>Sorry, there were no results</p>";
}

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

  $rows = $statement->fetchAll();

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
  }
  echo "</table>\n\r"; 
} else {
  echo "<p>Sorry, there were no results</p>";
}
$conn = null;  
?>
</html>