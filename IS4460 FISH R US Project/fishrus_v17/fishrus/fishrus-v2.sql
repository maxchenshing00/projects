-- The SQL code used to autogenerate ERD in MySQL Workbench
-- Creating the database and using it.
DROP DATABASE IF EXISTS FishRUs;
CREATE DATABASE FishRUs;
USE FishRUs;

-- Creating the tables.
CREATE TABLE `customer` (
  `Customer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(45) NOT NULL,
  `Last_Name` varchar(45) NOT NULL,
  `Phone_Number` char(12) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Customer_ID`)
);

CREATE TABLE `store` (
  `Store_ID` int(11) NOT NULL AUTO_INCREMENT,
  `State` char(2) NOT NULL,
  `City` varchar(45) NOT NULL,
  `Street` varchar(45) NOT NULL,
  `ZIP` char(5) NOT NULL,
  `Phone` char(12) NOT NULL,
  `Hours` varchar(45), -- dropped down below
  PRIMARY KEY (`Store_ID`)
);

CREATE TABLE `vendor` (
  `Vendor_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Vendor_Name` varchar(45) NOT NULL,
  `Phone_Number` char(12) DEFAULT NULL,
  PRIMARY KEY (`Vendor_ID`)
);

CREATE TABLE `employee` (
  `Emp_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(45) NOT NULL,
  `Last_Name` varchar(45) NOT NULL,
  `Position` varchar(45) NOT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Admin` boolean NOT NULL,
  `Store_ID` int(11) NOT NULL,
  PRIMARY KEY (`Emp_ID`),
  KEY `Store_ID_idx` (`Store_ID`),
  CONSTRAINT `fk_employee_StoreID` FOREIGN KEY (`Store_ID`) REFERENCES `store` (`Store_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- orders table changed below
CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_Date` date NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Emp_ID` int(11) NOT NULL,
  `Store_ID` int(11) NOT NULL,
  `Total_Price` decimal(7,2) DEFAULT 0,
  PRIMARY KEY (`Order_ID`),
  KEY `fk_order_CustomerID_idx` (`Customer_ID`),
  KEY `fk_order_EmpID_idx` (`Emp_ID`),
  KEY `fk_order_StoreID_idx` (`Store_ID`),
  CONSTRAINT `fk_order_CustomerID` FOREIGN KEY (`Customer_ID`) REFERENCES `customer` (`Customer_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_order_EmpID` FOREIGN KEY (`Emp_ID`) REFERENCES `employee` (`Emp_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_order_StoreID` FOREIGN KEY (`Store_ID`) REFERENCES `store` (`Store_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `product` (
  `Product_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Product_Name` varchar(100) NOT NULL,
  `Product_Price` decimal(10,2) NOT NULL,
  `Category` varchar(45) NOT NULL,
  `Vendor_ID` int(11) NOT NULL,
  PRIMARY KEY (`Product_ID`),
  KEY `fk_product_VendorID_idx` (`Vendor_ID`),
  CONSTRAINT `fk_product_VendorID` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `orderline` (
  `Order_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`,`Product_ID`),
  CONSTRAINT `fk_orderline_FoodID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_orderline_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `inventory` (
  `Inventory_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Product_ID` int(11) NOT NULL,
  `Vendor_ID` int(11) NOT NULL,
  `Store_ID` int(11) NOT NULL,
  `Inventory_Date` date NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Inventory_ID`),
  KEY `fk_inventory_ProductID_idx` (`Product_ID`),
  KEY `fk_inventory_VendorID_idx` (`Vendor_ID`),
  KEY `fk_inventory_StoreID_idx` (`Store_ID`),
  CONSTRAINT `fk_inventory_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_VendorID` FOREIGN KEY (`Vendor_ID`) REFERENCES `vendor` (`Vendor_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_StoreID` FOREIGN KEY (`Store_ID`) REFERENCES `store` (`Store_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- -- A trigger which calculates the Total_Price in the orders table.
-- DELIMITER $$
-- CREATE TRIGGER PaymentTotalCalculate AFTER INSERT ON orderline
-- 	FOR EACH ROW
--     BEGIN
-- 		UPDATE orders
--         SET orders.Total_Price = orders.Total_Price + NEW.Quantity * (SELECT product.Product_Price FROM product WHERE product.Product_ID = NEW.Product_ID) 
--         WHERE orders.Order_ID = NEW.Order_ID;
--     END $$
-- DELIMITER ;


-- Inserting the data into the tables.
-- Customer table
INSERT INTO `FishRUS`.`customer` (`Customer_ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`) VALUES ('1', 'John', 'Smith', '111-111-1111', 'john.smith@gmail.com');

-- Store table
INSERT INTO `FishRUS`.`store` (`Store_ID`, `State`, `City`, `Street`, `ZIP`, `Phone`, `Hours`) VALUES ('1', 'UT', 'Salt Lake City', '123 Main Street', '84112', '999-999-1111', '7:00AM-9:00PM');

-- Vendor table
INSERT INTO `FishRUS`.`vendor` (`Vendor_ID`, `Vendor_Name`, `Phone_Number`) VALUES ('1', 'Fish-4-You', '222-222-1111');
INSERT INTO `FishRUS`.`vendor` (`Vendor_ID`, `Vendor_Name`, `Phone_Number`) VALUES ('2', 'Aquaria Supplies', '222-222-2222');

-- Employee table
INSERT INTO `FishRUS`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Position`, `Email`, `Admin`, `Store_ID`) VALUES ('1', 'Adam', 'Jones', 'Manager', 'adam.jones123@gmail.com', True, '1');
INSERT INTO `FishRUS`.`employee` (`Emp_ID`, `First_Name`, `Last_Name`, `Position`, `Email`, `Admin`, `Store_ID`) VALUES ('2', 'Bianca', 'Brown', 'Sales', 'biancebrown95@gmail.com', True, '1');

-- Orders table
INSERT INTO `FishRUS`.`orders` (`Order_ID`, `Order_Date`, `Customer_ID`, `Emp_ID`, `Store_ID`, `Total_Price`) VALUES ('1', '2021-09-01', '1', '2', '1', '0');

-- Product table
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('1', 'Black Moor Goldfish', '8.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('2', 'Blue Tang', '82.49', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('3', 'Blue Spine Unicorn Tang', '569.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('4', 'Calico Ryukin Goldfish', '31.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('5', 'Clown Tang', '74.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('6', 'Fantail Goldfish Calico', '4.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('7', 'Fantail Goldfish Red', '3.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('8', 'Naso Tang', '109.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('9', 'Oranda Goldfish Assorted', '8.49', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('10', 'Powder Brown Tang', '101.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('11', 'Red Cap Oranda Goldfish', '12.99', 'Fish', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('12', 'Sailfin Tang', '99.99', 'Fish', '1');

INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('13', 'Bumblebee Snail', '11.99', 'Invert', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('14', 'Derasa Clam Striped With Blue Rim', '48.99', 'Invert', '1');
                                                                                                         
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('15', 'Hikari Canadian Mysis Shrimp', '6.99', 'Food', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('16', 'Piscine Energetics Mysis and Calanus Twin Pack', '24.99', 'Food', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('17', 'Piscine Energetics PE Calanus Frozen Fish Food', '32.99', 'Food', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('18', "Rod's Food Original Blend Frozen Reef Food", '20.00', 'Food', '2');
                                                                                                         
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('19', 'Amazon Sword Plant', '4.67', 'Plant', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('20', 'Anubias Congensis', '5.94', 'Plant', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('21', 'Brazilian Sword', '5.03', 'Plant', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('22', 'Cardinal Plant', '5.39', 'Plant', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('23', 'Dwarf Sagittaria', '6.29', 'Plant', '1');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('24', 'Myrio Red', '2.72', 'Plant', '1');
                                                                                                         
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('25', 'Cabrisea South Seas Base Rock', '29.99', 'Aquascaping', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('26', 'Cabriseas Liferock', '89.99', 'Aquascaping', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('27', 'Malaysian Driftwood', '12.99', 'Aquascaping', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('28', 'Mopani Driftwood', '7.99', 'Aquascaping', '2');
                                                                                                         
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('29', 'Live Aquaria Beginner Shrimp Aquarium Kit Orbi Black', '289.99', 'Aquarium', '2');
INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES ('30', 'Live Aquaria Beginner Shrimp Aquarium Kit Orbi White', '289.99', 'Aquarium', '2');

-- Orderline table
INSERT INTO `FishRUS`.`orderline` (`Order_ID`, `Product_ID`, `Quantity`) VALUES ('1', '2', '1');

-- Inventory table
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('1', '1', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('2', '2', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('3', '3', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('4', '4', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('5', '5', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('6', '6', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('7', '7', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('8', '8', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('9', '9', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('10', '10', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('11', '11', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('12', '12', '1', '1', '2021-01-01', '3'); 
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('13', '13', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('14', '14', '2', '1', '2021-01-01', '3'); 
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('15', '15', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('16', '16', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('17', '17', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('18', '18', '2', '1', '2021-01-01', '10'); 
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('19', '19', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('20', '20', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('21', '21', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('22', '22', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('23', '23', '1', '1', '2021-01-01', '3');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('24', '24', '1', '1', '2021-01-01', '3'); 
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('25', '25', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('26', '26', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('27', '27', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('28', '28', '2', '1', '2021-01-01', '10'); 
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('29', '29', '2', '1', '2021-01-01', '10');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('30', '30', '2', '1', '2021-01-01', '10'); 


-- Changing store table, updating store data
ALTER TABLE store DROP `Hours`;
ALTER TABLE store ADD `Start_Hour` time; -- example: 8:00
ALTER TABLE store ADD `End_Hour` time; -- example: 17:00

UPDATE store SET `Start_Hour` = '8:00', `End_Hour` = '17:00' WHERE `Store_ID` = 1;

-- Adding Discount table and Genus table, updating product data
CREATE TABLE `discount` (
  `Product_ID` int(11) NOT NULL,
  `Discount` decimal(5,4) NOT NULL,
  PRIMARY KEY (`Product_ID`, `Discount`),
  CONSTRAINT `fk_discount_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE `genus` (
  `Product_ID` int(11) NOT NULL,
  `Genus` varchar(64) NOT NULL,
  PRIMARY KEY (`Product_ID`, `Genus`),
  CONSTRAINT `fk_genus_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

INSERT INTO `FishRUS`.`discount` (`Product_ID`,`Discount`) VALUES ('19', '0.1000');
INSERT INTO `FishRUS`.`discount` (`Product_ID`,`Discount`) VALUES ('6', '0.1000');

INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('1', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('4', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('6', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('7', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('9', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('11', 'Carassius auratus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('2', 'Paracanthurus hepatus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('3', 'Naso unicornus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('8', 'Naso unicornus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('5', 'Acanthurus lineatus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('10', 'Acanthurus japonicus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('12', 'Zebrasoma velifer');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('13', 'Engina sp');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('14', 'Tridacna derasa');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('19', 'Echinodorus amazonicus');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('20', 'Anubias afzelii');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('21', 'Spathiphyllum tasson');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('22', 'Lobelia cardinalis "Dwarf"');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('23', 'Sagittaria subulata');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('24', 'Myriophyllum heterophyllum');

INSERT INTO `FishRUS`.`product` (`Product_ID`, `Product_Name`, `Product_Price`, `Category`, `Vendor_ID`) VALUES (null, 'Magikarp', '0.01', 'Fish', '1');
INSERT INTO `FishRUS`.`genus` (`Product_ID`,`Genus`) VALUES ('31', 'Magikarp');
INSERT INTO `FishRUS`.`inventory` (`Inventory_ID`, `Product_ID`, `Vendor_ID`, `Store_ID`, `Inventory_Date`, `Quantity`) VALUES ('31', '31', '1', '1', '2021-01-01', '10'); 

-- Adding Username and Password to customer and employee table
ALTER TABLE customer ADD `Username` varchar(100);
ALTER TABLE customer ADD `Password` varchar(100);
ALTER TABLE employee ADD `Username` varchar(100);
ALTER TABLE employee ADD `Password` varchar(100);

UPDATE customer SET `Username` = 'john123', `Password` = 'password' WHERE `Customer_ID` = 1;
UPDATE employee SET `Username` = 'adam123', `Password` = 'password' WHERE `Emp_ID` = 1;
UPDATE employee SET `Username` = 'bianca123', `Password` = 'password' WHERE `Emp_ID` = 2;

-- For login.php
-- Creating 'user' table, creating 'admin' table, dropping 'customer' table, dropping 'employee' table
CREATE TABLE `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT, -- need to change to User_ID, will need to change login.php, login-confirm.php, all display pages
  `First_Name` varchar(45) NOT NULL,
  `Last_Name` varchar(45) NOT NULL,
  `Phone_Number` char(12) DEFAULT NULL,
  `Email` varchar(60) NOT NULL,
  `State` char(2) DEFAULT NULL,
  `City` varchar(45) DEFAULT NULL,
  `Street` varchar(45) DEFAULT NULL,
  `ZIP` char(5) DEFAULT NULL,
  `Username` varchar(45) NOT NULL,
  `Password` varchar(150) NOT NULL,
  PRIMARY KEY (`ID`)
);

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Position` varchar(45) NOT NULL,
  `Store_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Store_ID_idx` (`Store_ID`),
  CONSTRAINT `fk_admin_ID` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_admin_StoreID` FOREIGN KEY (`Store_ID`) REFERENCES `store` (`Store_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

DROP TABLE orderline; -- added to MySQL
DROP TABLE orders; -- added to MySQL
DROP TABLE customer;
DROP TABLE employee;

INSERT INTO `FishRUS`.`user` (`ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`, `State`, `City`, `Street`, `ZIP`, `Username`, `Password`) VALUES ('1', 'John', 'Smith', '111-111-1111', 'john.smith@gmail.com', 'UT', 'Salt Lake City', '123 Main Street', '84112', 'john123', 'pass');
INSERT INTO `FishRUS`.`user` (`ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`, `Username`, `Password`) VALUES ('2', 'Adam', 'Jones', '555-111-1111', 'adam.jones123@gmail.com', 'adam123', 'pass');
INSERT INTO `FishRUS`.`user` (`ID`, `First_Name`, `Last_Name`, `Phone_Number`, `Email`, `Username`, `Password`) VALUES ('3', 'Bianca', 'Brown', '555-111-1112', 'biancebrown95@gmail.com', 'bianca123', 'pass');
INSERT INTO `FishRUS`.`admin` (`ID`, `Position`, `Store_ID`) VALUES ('2', 'Manager', '1');
INSERT INTO `FishRUS`.`admin` (`ID`, `Position`, `Store_ID`) VALUES ('3', 'Sales', '1');

-- For cart.php
-- Creating cart_items table
CREATE TABLE `cart_item` (
  `Cart_Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Cart_Item_ID`),
  KEY `fk_cartitem_ID_idx` (`ID`),
  KEY `fk_cartitem_ProductID_idx` (`Product_ID`),
  CONSTRAINT `fk_cartitem_ID` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_cartitem_ProductID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- For checkout.php
-- Creating credit_card table, dropping and recreating order table
CREATE TABLE `credit_card` (
  `ID` int(11) NOT NULL,
  `Card_Number` varchar(100) NOT NULL, -- 16 digits, but hashed
  `Card_Type` varchar(45) NOT NULL, -- Visa, MasterCard, American Express
  `Expiration` DATETIME NOT NULL, -- INSERT INTO credit_card (Expiration) Values (Convert(DateTime,'20250626',112))
  `Security_Code` varchar(70) NOT NULL, -- 3 or 4 digits, but hashed
  PRIMARY KEY (`Card_Number`),
  KEY `fk_creditcard_ID_idx` (`ID`),
  CONSTRAINT `fk_creditcard_ID` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- DROP TABLE `orders`;

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Order_Date` date NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Credit_Card_Num` varchar(100) NOT NULL,
  `Credit_Card_End` varchar(10) NOT NULL,
  `Card_Type` varchar(45) NOT NULL,
  `Total_Price` decimal(7,2) DEFAULT 0,
  PRIMARY KEY (`Order_ID`),
  KEY `fk_order_CustomerID_idx` (`Customer_ID`),
  -- KEY `fk_order_CreditCardNum_idx` (`Credit_Card_Num`),
  CONSTRAINT `fk_order_CustomerID` FOREIGN KEY (`Customer_ID`) REFERENCES `user` (`ID`) ON DELETE NO ACTION ON UPDATE CASCADE
  -- CONSTRAINT `fk_order_CreditCardNum` FOREIGN KEY (`Credit_Card_Num`) REFERENCES `credit_card` (`Card_Number`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- added to MySQL
CREATE TABLE `orderline` (
  `Order_ID` int(11) NOT NULL,
  `Product_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Order_ID`,`Product_ID`),
  CONSTRAINT `fk_orderline_FoodID` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_orderline_OrderID` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE NO ACTION ON UPDATE CASCADE
);

-- A trigger which calculates the Total_Price in the orders table.
DELIMITER $$
CREATE TRIGGER PaymentTotalCalculate AFTER INSERT ON orderline
	FOR EACH ROW
    BEGIN
		UPDATE orders
        SET orders.Total_Price = orders.Total_Price + NEW.Quantity * (SELECT product.Product_Price FROM product WHERE product.Product_ID = NEW.Product_ID) 
        WHERE orders.Order_ID = NEW.Order_ID;
    END $$
DELIMITER ;