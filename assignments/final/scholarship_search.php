<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$dsn      = "mysql:host=localhost;dbname=college_ngo";  
$username = "root";
$password = "";

$msg = "";
$years = [];
$semesters = [];

$year = "";
$semester = "";

//create and check connection
$conn = new PDO($dsn, $username, $password);
try  {
  $conn = new PDO($dsn, $username, $password);
  echo "Connection is successful<br><br>";
  } catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo "An error occurred: $error_message" ;
  }

  //get a list of year for option values
  function getOptions($db_connect, $col, $table) {
    $arr = [];
    $select = "SELECT DISTINCT $table.$col FROM $table";
    $query = $db_connect->prepare($select);
    $query->execute();

    $list = $query->fetchAll();
    foreach($list as $li) {
      $para = $li[$col];
      array_push($arr, $para);
    }
    return $arr;
  }

  $years = getOptions($conn, 'year', 'scholarships');
  $semesters = getOptions($conn, 'semester', 'scholarships');
 
  //display table on first load
  $sql = "SELECT CONCAT(me.fname, ', ', me.lname) AS 'Student Name',
                  sc.organization AS 'Scholarship',
                  sc.amount AS 'Amount',
                  sc.semester AS 'Semester',
                  sc.year AS 'Year' 
          FROM members me 
          JOIN scholarships_students ss 
            ON (ss.student_id = me.student_id)
          JOIN scholarships sc 
            ON (ss.scholarship_id = sc.scholarship_id)
          ORDER BY 'Year' DESC, 'Semester', 'Amount' DESC, me.lname";
  $statement = $conn->prepare($sql);
  $statement->execute();
  $rowcount = $statement->rowCount();

  // display table based on button selection
  if (($_SERVER['REQUEST_METHOD'] == 'POST')) {
    if (isset($_POST['select'])) {
        $semester = $_POST['semester'];
        $year = $_POST['year'];
        $sql = "SELECT CONCAT(me.fname, ', ', me.lname) AS 'Student Name',
                       sc.organization AS 'Scholarship',
                       sc.amount AS 'Amount',
                       sc.semester AS 'Semester',
                       sc.year AS 'Year'
                FROM members me
                JOIN scholarships_students ss 
                  ON (ss.student_id = me.student_id)
                JOIN scholarships sc 
                  ON (ss.scholarship_id = sc.scholarship_id)
                WHERE (Year = $year AND Semester = '$semester')
                ORDER BY Year DESC, 'Semester', 'Amount' DESC, me.lname";
        }
    
    if ((isset($_POST['reset']))) {
      $sql = "SELECT CONCAT(me.fname, ', ', me.lname) AS 'Student Name',
                     sc.organization AS 'Scholarship',
                     sc.amount AS 'Amount',
                     sc.semester AS 'Semester',
                     sc.year AS 'Year' 
              FROM members me 
              JOIN scholarships_students ss 
                ON (ss.student_id = me.student_id)
              JOIN scholarships sc 
                ON (ss.scholarship_id = sc.scholarship_id)
              ORDER BY 'Year' DESC, 'Semester', 'Amount' DESC, me.lname";
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
  <title>Scholarships Search</title>
  <style>
        body {
            font-family: arial, sans-serif;
            font-size: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }   

        .row {
          display: flex;
        }

        button {
          height: 1.6em;
        }

        a {
          text-decoration: none;
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
            width: 30%;
        }

        .red {
          color: red;
        }
    </style>
</head>
<body>

<header>
    <h1><?php echo "$rowcount Scholarships Offered $semester $year"; ?></h1>
 </header>

<main>

<?php
//form

$action = $_SERVER['PHP_SELF'];

echo "<div class='row'>\n";
echo "<form action='$action' method='post'>\n";
echo "<label for='year'>Select a Year:</label>\n";
echo "<select name='year' id='year' required>\n";
echo "<option value=''>Make a Selection</option>\n";

foreach ($years as $year) {
  echo "<option value='$year'>$year</option>\n";
}
echo "</select>\n";

echo "<label for='semester'>Select a Semester:</label>\n";
echo "<select name='semester' id='semester' required>\n";
echo "<option value=''>Make a Selection</option>\n";
echo "<option value='Fall'>Fall</option>\n";
echo "<option value='Spring'>Spring</option>\n";
echo "</select>\n";
echo "<input type='submit' name='select' value='Display Scholarships Results'>\n";
echo "</form>\n";
echo "<button type='button' id='reset'>\n";
echo "<a href='scholarship_search.php'>Display ALL Scholarships Results</a>\n";
echo "</button>\n";
echo "</div>\n";

if ($rowcount != 0){ 
  // header row of table
  echo "<table>\n\r";  
  echo "<tr>\n\r"; 
  echo "<th>Student Name</th>\n\r"; 
  echo "<th>Scholarship</th>\n\r"; 
  echo "<th>Amount</th>\n\r";
  echo "<th>Semester</th>\n\r";
  echo "<th>Year</th>\n\r";
  echo "</tr>\n\r\n\r";

  $rows = $statement->fetchAll();

  foreach($rows as $row) {
    echo "<tr>\n\r";
    echo "<td class='long'>" . $row["Student Name"] . "</td>\n\r";
    echo "<td class='long address'>" . $row["Scholarship"] . "</td>\n\r";
    echo "<td class='center'>" . $row["Amount"] . "</td>\n\r";
    echo "<td class='center'>" . $row["Semester"] . "</td>\n\r";
    echo "<td class='center'>" . $row["Year"] . "</td>\n\r";
    echo "</tr>\n\r\n\r";         
  }
  echo "</table>\n\r"; 
} else {
  $msg = "Sorry, there were no scholarships offered for $semester $year";
  echo $msg;
}
$conn = null;  
?>

</main>
</body>
</html>