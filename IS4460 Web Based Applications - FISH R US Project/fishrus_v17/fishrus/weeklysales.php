<html> 
    <body>
    <header align='center'>
        <title>Weekly Sales</title>
        <body>
        <h1>FISH R US</h1>
        <h2>Weekly Sales</h2>
        

<?php // query-mysqli.php
  require_once 'utility/login-cred.php';
  $connection = new mysqli($hn, $un, $pw, $db);

  if ($connection->connect_error) die("Fatal Error");

  $query  = "SELECT week(o.Order_Date) AS Week, SUM(p.Product_Price) AS Total_Sales 
  FROM orders AS o 
  INNER JOIN orderline AS ol ON o.Order_ID = ol.Order_ID 
  INNER JOIN product AS p ON ol.Product_ID = p.Product_ID 
  GROUP BY o.Order_Date 
  Order By o.Order_Date ASC;";

  $result = $connection->query($query);

  if (!$result) die(mysqli_error($conn));

  $rows = $result->num_rows;

  for ($j = 0 ; $j < $rows ; ++$j)
  {
    $result->data_seek($j);
    echo 'Week: '.'<br>'  .htmlspecialchars($result->fetch_assoc()['Week'])  .'<br>';
    $result->data_seek($j);
    echo 'Total Sales: '.'<br>'   .htmlspecialchars($result->fetch_assoc()['Total_Sales'])   .'<br>'.'<br>';
    $result->data_seek($j);
    
  }

  $result->close();
  $connection->close();
  
?>

  <br>
<input type="button" value="Back" onclick="history.back()">
</body>
</html>