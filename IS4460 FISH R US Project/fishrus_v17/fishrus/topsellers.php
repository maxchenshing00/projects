<html> 
    <body>
    <header align='center'>
        <title>TOP Sellers</title>
        <body>
        <h1>FISH R US</h1>
        <h2>TOP SELLERS</h2>

<?php // query-mysqli.php
  require_once 'utility/login-cred.php';
  $connection = new mysqli($hn, $un, $pw, $db);

  if ($connection->connect_error) die("Fatal Error");

  $query  = "SELECT p.Product_Name, ol.Quantity, SUM(p.Product_Price) AS Total_Sales
            FROM orders AS o 
            INNER JOIN orderline AS ol ON o.Order_ID = ol.Order_ID 
            INNER JOIN product AS p ON ol.Product_ID = p.Product_ID 
            GROUP BY p.Product_Name, ol.Quantity
            Order By ol.Quantity DESC;";
  $result = $connection->query($query);

  if (!$result) die(mysqli_error($conn));

  $rows = $result->num_rows;

  for ($j = 0 ; $j < $rows ; ++$j)
  {
    $result->data_seek($j);
    echo 'Product Name: '.'<br>'  .htmlspecialchars($result->fetch_assoc()['Product_Name'])  .'<br>';
    $result->data_seek($j);
    echo 'Total Quantity: '.'<br>'   .htmlspecialchars($result->fetch_assoc()['Quantity'])   .'<br>';
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