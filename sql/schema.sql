CREATE DATABASE IF NOT EXISTS visit_albay
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE visit_albay;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS cancellation_requests;
DROP TABLE IF EXISTS edit_requests;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS hotel_destinations;
DROP TABLE IF EXISTS hotels;
DROP TABLE IF EXISTS destinations;
DROP TABLE IF EXISTS peak_seasons;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(80) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE peak_seasons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  surcharge_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

CREATE TABLE destinations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  town VARCHAR(120) NOT NULL,
  category VARCHAR(60) NOT NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  image_url VARCHAR(500) NOT NULL,
  tagline VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  duration VARCHAR(80) NOT NULL,
  vibe VARCHAR(120) NOT NULL,
  best_for JSON NOT NULL,
  highlights JSON NOT NULL,
  activities JSON NOT NULL,
  significance TEXT NOT NULL,
  map_x VARCHAR(10) DEFAULT NULL,
  map_y VARCHAR(10) DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE hotels (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(60) NOT NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  accommodation_type VARCHAR(80) NOT NULL,
  area VARCHAR(150) NOT NULL,
  rating DECIMAL(2,1) NOT NULL DEFAULT 0.0,
  reviews VARCHAR(40) NOT NULL,
  price_per_night DECIMAL(10,2) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE hotel_destinations (
  hotel_id INT UNSIGNED NOT NULL,
  destination_id INT UNSIGNED NOT NULL,
  distance_km DECIMAL(5,1) NOT NULL DEFAULT 12.0,
  PRIMARY KEY (hotel_id, destination_id),
  CONSTRAINT fk_hotel_destinations_hotel
    FOREIGN KEY (hotel_id) REFERENCES hotels(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_hotel_destinations_destination
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reference_code VARCHAR(20) NOT NULL UNIQUE,
  user_id INT UNSIGNED NOT NULL,
  destination_id INT UNSIGNED NOT NULL,
  hotel_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(160) NOT NULL,
  email VARCHAR(180) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address VARCHAR(255) DEFAULT NULL,
  check_in_date DATE NOT NULL,
  check_out_date DATE NOT NULL,
  guests SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  nights SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  rooms SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  hotel_price DECIMAL(10,2) NOT NULL,
  hotel_total DECIMAL(10,2) NOT NULL,
  payment_method ENUM('GCash', 'Credit / Debit Card', 'Cash on Arrival') NOT NULL,
  payment_details JSON NOT NULL,
  special_request TEXT DEFAULT NULL,
  status ENUM('active', 'cancelled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_bookings_destination
    FOREIGN KEY (destination_id) REFERENCES destinations(id),
  CONSTRAINT fk_bookings_hotel
    FOREIGN KEY (hotel_id) REFERENCES hotels(id)
) ENGINE=InnoDB;

CREATE TABLE edit_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL,
  proposed JSON NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  seen TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_status (status),
  CONSTRAINT fk_edit_requests_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE cancellation_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id INT UNSIGNED NOT NULL,
  reason TEXT NOT NULL,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  seen TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  resolved_at TIMESTAMP NULL DEFAULT NULL,
  INDEX idx_status (status),
  CONSTRAINT fk_cancellation_requests_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO destinations
  (slug, name, town, category, rating, image_url, tagline, description, duration, vibe, best_for, highlights, activities, significance, map_x, map_y)
VALUES
  ('mayon', 'Mayon Volcano', 'Daraga / Legazpi', 'Nature', 5.0,
   'https://commons.wikimedia.org/wiki/Special:FilePath/Majestic_Beauty_of_Mayon_Volcano.jpg?width=1800',
   'The world''s most perfect cone.',
   'An active stratovolcano famed for its near-symmetrical cone, the defining icon of Albay.',
   'Half to full day', 'Iconic and awe-inspiring',
   JSON_ARRAY('Photos', 'Adventure', 'First-timers'),
   JSON_ARRAY('The famous symmetrical cone seen across the province', 'Sunrise and golden-hour views over the fields', 'ATV and lava-trail tours around the foothills'),
   JSON_ARRAY('Photography', 'ATV rides', 'Guided foothill tours'),
   'Albay''s defining landmark, visible from almost everywhere in the province.', '49%', '46%'),
  ('cagsawa', 'Cagsawa Ruins', 'Daraga', 'Heritage', 4.8,
   'https://commons.wikimedia.org/wiki/Special:FilePath/Cagsawa_ruins.jpg?width=1800',
   'A belfry that survived 1814.',
   'The remains of a Franciscan church buried by the 1814 eruption of Mayon, now one of Albay''s signature views.',
   '1 to 2 hours', 'Reflective and historic',
   JSON_ARRAY('History', 'Photos', 'Families'),
   JSON_ARRAY('The lone bell tower set against the cone', 'On-site park, museum, and souvenir stalls', 'The classic framed photo of tower and volcano'),
   JSON_ARRAY('Photography', 'Heritage walk', 'Souvenir shopping'),
   'Albay''s most recognized heritage view, born of Mayon''s 1814 eruption.', '42%', '60%'),
  ('lignon', 'Lignon Hill', 'Legazpi', 'Adventure', 4.6,
   'https://commons.wikimedia.org/wiki/Special:FilePath/Lig%C3%B1on%20Hill%20view%20from%20Daraga%20San%20Roque%20%28Daraga%2C%20Albay%3B%2004-17-2023%29.jpg?width=1800',
   '360-degree views and a zipline.',
   'A short hike or drive up gives panoramic views of Mayon, Legazpi City, and Albay Gulf.',
   '1 to 2 hours', 'Active and panoramic',
   JSON_ARRAY('Adventure', 'Views', 'Photos'),
   JSON_ARRAY('360-degree summit viewdeck', 'Zipline and hanging bridge', 'Short hike or easy drive to the top'),
   JSON_ARRAY('Zipline', 'Hiking', 'Sightseeing'),
   'A viewpoint hill above Legazpi overlooking Albay Gulf and Mayon.', '64%', '62%'),
  ('daraga', 'Daraga Church', 'Daraga', 'Heritage', 4.7,
   'https://commons.wikimedia.org/wiki/Special:FilePath/The_Daraga_Church_in_Albay_Province.jpg?width=1800',
   'Baroque on a hilltop.',
   'Nuestra Senora de la Porteria is a volcanic-stone Baroque church built in 1773 above Daraga.',
   'About 1 hour', 'Serene and historic',
   JSON_ARRAY('History', 'Photos', 'Culture'),
   JSON_ARRAY('Carved volcanic-stone facade', 'Hilltop views of the town and Mayon', 'A quiet, photogenic heritage setting'),
   JSON_ARRAY('Photography', 'Heritage visit', 'Quiet reflection'),
   'A Baroque church of volcanic stone built in 1773, above Daraga town.', '44%', '63%'),
  ('sumlang', 'Sumlang Lake', 'Camalig', 'Nature', 4.5,
   'https://commons.wikimedia.org/wiki/Special:FilePath/Sumlang%20Lake%20%28Camalig%2C%20Albay%3B%2004-20-2023%29.jpg?width=1800',
   'Bamboo rafts under Mayon.',
   'A serene lake offering bamboo-raft rides with clear reflections of Mayon Volcano.',
   '1 to 2 hours', 'Calm and scenic',
   JSON_ARRAY('Relaxation', 'Families', 'Photos'),
   JSON_ARRAY('Bamboo-raft rides across still water', 'Clear Mayon reflections on calm days', 'Lakeside gardens and cafes'),
   JSON_ARRAY('Bamboo rafting', 'Photography', 'Relaxing by the lake'),
   'A community-managed lake known for clear reflections of Mayon.', '38%', '52%'),
  ('quitinday', 'Quitinday Green Hills', 'Camalig / Jovellar', 'Adventure', 4.4,
   'https://commons.wikimedia.org/wiki/Special:FilePath/Quitinday%20Green%20Hills%20south%20view%20%28Camalig%2C%20Albay%3B%2004-22-2023%29.jpg?width=1800',
   'Albay''s rolling green hills.',
   'Soft rolling limestone hills often called the Chocolate Hills of Albay, with nearby caves and an underground river.',
   'Half day', 'Off-the-beaten-path',
   JSON_ARRAY('Adventure', 'Nature', 'Photos'),
   JSON_ARRAY('Rolling green-hill viewpoint', 'Nearby underground river and caves', 'A quieter, less-crowded side of Albay'),
   JSON_ARRAY('Hiking', 'Photography', 'Cave exploring with a guide'),
   'Rolling limestone hills near an underground river and caves.', '26%', '56%');

-- Destination photos are bundled locally (see assets/img/destinations/).
UPDATE destinations SET image_url = CONCAT('assets/img/destinations/', slug, '.jpg');

INSERT INTO hotels
  (slug, name, accommodation_type, area, rating, reviews, price_per_night, image_path)
VALUES
  ('oriental', 'The Oriental Legazpi', 'Hotel / 5-star', 'Taysan, Legazpi / Mayon view', 4.5, '2,000+', 8200.00, 'assets/img/hotels/oriental-legazpi.png'),
  ('marison', 'The Marison Hotel', 'Hotel / 4-star', 'Legazpi / spa', 4.5, '900+', 3900.00, 'assets/img/hotels/marison.jpeg'),
  ('venezia', 'Hotel Venezia', 'Hotel / 3-star', 'Legazpi / near airport', 4.2, '1,100+', 2700.00, 'assets/img/hotels/venezia.jpg'),
  ('pepperland', 'The Pepperland Hotel', 'Hotel / 3-star', 'Legazpi', 4.0, '600+', 2400.00, 'assets/img/hotels/pepperland.jpg'),
  ('st-ellis', 'Hotel St. Ellis', 'Hotel / 3-star', 'Legazpi', 4.1, '700+', 2200.00, 'assets/img/hotels/st-ellis.jpg'),
  ('daraga-casa', 'Daraga Mayon-View Casa', 'Airbnb / whole place', 'Daraga / sleeps 4', 4.8, '150+', 2100.00, 'assets/img/hotels/daraga-casa.jpg'),
  ('vals-farm', 'Vals Farm Guesthouse', 'Guesthouse', 'Camalig / near Sumlang', 4.9, '300+', 1900.00, 'assets/img/hotels/vals-farm.jpeg'),
  ('fg-lodge', 'FG Lodge', 'Guesthouse', 'Daraga / rooftop Mayon view', 4.4, '200+', 1400.00, 'assets/img/hotels/fg-lodge.jpeg'),
  ('mayon-lodging', 'Mayon Lodging House', 'Budget inn', 'Daraga', 4.0, '250+', 1200.00, 'assets/img/hotels/mayon-lodging.jpeg');

INSERT INTO hotel_destinations (hotel_id, destination_id)
SELECT h.id, d.id
FROM hotels h
JOIN destinations d
WHERE h.slug IN ('oriental', 'marison', 'venezia', 'pepperland', 'st-ellis');

INSERT INTO hotel_destinations (hotel_id, destination_id)
SELECT h.id, d.id
FROM hotels h
JOIN destinations d ON d.slug IN ('mayon', 'cagsawa', 'daraga')
WHERE h.slug IN ('daraga-casa', 'fg-lodge', 'mayon-lodging');

INSERT INTO hotel_destinations (hotel_id, destination_id)
SELECT h.id, d.id
FROM hotels h
JOIN destinations d ON d.slug IN ('cagsawa', 'sumlang', 'quitinday')
WHERE h.slug = 'vals-farm';

-- Approximate distance (km) from each hotel to each destination, so the stay
-- picker can list the nearest places to the chosen destination first.
UPDATE hotel_destinations hd
JOIN hotels h ON h.id = hd.hotel_id
JOIN destinations d ON d.id = hd.destination_id
SET hd.distance_km = CASE
  WHEN h.area LIKE '%Camalig%' AND d.town LIKE '%Camalig%' THEN 3.0
  WHEN h.area LIKE '%Daraga%'  AND d.town LIKE '%Daraga%'  THEN 3.5
  WHEN h.area LIKE '%Legazpi%' AND d.town LIKE '%Legazpi%' THEN 4.0
  WHEN h.area LIKE '%Daraga%'  AND d.town LIKE '%Legazpi%' THEN 8.5
  WHEN h.area LIKE '%Legazpi%' AND d.town LIKE '%Daraga%'  THEN 9.0
  WHEN h.area LIKE '%Camalig%' THEN 16.0
  WHEN d.town LIKE '%Camalig%' THEN 17.0
  ELSE 13.0
END;

-- Peak tourist windows in Albay carry a +15% surcharge on the stay total.
-- Date ranges are explicit (covering 2026-2027) so they are easy to adjust.
INSERT INTO peak_seasons (label, start_date, end_date, surcharge_pct) VALUES
  ('Magayon Festival 2026', '2026-05-01', '2026-05-31', 15.00),
  ('Holy Week 2026',        '2026-03-29', '2026-04-05', 15.00),
  ('Christmas & New Year 2026', '2026-12-15', '2027-01-05', 15.00),
  ('Magayon Festival 2027', '2027-05-01', '2027-05-31', 15.00),
  ('Holy Week 2027',        '2027-03-21', '2027-03-28', 15.00),
  ('Christmas & New Year 2027', '2027-12-15', '2028-01-05', 15.00);