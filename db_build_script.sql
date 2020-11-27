-- RUN THIS USER CREATION IF IT'S THE FIRST TIME YOU ARE RUNNING THE CODE, OTHERWISE LEAVE COMMENTED OUT.
/*
 CREATE USER 'website'@'localhost' IDENTIFIED BY '3ZqpGsAsmC6U2opZ';
GRANT INSERT, SELECT, UPDATE, DELETE ON *.* TO 'website'@'localhost'; 
*/

-- CHECK DATABASE HAS BEEN CREATED, IF NOT CREATE
CREATE DATABASE IF NOT EXISTS auctionsite;

USE auctionsite;

-- DROP ANY TABLES THAT CURRENTLY EXIST TO REPOPULATE.
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS auction_listing;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS bids;
DROP TABLE IF EXISTS watchlist; 
DROP TABLE IF EXISTS watch_notifications; 

-- TABLE NAMES ARE LOWER CASE PER MYSQL CONVENTIONS WITH SPACE REPLACED WITH _

-- CREATE TABLES
CREATE TABLE users (
    userID INT NOT NULL AUTO_INCREMENT, 
    fName VARCHAR(50) NOT NULL,
    lName VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    addressLine1 VARCHAR(255) NOT NULL,
    addressLine2 VARCHAR(255),
    city VARCHAR(50) NOT NULL,
    postcode CHAR(8) NOT NULL,
    buyer BOOLEAN DEFAULT TRUE,
    seller BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (userID),
    UNIQUE (email)
);


CREATE TABLE auction_listing (
    listingID INT NOT NULL AUTO_INCREMENT,
    sellerUserID INT NOT NULL,
    itemName VARCHAR(255) NOT NULL,
    itemDescription TEXT,
    itemImage VARCHAR(255), -- IMAGES AS EXTRA OPTIONAL FEATURE, SHOULD BE IMAGEURL TO DISPLAY, RATHER THAN STORING WHOLE IMAGE FILE IN DB FOR NOW. 
    startPrice DECIMAL(8,2) NOT NULL DEFAULT 0, -- CHOICE OF DECIMAL, 8 DIGIT PRECISION WITH 2 SCALE, TO RELIABLY STORE MONETRAY VALUE.
    reservePrice DECIMAL(8,2) NOT NULL DEFAULT 0,
    startTime TIMESTAMP NOT NULL DEFAULT NOW(),
    endTime DATETIME NOT NULL,
    categoryID INT NOT NULL,
    PRIMARY KEY (listingID),
    FOREIGN KEY (sellerUserID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    INDEX (endTime,categoryID)
);

CREATE TABLE categories (
    categoryID INT NOT NULL AUTO_INCREMENT,
    deptName VARCHAR(50) NOT NULL,
    subCategoryName VARCHAR(50) NOT NULL,
    PRIMARY KEY (categoryID),
    UNIQUE (subCategoryName)
);

CREATE TABLE bids (
    bidID INT NOT NULL AUTO_INCREMENT,
    userID INT NOT NULL,
    listingID INT NOT NULL,
    bidPrice DECIMAL(8,2) NOT NULL CHECK (bidPrice > 0),	-- ADDING CONSTRAINT
    bidTimestamp TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (bidID),
    FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (listingID) REFERENCES auction_listing(listingID) ON UPDATE CASCADE ON DELETE NO ACTION
);

/* CREATE TABLE watchlist (
    userID INT NOT NULL,
    listingID INT NOT NULL,
    bidID INT NOT NULL,
    message TEXT,
    emailSent BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (userID,listingID,bidID),
    FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (listingID) REFERENCES auction_listing(listingID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (bidID) REFERENCES bids(bidID) ON UPDATE CASCADE ON DELETE CASCADE
); */

CREATE TABLE watchlist (
    watchID INT NOT NULL AUTO_INCREMENT,
    listingID INT NOT NULL,
    userID INT NOT NULL,
    isWatching BOOLEAN NOT NULL,
    PRIMARY KEY (watchID),
    FOREIGN KEY (userID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (listingID) REFERENCES auction_listing(listingID) ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX (listingID), -- NEED TO SEARCH FOR THOSE WATCHING QUICKLY BY LISTING, TO NOTIFY BASED ON LISTING CHANGE.
    INDEX (userID)  -- NEED TO FIND A USER'S WATCHLIST QUICKLY TO DISPLAY TO THEM THEIR NOTIFICATIONS.
);

CREATE TABLE watch_notifications (
    notificationID INT NOT NULL AUTO_INCREMENT,
    watchID INT NOT NULL,
    bidID INT NOT NULL,
    message TEXT,
    emailSent BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (notificationID),
    FOREIGN KEY (watchID) REFERENCES watchlist(watchID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (bidID) REFERENCES bids(bidID) ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE(watchID,bidID) -- ENSURES WE CANNOT GIVE CUSTOMER MORE THAN ONE NOTIFICATION FOR EACH BID UPDATE.
);

ALTER TABLE auction_listing
ADD FOREIGN KEY (categoryID) REFERENCES categories(categoryID) ON UPDATE CASCADE ON DELETE CASCADE;

-- SPECIFYING CATEGORIES


-- ADDING DUMMY DATA

-- should hash these passwords before creating
INSERT INTO auctionsite.users (fName, lName, email, password, addressLine1, addressLine2, city, postcode,
buyer, seller)
VALUES
('Tom', 'Cruise', 'tom.cruise@ourauctionsite.com','Password123','15 Honey Drive',null,'London','NW15JBE',TRUE, TRUE),
('John', 'Doe', 'john.doe@ourauctionsite.com', 'Password123', '8 Street', "John's Avenue", 'London', 'WH547PE', TRUE, TRUE),
('Bob', 'Smith', 'bob.smith@ourauctionsite.com', 'Password123', '6 Street', "Bob's Avenue", 'London', 'GS392JF', TRUE, FALSE);;

INSERT INTO auctionsite.categories (deptName, subCategoryName)
VALUES
('Books', 'Fiction'),
('Books','Nonfiction'),
('Electronics', 'TVs'),
('Electronics', 'Computers'),
('Electronics', 'Mobile Phones'),
('Electronics', 'Video Games'),
('Sport & Leisure', 'Cycling'),
('Sport & Leisure','Golf'),
('Sport & Leisure','Tennis'),
('Sport & Leisure','Art'),
('Electronics', 'Cameras'),
('Electronics', 'Audio'),
('Health', 'Gym'),
('Health', 'Female Beauty'),
('Health', 'Supplements')
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription ,startPrice , reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Lenovo Laptop', 'This is my lenovo laptop I got back in 2018. I love it but now I have just bought a mac so do not need it anymore.'
, 105.99, 150, now(),date_add(now(), interval 7 day),4
FROM users where email = 'tom.cruise@ourauctionsite.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Assassins Creed Valhalla PS5', 'Great Game. Already played it so selling it.'
, 25.49, now(),date_add(now(), interval 10 day),6
FROM users where email = 'tom.cruise@ourauctionsite.com'
;

INSERT INTO auctionsite.auction_listing (sellerUserID, itemName,itemDescription , startPrice, reservePrice,
    startTime , endTime, categoryID)
SELECT userID
, 'Samsung 55 inch TV', 'Samsung TV, amazing condiiton. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur sagittis pretium velit egestas venenatis. 
Donec vel lacinia sem, non tristique neque. Suspendisse pellentesque tempus sodales. Vestibulum rhoncus maximus diam. Aenean ut nisi non felis tristique egestas a eget diam. 
Integer suscipit ac ex vel semper. Donec efficitur blandit elit, id scelerisque quam suscipit eu. Donec sagittis tempor erat, nec posuere nisi efficitur ac. Nulla ac justo fermentum, consequat ipsum ac, condimentum lectus. 
Maecenas ac congue arcu, eget consectetur augue. Phasellus eget tortor risus. Pellentesque feugiat libero accumsan, rhoncus magna eget, commodo ligula. Nunc sed suscipit ex. Nulla finibus interdum lectus, sit amet gravida orci dictum eget.

Sed sed velit sollicitudin, dapibus nibh sit amet, vulputate neque. Morbi in luctus metus. Nullam nec lacus pellentesque, auctor lorem ut, egestas nisi. Nullam iaculis nibh molestie sapien facilisis congue. 
Etiam id felis non erat ultricies ornare vitae nec sapien. Morbi dapibus sollicitudin diam et efficitur. Etiam porttitor risus at mauris gravida mollis. Vestibulum in nulla nec mauris sollicitudin malesuada eleifend nec odio. 
Sed quam ipsum, faucibus id laoreet eget, vehicula sit amet diam. Phasellus ac massa ut mi aliquet fermentum vitae sed velit.'
,210, 375, now(),date_add(now(), interval 8 day),3
FROM users where email = 'john.doe@ourauctionsite.com'
;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 110
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 150.33
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 2, listingID, 234.58
from auction_listing where listingID=1;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 3, listingID, 29.58
from auction_listing where listingID=2;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 1000.99
from auction_listing where listingID=3;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 33
from auction_listing where listingID=2;

INSERT INTO auctionsite.bids (userID, listingID, bidPrice)
SELECT 1, listingID, 250
from auction_listing where listingID=1;