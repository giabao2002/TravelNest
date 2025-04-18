-- SQL script to remove the main_image column from the tours table
USE travel_nest;

-- Drop the main_image column
ALTER TABLE tours DROP COLUMN main_image; 