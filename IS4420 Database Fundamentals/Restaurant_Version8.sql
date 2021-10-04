# Creating the database and using it.
DROP DATABASE IF EXISTS RestaurantBluePrint;
CREATE DATABASE RestaurantBluePrint;
USE RestaurantBluePrint;

# Creating the tables.
CREATE TABLE `food` (
  `Food_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Food_Name` varchar(45) NOT NULL,
  `Food_Price` decimal(10,2) NOT NULL,
  `Food_Descr` varchar(2500) DEFAULT NULL,
  PRIMARY KEY (`Food_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(45) NOT NULL,
  `Last_Name` varchar(45) NOT NULL,
  `Phone_Number` char(12) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Customer_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `restaurant` (
  `Res_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Res_Name` varchar(45) NOT NULL DEFAULT 'Soy Buena Con Cualquier Cosa',
  `State` char(2) NOT NULL CHECK(State IN ('CA', 'UT', 'NV', 'AZ', 'CO')),
  `City` varchar(45) NOT NULL,
  `Street` varchar(45) NOT NULL,
  `ZIP` char(5) NOT NULL,
  `Phone` char(12) NOT NULL,
  PRIMARY KEY (`Res_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `schedule` (
  `Schedule_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Schedule_Descr` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`Schedule_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `employee` (
  `Emp_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(45) NOT NULL,
  `Last_Name` varchar(45) NOT NULL,
  `Hire_Date` date NOT NULL,
  `Job_Title` varchar(45) NOT NULL,
  `Phone_Number` char(12) NOT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Wage_Type` varchar(45) NOT NULL CHECK(Wage_Type IN ('Hourly','Salary')),
  `Res_ID` int(11) NOT NULL,
  `Schedule_ID` int(11) NOT NULL,
  PRIMARY KEY (`Emp_ID`),
  KEY `Res_ID_idx` (`Res_ID`),
  CONSTRAINT `fk_employee_ResID` FOREIGN KEY (`Res_ID`) REFERENCES `restaurant` (`Res_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_employee_ScheduleID` FOREIGN KEY (`Schedule_ID`) REFERENCES `schedule` (`Schedule_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `hourly` (
  `Emp_ID` int(11) NOT NULL,
  `Hourly_Rate` decimal(5,2) NOT NULL,
  `Total_Hours` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`Emp_ID`),
  CONSTRAINT `fk_hourly_EmpID` FOREIGN KEY (`Emp_ID`) REFERENCES `employee` (`Emp_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `salary` (
  `Emp_ID` int(11) NOT NULL,
  `Annual_Wage` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Emp_ID`),
  CONSTRAINT `fk_salary_EmpID` FOREIGN KEY (`Emp_ID`) REFERENCES `employee` (`Emp_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tables` (
  `Table_Number` int(11) NOT NULL AUTO_INCREMENT,
  `Table_Size` int(11) NOT NULL,
  `Res_ID` int(11) NOT NULL,
  PRIMARY KEY (`Table_Number`,`Res_ID`),
  KEY `Res_ID_idx` (`Res_ID`),
  CONSTRAINT `fk_tables_ResID` FOREIGN KEY (`Res_ID`) REFERENCES `restaurant` (`Res_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `day_of_week` (
  `Schedule_ID` int(11) NOT NULL,
  `Day_Name` varchar(45) NOT NULL,
  `Start_Time` time NOT NULL,
  `End_Time` time NOT NULL,
  PRIMARY KEY (`Schedule_ID`,`Day_Name`),
  CONSTRAINT `fk_day_of_week_ScheduleID` FOREIGN KEY (`Schedule_ID`) REFERENCES `schedule` (`Schedule_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_Date` date NOT NULL,
  `Order_Type` varchar(45) NOT NULL CHECK(Order_Type='Stay' OR Order_Type='Delivery'),
  `Customer_ID` int(11) NOT NULL,
  `Emp_ID` int(11) NOT NULL,
  `Payment_Total` decimal(7,2) DEFAULT 0,
  PRIMARY KEY (`Order_ID`),
  KEY `fk_order_CustomerID_idx` (`Customer_ID`),
  KEY `fk_order_EmpID_idx` (`Emp_ID`),
  CONSTRAINT `fk_order_CustomerID` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_order_EmpID` FOREIGN KEY (`Emp_ID`) REFERENCES `employee` (`Emp_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `stay_in` (
  `Order_ID` int(11) NOT NULL,
  `Reserve_Start_Time` time NOT NULL,
  `Reserve_End_Time` time NOT NULL,
  `Table_Number` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`),
  KEY `fk_stay_in_TableNumber_idx` (`Table_Number`),
  CONSTRAINT `fk_stay_in_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_stay_in_TableNumber` FOREIGN KEY (`Table_Number`) REFERENCES `tables` (`Table_Number`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `delivery` (
  `Order_ID` int(11) NOT NULL,
  `State` char(2) NOT NULL CHECK(State IN ('CA', 'UT', 'NV', 'AZ', 'CO')),
  `City` varchar(45) NOT NULL,
  `Street` varchar(45) NOT NULL,
  `ZIP` char(5) NOT NULL,
  PRIMARY KEY (`Order_ID`),
  CONSTRAINT `fk_delivery_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_food_item` (
  `Order_ID` int(11) NOT NULL,
  `Food_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`,`Food_ID`),
  CONSTRAINT `fk_order_food_item_FoodID` FOREIGN KEY (`Food_ID`) REFERENCES `food` (`Food_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_order_food_item_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Creating a trigger for the attribute Payment_Total in the orders table.
DELIMITER $$
CREATE TRIGGER PaymentTotalCalculate AFTER INSERT ON order_food_item
	FOR EACH ROW
    BEGIN
		UPDATE orders
        SET orders.Payment_Total = orders.Payment_Total + NEW.Quantity * (SELECT food.Food_Price FROM food WHERE food.Food_ID = NEW.Food_ID) 
        WHERE orders.Order_ID = NEW.Order_ID;
    END $$
DELIMITER ;

# Inserting the data into the tables. 
-- Food Table
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('1', 'Tortilla Espanola', '4', 'Directly translating to \"Spanish tortilla,\" tortilla espanola is an omelette consisting of egg and potatoes that is cooked in a skillet with olive oil. It may include onions and features a simple seasoning of salt and pepper. The Spanish tortilla is frequently eaten as a tapa, or appetizer.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('2', 'Empanadas', '1.5', 'Empanadas are pastry shells that are filled with different types of fillings, such as meat, cheese, corn, or vegetables. Empanadas are usually served as a starter, tapa, or part of a main course in various Hispanic countries, including Argentina, Chile, and Spain.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('3', 'Arepa', '3.5', 'Made of precooked corn meal, or arepa flour, arepas are flat, round patties that can be fried, baked, or grilled and come in various sizes.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('4', 'Tajadas', '5', 'Tajadas are fried ripe plantains that can be served as a dessert or as a side with rice and entrees such as pork or fried chicken');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('5', 'Gallo pinto', '8', 'A simple mix of rice and beans, gallo pinto translates to \"spotted rooster\" and likely refers to the speckled contrast of the black beans and the white rice.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('6', 'Tacos', '1', 'Tacos are extremely versatile and can be incorporated into any menu. Chances are you already know that tacos are tortillas folded around filling, so you can see how much potential this dish has to be customized!');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('7', 'Stuffed Peppers', '4', 'Mexican stuffed peppers, or \"chile rellenos\" are usually roasted poblano peppers stuffed with melted cheese (Chihuahua or queso Oaxaca) along with a mixture of pork or red meat, raisins, and other spices.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('8', 'Grilled Corn', '5', 'The corn is shucked and grilled so the kernels just begin to char. It’s then served slathered in a sauce made of mayonnaise, chili powder, and other flavors.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('9', 'Mole', '9', 'Mole is a Mexican chili sauce that is usually served over chicken, seafood, turkey, and other dishes. Though it can take many different forms, it’s always made with a type of fruit, chili peppers, herbs and spices (such as garlic, cumin, coriander, anise, cloves) and sometimes chocolate.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('10', 'Paella', '12', 'This rice-based dish is one of the most popular meals in Spain. It combines white rice, vegetables, beans, meat, and herbs and spices like rosemary and saffron.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('11', 'Ceviche', '4', 'Ceviche is a seafood dish that is popular in Peru, Ecuador, Mexico, and other Pacific coastal regions of Latin America. Consisting of fresh raw fish, citrus juice, chopped onions, salt, and cilantro, ceviche is a refreshing, sweet-savory dish.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('12', 'Flan', '7', 'Flan is a custard with a gelatin-like consistency and may also go by the name \"creme caramel\" or \"pudim\" (Portuguese). Essentially a baked custard, flan is usually made of eggs, sweetened condensed milk, evaporated milk, vanilla extract, and sugar');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('13', 'Arroz con Leche', '8', 'Arroz con leche, translating to \"rice with milk,\" is a creamy pudding with cinnamon, nutmeg, and vanilla. In Colombia, arroz con leche may feature grated coconut, and cloves might be added in Peru.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('14', 'Tres Leches Cake', '9', 'This light dessert is usually a sponge cake that is soaked in three different types of milk: evaporated milk, condensed milk, and heavy cream, yielding a sweet and moist cake.');
INSERT INTO `restaurantblueprint`.`food` (`Food_ID`, `Food_Name`, `Food_Price`, `Food_Descr`) VALUES ('15', 'Churros', '1', 'Churros are long, sweet fried-dough pastries that are normally served with a dipping sauce of chocolate or dulce de leche.');

-- Customer Table
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('1', 'Mike', 'Smith', '324-543-4839', 'Mike@gmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('2', 'Bob', 'Bradly', '643-876-4567', 'Bob@hotmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('3', 'Mark', 'Shade', '568-890-4356', 'Mark@gmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('4', 'Sam', 'Troy', '954-064-0987', 'Sam@comcast.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('5', 'Sara', 'David', '493-695-0934', 'Sara@yahoo.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('6', 'Shannon', 'Hunt', '968-930-2614', 'Shannon@aol.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('7', 'Patrick', 'Star', '473-795-4837', 'Patrick@hotmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('8', 'Kate', 'Jacobs', '843-065-4794', 'Kate@Gmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('9', 'Eli', 'Page', '908-385-0393', 'Eli@yahoo.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('10', 'Alexa', 'Mars', '382-594-9684', 'Alexa@gmail.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('11', 'Ruby', 'White', '689-934-9036', 'Ruby@hotmailcom');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('12', 'Pat', 'Press', '143-965-9384', 'Pat@aol.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('13', 'Pual', 'Balily', '567-789-5468', 'Pual@comcast.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('14', 'Cora', 'Gill', '730-090-9374', 'Cora@gmial.com');
INSERT INTO `restaurantblueprint`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('15', 'Sally', 'Jhonson', '453-389-0695', 'Sally@yahoo.com');

-- Restaurant Table
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('1', 'Soy buena con cualquier cosa', 'UT', 'Salt Lake City', '345 Street', '84105', '555-555-5555');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('2', 'Soy buena con cualquier cosa', 'UT', 'Ogden', '678 Street', '84409', '555-555-6666');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('3', 'Soy buena con cualquier cosa', 'UT', 'St. George', '120 Street', '84791', '555-555-7777');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('16', 'Soy buena con cualquier cosa', 'UT', 'Provo', '95 Street', '84097', '555-555-8888');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('4', 'Soy buena con cualquier cosa', 'CA', 'Los Angeles', '1111 Street', '90001', '555-555-0000');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('5', 'Soy buena con cualquier cosa', 'CA', 'San Diego', '1112 Street', '91911', '555-555-0001');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('6', 'Soy buena con cualquier cosa', 'CA', 'San Francisco', '1113 Street', '94016', '555-555-0002');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('7', 'Soy buena con cualquier cosa', 'CA', 'San Jose', '1114 Street', '94088', '555-555-0003');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('8', 'Soy buena con cualquier cosa', 'CA', 'Fresno', '1115 Street', '93650', '555-555-0004');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('9', 'Soy buena con cualquier cosa', 'NV', 'Las Vegas', '200 Street', '89101', '555-555-0005');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('10', 'Soy buena con cualquier cosa', 'AZ', 'Phoenix', '1116 Street', '85001', '555-555-0006');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('11', 'Soy buena con cualquier cosa', 'AZ', 'Tucson', '1117 Street', '85704', '555-555-0007');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('12', 'Soy buena con cualquier cosa', 'AZ', 'Mesa', '1118 Street', '85706', '555-555-0008');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('13', 'Soy buena con cualquier cosa', 'CO', 'Denver', '1119 Street', '80014', '555-555-0009');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('14', 'Soy buena con cualquier cosa', 'CO', 'Colorado Springs', '1120 Street', '80829', '555-555-0010');
INSERT INTO `restaurantblueprint`.`restaurant` (`Res_ID`, `Res_Name`, `State`, `City`, `Street`, `ZIP`, `Phone`) VALUES ('15', 'Soy buena con cualquier cosa', 'CO', 'Aurora', '1121 Street', '80015', '555-555-0011');

-- Schedule Table
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('1', 'Web Developer, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('2', 'Web Developer, Chris Chan');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('3', 'Software Developer, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('4', 'Cybersecurity, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('5', 'Marketing, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('6', 'Accounting, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('7', 'Food Service, Mon-Thur');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('9', 'Delivery, Fri-Sun');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('8', 'Food Service, Fri-Sun');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('10','Delivery, Mon-Thur');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('11','Server, Mon-Fri');
INSERT INTO `restaurantblueprint`.`schedule` (`Schedule_ID`, `Schedule_Descr`) VALUES ('12','Server, Sat-Sun');

-- Day of Week Table
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('1', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('1', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('1', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('1', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('1', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('2', 'Thursday', '8:00', '12:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('2', 'Friday', '8:00', '12:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('2', 'Saturday', '8:00', '12:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('3', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('3', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('3', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('3', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('3', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('4', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('4', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('4', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('4', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('4', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('5', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('5', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('5', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('5', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('5', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('6', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('6', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('6', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('6', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('6', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('7', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('7', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('7', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('7', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('8', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('8', 'Saturday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('8', 'Sunday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('9', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('9', 'Saturday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('9', 'Sunday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('10', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('10', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('10', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('10', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('11', 'Monday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('11', 'Tuesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('11', 'Wednesday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('11', 'Thursday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('11', 'Friday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('12', 'Saturday', '8:00', '17:00');
INSERT INTO `restaurantblueprint`.`day_of_week` (`Schedule_ID`, `Day_Name`, `Start_Time`, `End_Time`) VALUES ('12', 'Sunday', '8:00', '17:00');

-- Employee Table
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('1', 'Anna', 'Appleton', '2005-01-25', 'Web Developer', '111-111-1111', 'AnnaAppleton@gmail.com', 'Salary', '1', '1');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('2', 'Bart', 'Brighton', '2005-01-25', 'Web Developer', '111-111-1112', 'BartBrighton@gmail.com', 'Salary', '1', '1');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('3', 'Chris', 'Chan', '2007-10-10', 'Web Developer', '111-111-1113', 'ChrisChan@gmail.com', 'Salary', '1', '2');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('4', 'Dexter', 'Dontella', '2007-10-10', 'Software Developer', '111-111-1114', 'DexterDontella@gmail.com', 'Salary', '1', '3');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('5', 'Emily', 'Emmerson', '2015-06-01', 'Software Developer', '111-111-1115', 'EmilyEmmerson@gmail.com', 'Salary', '1', '3');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('6', 'Farquad', 'Farmington', '2015-06-01', 'Software Developer', '111-111-1116', 'FarquadFarmington@gmail.com', 'Salary', '1', '3');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('7', 'George', 'Goodwin', '2016-07-01', 'Cybersecurity', '111-111-1117', 'GeorgeGoodwin@gmail.com', 'Salary', '1', '4');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('8', 'Heather', 'Hurd', '2016-07-01', 'Cybersecurity', '111-111-1118', 'HeatherHurd@gmail.com', 'Salary', '1', '4');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('9', 'Immanuel', 'Irvin', '2018-03-15', 'Cybersecurity', '111-111-1119', 'ImmanuelIrvin@gmail.com', 'Salary', '1', '4');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('10', 'Joshua', 'Johnshon', '2018-03-15', 'Marketing', '111-112-1111', 'JoshuaJohnshon@gmail.com', 'Salary', '1', '5');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('11', 'Kate', 'Kelley', '2018-03-15', 'Marketing', '111-112-1112', 'KateKelley@gmail.com', 'Salary', '1', '5');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('12', 'Luke', 'Larson', '2019-11-03', 'Marketing', '111-112-1113', 'LukeLarson@gmail.com', 'Salary', '1', '5');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('13', 'Mark', 'Miller', '2020-01-01', 'Accounting', '111-112-1114', 'MarkMiller@gmail.com', 'Salary', '1', '6');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('14', 'Noah', 'Nelson', '2020-01-01', 'Accounting', '111-112-1115', 'NoahNelson@gmail.com', 'Salary', '1', '6');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('15', 'Oscar', 'Odling', '2020-01-01', 'Accounting', '111-112-1116', 'OscarOdling@gmail.com', 'Salary', '1', '6');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('16', 'Andy', 'Cortes', '2019-01-13', 'Food Service', '123-123-1234', 'AndyCortes@gmail.com', 'Hourly', '1', '7');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('17', 'Anna', 'Medellin', '2019-01-13', 'Food Service', '321-312-1456', 'AnnaMedellin@gmail.com', 'Hourly', '1', '7');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('18', 'Rey', 'McDonald', '2019-01-13', 'Delivery', '456-174-3463', 'ReyMcDonald@gmail.com', 'Hourly', '1', '9');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('19', 'Jacquelyn', 'Klein', '2015-03-07', 'Food Service', '222-222-2222', 'JacquelynKlein@gmail.com', 'Hourly', '1', '7');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('20', 'Frances', 'Moore', '2015-03-07', 'Food Service', '222-222-2223', 'FrancesMoore@gmail.com', 'Hourly', '1', '8');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('21', 'Dallas', 'Morgan', '2015-03-07', 'Food Service', '222-222-2224', 'DallasMorgan@gmail.com', 'Hourly', '1', '8');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('22', 'Arthur', 'Sherman', '2015-03-07', 'Food Service', '222-222-2225', 'ArthurSherman@gmail.com', 'Hourly', '1', '8');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('23', 'Felicia', 'Hill', '2020-01-01', 'Delivery', '222-222-2226', 'AndyCortes@gmail.com', 'Hourly', '1', '10');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('24', 'Freddie', 'Frank', '2020-01-01', 'Delivery', '222-222-2227', 'AnnaMedellin@gmail.com', 'Hourly', '1', '10');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('25', 'Naomi', 'Gardner', '2020-01-01', 'Server', '222-222-2228', 'ReyMcDonald@gmail.com', 'Hourly', '1', '11');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('26', 'Blanche', 'Daniels', '2020-01-01', 'Server', '222-222-2229', 'BlancheDaniels@gmail.com', 'Hourly', '1', '11');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('27', 'Vicky', 'Pierce', '2020-01-01', 'Server', '222-222-2230', 'VickyPierce@gmail.com', 'Hourly', '1', '11');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('28', 'Thomas', 'Higgins', '2020-01-01', 'Server', '222-222-2231', 'ThomasHiggins@gmail.com', 'Hourly', '1', '12');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('29', 'Ellis', 'Lopez', '2020-01-01', 'Server', '222-222-2232', 'EllisLopez@gmail.com', 'Hourly', '1', '12');
INSERT INTO `restaurantblueprint`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Hire_Date`, `Job_Title`, `Phone_Number`, `Email`, `Wage_Type`, `Res_ID`, `Schedule_ID`) VALUES ('30', 'Edwin', 'Brock', '2020-01-01', 'Delivery', '222-222-2233', 'AndyCortes@gmail.com', 'Hourly', '1', '9');

-- Hourly Table
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('16', '10', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('17', '10', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('18', '12', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('19', '10', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('20', '10', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('21', '10', '2000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('22', '10', '2500');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('23', '12', '2500');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('24', '12', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('25', '10', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('26', '10', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('27', '10', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('28', '10', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('29', '10', '3000');
INSERT INTO `restaurantblueprint`.`hourly` (`Emp_ID`, `Hourly_Rate`, `Total_Hours`) VALUES ('30', '12', '3000');

-- Salary Table
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('1', '50000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('2', '50000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('3', '50000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('4', '60000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('5', '60000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('6', '60000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('7', '52000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('8', '52000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('9', '52000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('10', '40000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('11', '40000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('12', '40000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('13', '42000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('14', '42000');
INSERT INTO `restaurantblueprint`.`salary` (`Emp_ID`, `Annual_Wage`) VALUES ('15', '42000');

-- Tables Table
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('1', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('2', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('3', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('4', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('5', '7', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('6', '7', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('7', '7', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('8', '8', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('9', '8', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('10', '10', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('11', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('12', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('13', '4', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('14', '12', '1');
INSERT INTO `restaurantblueprint`.`tables` (`Table_Number`, `Table_Size`, `Res_ID`) VALUES ('15', '10', '1');

-- Orders Table
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('1', '2021-04-14', 'Stay', '15', '25', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('2', '2021-04-14', 'Delivery', '14', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('3', '2021-04-14', 'Delivery', '13', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('4', '2021-04-13', 'Stay', '12', '25', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('5', '2021-04-13', 'Stay', '1', '25', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('6', '2021-04-13', 'Delivery', '2', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('7', '2021-04-13', 'Delivery', '3', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('8', '2021-04-12', 'Delivery', '4', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('9', '2021-04-12', 'Stay', '5', '26', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('10', '2021-04-12', 'Delivery', '6', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('11', '2021-04-12', 'Delivery', '7', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('12', '2021-04-12', 'Delivery', '8', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('13', '2021-04-12', 'Stay', '9', '26', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('14', '2021-04-12', 'Stay', '10', '27', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('15', '2021-04-12', 'Stay', '11', '27', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('16', '2021-04-11', 'Stay', '10', '28', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('17', '2021-04-11', 'Stay', '11', '29', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('18', '2021-04-11', 'Delivery', '12', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('19', '2021-04-11', 'Delivery', '13', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('20', '2021-04-10', 'Stay', '3', '28', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('21', '2021-04-10', 'Stay', '4', '29', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('22', '2021-04-10', 'Delivery', '5', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('23', '2021-04-10', 'Delivery', '6', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('24', '2021-04-10', 'Delivery', '7', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('25', '2021-04-10', 'Delivery', '13', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('26', '2021-04-09', 'Stay', '1', '25', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('27', '2021-04-09', 'Stay', '12', '27', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('28', '2021-04-09', 'Delivery', '13', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('29', '2021-04-09', 'Delivery', '13', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('30', '2021-04-09', 'Delivery', '14', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('31', '2021-04-09', 'Delivery', '15', '30', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('32', '2021-04-08', 'Stay', '11', '25', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('33', '2021-04-08', 'Stay', '12', '26', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('34', '2021-04-08', 'Delivery', '13', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('35', '2021-04-08', 'Delivery', '13', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('36', '2021-04-08', 'Delivery', '13', '24', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('37', '2021-04-08', 'Delivery', '3', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('38', '2021-04-07', 'Stay', '1', '26', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('39', '2021-04-07', 'Delivery', '13', '23', '0');
INSERT INTO `restaurantblueprint`.`orders` (`Order_ID`, `Order_Date`, `Order_Type`, `Customer_ID`, `Emp_ID`, `Payment_Total`) VALUES ('40', '2021-04-07', 'Delivery', '15', '24', '0');

-- Stay in Table
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('1', '18:00', '19:00', '1');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('4', '18:00', '19:00', '2');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('5', '18:00', '19:00', '3');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('9', '17:00', '18:00', '4');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('13', '17:00', '18:00', '5');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('14', '17:00', '18:00', '6');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('15', '17:00', '18:00', '7');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('16', '15:00', '16:00', '8');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('17', '16:00', '17:00', '9');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('20', '17:00', '18:00', '10');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('21', '15:00', '16:00', '1');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('26', '16:00', '17:00', '2');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('27', '17:00', '18:00', '3');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('32', '15:00', '16:00', '4');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('33', '16:00', '17:00', '5');
INSERT INTO `restaurantblueprint`.`stay_in` (`Order_ID`, `Reserve_Start_Time`, `Reserve_End_Time`, `Table_Number`) VALUES ('38', '17:00', '18:00', '6');

-- Delivery Table
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('2', 'UT', 'Salt Lake City', '123 Street', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('3', 'UT', 'Salt Lake City', 'Main St', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('6', 'UT', 'Salt Lake City', 'Fairmont Ave', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('7', 'UT', 'Salt Lake City', 'S main St', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('8', 'UT', 'Salt Lake City', 'College St', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('10', 'UT', 'Salt Lake City', '4567 S Main', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('11', 'UT', 'Salt Lake City', '657 W St', '84105');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('12', 'UT', 'Salt Lake City', 'Salt Lake City Dr', '84120');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('18', 'UT', 'Salt Lake City', '500 W St', '84120');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('19', 'UT', 'Salt Lake City', '234 Street', '84120');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('22', 'UT', 'Salt Lake City', '345 Street', '84122');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('23', 'UT', 'Salt Lake City', '456 Street', '84122');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('24', 'UT', 'Salt Lake City', '567 Street', '84102');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('25', 'UT', 'Salt Lake City', '678 Street', '84102');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('28', 'UT', 'Salt Lake City', '789 Street', '84102');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('29', 'UT', 'Salt Lake City', '9000 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('30', 'UT', 'Salt Lake City', '9001 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('31', 'UT', 'Salt Lake City', '9002 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('34', 'UT', 'Salt Lake City', '9003 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('35', 'UT', 'Salt Lake City', '9004 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('36', 'UT', 'Salt Lake City', '9005 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('37', 'UT', 'Salt Lake City', '9006 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('39', 'UT', 'Salt Lake City', '9007 Street', '84133');
INSERT INTO `restaurantblueprint`.`delivery` (`Order_ID`, `State`, `City`, `Street`, `ZIP`) VALUES ('40', 'UT', 'Salt Lake City', '9008 Street', '84133');

-- Order Food Item Table
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('1', '1', '5');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('1', '2', '13');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('2', '5', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('3', '15', '5');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('4', '14', '6');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('4', '12', '7');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('4', '11', '9');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('5', '1', '8');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('5', '3', '9');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('5', '4', '10');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('5', '5', '10');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('6', '14', '11');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('6', '3', '12');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('7', '12', '13');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('8', '6', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('9', '7', '5');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('10', '1', '6');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('11', '3', '7');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('12', '8', '8');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('13', '9', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('14', '9', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('15', '4', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('16', '1', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('16', '2', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('16', '3', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('17', '4', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('17', '5', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('17', '6', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('18', '10', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('18', '2', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('18', '12', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('19', '12', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('19', '10', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('19', '15', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('20', '12', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('20', '6', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('20', '7', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('21', '8', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('21', '15', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('22', '5', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('22', '7', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('23', '11', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('23', '3', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('24', '15', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('24', '3', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('25', '7', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('25', '5', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('25', '1', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('25', '6', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('25', '13', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('26', '3', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('26', '8', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('27', '11', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('28', '3', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('29', '6', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('30', '4', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('31', '1', '4');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('31', '7', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('32', '3', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('32', '15', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('32', '10', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('33', '12', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('33', '4', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('33', '5', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('34', '14', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('35', '4', '2');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('36', '2', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('36', '11', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('36', '15', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('36', '3', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('36', '7', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('37', '12', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('38', '13', '3');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('39', '10', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('39', '9', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('40', '1', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('40', '6', '1');
INSERT INTO `restaurantblueprint`.`order_food_item` (`Order_ID`, `Food_ID`, `Quantity`) VALUES ('40', '9', '1');

# Creating the 5 queries.
-- Display a list of all employees with their employee id, name, job title, and phone number working at the restaurant with the restaurant id = 1.
SELECT
	Emp_ID,
    CONCAT(First_Name,' ', Last_Name) AS Emp_Name,
    Job_Title,
    Phone_Number
FROM employee
WHERE Res_ID = 1;

-- Display food items with the food id, name, price, and description.
SELECT *
FROM food;

-- Display the history of orders with the order date, order id, order type, customer name, and employee name starting from 
-- 2021-04-10 up to today.
SELECT
	Order_Date,
    Order_ID,
    Order_Type,
    CONCAT(c.First_Name,' ',c.Last_Name) AS Customer_Name,
    CONCAT(e.First_Name,' ',e.Last_Name) AS Employee_Name
FROM orders o
JOIN customer c
	ON o.Customer_Id = c.Customer_ID
JOIN employee e
	ON o.Emp_Id = e.Emp_ID
WHERE Order_Date >= '2021-04-10'
ORDER BY Order_Date ASC;

-- Display all customer's orders with payment equal to or over $30 with the payment total, order id, order type, order date, customer 
-- name, customer phone, and customer email. Order the result by the highest payment to the lowest payment.
SELECT
	Payment_Total,
    Order_ID,
    Order_Type,
    Order_Date,
    CONCAT(c.First_Name,' ',c.Last_Name) AS Customer_Name,
    COALESCE(c.Phone_Number, 'No Phone Number') AS Phone_Number,
    COALESCE(c.Email, 'No Email') AS Email
FROM orders o
JOIN customer c
	ON o.Customer_ID = c.Customer_ID
WHERE Payment_Total >= 30
ORDER BY Payment_Total DESC;

-- Display all orders with the order id, along with the food id, food name, food price, and quantity of the food item within each order.
SELECT
	o.Order_ID,
    f.Food_ID,
    f.Food_Name,
    f.Food_Price,
    ofi.Quantity
FROM orders o
JOIN order_food_item ofi
	ON o.Order_ID = ofi.Order_ID
JOIN food f
	ON f.Food_ID = ofi.Food_ID
ORDER BY Order_ID;

#Creating the 2 views.
-- Create a view that contains restaurant id, restaurant name, restaurant address, and restaurant phone number.
CREATE VIEW restaurant_info AS
SELECT
	Res_ID,
    Res_Name,
    CONCAT(Street,', ',City,', ',State,' ',ZIP) AS Res_Address,
    Phone
FROM restaurant;

-- Create a view that contains information on order. Include order id, order date, order type, customer id, employee id, customer name,
-- customer phone number, customer email, employee name, food id, food name, food price, and quantity.
CREATE VIEW order_info AS
SELECT
	o.Order_ID,
    o.Order_Date,
    o.Order_Type,
    o.Customer_ID,
    o.Emp_ID,
    o.Payment_Total,
    CONCAT(c.First_Name,' ',c.Last_Name) AS Customer_Name,
    c.Phone_Number,
    c.Email,
    CONCAT(e.First_Name,' ',e.Last_Name) AS Employee_Name,
    f.Food_ID,
    f.Food_Name,
    f.Food_Price,
    ofi.Quantity
FROM orders o
JOIN customer c
	ON o.Customer_ID = c.Customer_ID
JOIN employee e
	ON e.Emp_ID = o.Emp_ID
JOIN order_food_item ofi
	ON ofi.Order_ID = o.Order_ID
JOIN food f
	ON ofi.Food_ID = f.Food_ID;
    
#Creating the 3 stored procedures.
-- Display a list of all resteraunts in a state. Display the restaurant id, restaurant name, resteraunt address, and resteraunt phone number 
-- given a state input.
DELIMITER $$
CREATE PROCEDURE get_resteraunt_by_state(state CHAR(2))
BEGIN
	SELECT
		ri.Res_ID,
        ri.Res_Name,
        ri.Res_Address,
        ri.Phone
    FROM restaurant_info ri
    JOIN restaurant r
		ON ri.Res_ID = r.Res_ID
    WHERE r.State = state;
END $$
DELIMITER ;

-- Display the total monthly sales given the month and year input.
DELIMITER $$
CREATE PROCEDURE get_sales_by_month(the_month INT(2), the_year YEAR)
BEGIN
	SET @StartDate = CONCAT(the_year,'-',the_month,'-',1);
	SET @EndDate = LAST_DAY(@StartDate);

	SELECT
		SUM(Payment_Total) AS Monthy_Sales
	FROM order_info oi
    WHERE oi.Order_Date BETWEEN @StartDate AND @EndDate;
END $$
DELIMITER ;

-- Display the food items by most ordered to least ordered.
DELIMITER $$
CREATE PROCEDURE get_food_popularity()
BEGIN
	SELECT
		f.Food_ID,
        f.Food_Name,
        SUM(ofi.Quantity) AS Num_Times_Ordered
	FROM food f
    JOIN order_food_item ofi
		ON f.Food_ID = ofi.Food_ID
	GROUP BY f.Food_ID
    ORDER BY Num_Times_Ordered DESC;
END $$
DELIMITER ;