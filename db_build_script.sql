--CHECK DATABASE HAS BEEN CREATED, IF NOT CREATE
CREATE DATABASE IF NOT EXISTS auctionsite;

USE auctionsite;

-- DROP ANY TABLES THAT CURRENTLY EXIST TO REPOPULATE.
DROP TABLE users;
DROP TABLE auction_listing;
DROP TABLE categories;
DROP TABLE bids;
DROP TABLE watchlist;

-- TABLE NAMES ARE LOWER CASE PER MYSQL CONVENTIONS WITH SPACE REPLACED WITH _

-- CREATE TABLES
CREATE TABLE users (
    userID INT NOT NULL AUTO_INCREMENT, 
    fName VARCHAR(50) NOT NULL,
    lName VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(50) NOT NULL,
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
    itemImage MEDIUMBLOB, 
    startPrice DECIMAL(8,2) NOT NULL DEFAULT 0, -- CHOICE OF DECIMAL, 8 DIGIT PRECISION WITH 2 SCALE, TO RELIABLY STORE MONETRAY VALUE.
    reservePrice DECIMAL(8,2) NOT NULL DEFAULT 0,
    startTime TIMESTAMP NOT NULL DEFAULT NOW(),
    endTime TIMESTAMP NOT NULL,
    categoryID NOT NULL,
    PRIMARY KEY (listingID),
    FOREIGN KEY (sellerUserID) REFERENCES users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    INDEX (endTime,categoryID)
);

CREATE TABLE categories (
    categoryID INT NOT NULL AUTO_INCREMENT,
    deptName VARCHAR(50) NOT NULL,
    subCategoryName VARCHAR(50) NOT NULL,
    PRIMARY KEY (categoryID)
);

CREATE TABLE bids (
    bidID INT NOT NULL AUTO_INCREMENT,
    userID INT NOT NULL,
    listingID INT NOT NULL,
    bidPrice DECIMAL(8,2) NOT NULL,
    bidTimestamp TIMESTAMP NOT NULL DEFAULT NOW(),
    PRIMARY KEY (bidID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON UPDATE CASCADE ON DELETE NO ACTION,
    FOREIGN KEY (listingID) REFERENCES AuctionListing(listingID) ON UPDATE CASCADE ON DELETE NO ACTION,
    CONSTRAINT CHK_BID CHECK (bidPrice > 0)
);

CREATE TABLE watchlist (
    userID INT NOT NULL,
    listingID INT NOT NULL,
    bidID INT NOT NULL,
    message TEXT,
    emailSent BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (userID,listingID,bidID),
    FOREIGN KEY (userID) REFERENCES Users(userID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (listingID) REFERENCES AuctionListing(listingID) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (bidID) REFERENCES Bids(bidID) ON UPDATE CASCADE ON DELETE CASCADE,
);

ALTER TABLE auction_listing
ADD FOREIGN KEY (categoryID) REFERENCES categories(categoryID) ON UPDATE CASCADE ON DELETE CASCADE;