USE rihla_db;

-- Insert Tours
INSERT INTO tours (name, description, price, location, image_url) VALUES
('Wadi Shab', 'Hiking and swimming in natural pools', 5.00, 'Al Sharqiyah', 'Wadi-shab-tips.jpg'),
('Nizwa Fort', 'Historical fortress with panoramic views', 3.00, 'Nizwa', 'nizwa-fort.jpg'),
('Sur Dhow Factory', 'Traditional boat-building experience', 2.00, 'Sur', 'dhow.jpg'),
('Jebel Akhdar Tour', 'Mountain adventure with scenic views', 10.00, 'Al Dakhiliyah', 'Jabel-al-akhdar_450_750.jpg'),
('Mutrah Souq', 'Traditional marketplace experience', 0.00, 'Muscat', 'mutrah-souq.jpg');

-- Insert Transportation
INSERT INTO transportation (type, city, cost) VALUES
('Taxi', 'Muscat', 3.00),
('Public Bus', 'Muscat', 1.00),
('Car Rental', 'Muscat Airport', 15.00),
('Taxi', 'Salalah', 10.00),
('Ferry', 'Musandam', 20.00);

-- Insert Markets
INSERT INTO markets (name, city, items) VALUES
('Mutrah Souq', 'Muscat', 'Silver jewelry, incense, scarves'),
('Nizwa Souq', 'Nizwa', 'Pottery, daggers, spices'),
('Salalah Al-Haffa', 'Salalah', 'Frankincense, traditional garments'),
('Sohar Fish Market', 'Sohar', 'Fresh seafood, local produce'),
('Rustaq Souq', 'Al Batinah', 'Dates, honey, handicrafts');

-- Insert User Activities
INSERT INTO user_activities (name, description, price, location, contact_number) VALUES
('Desert Camping', 'Overnight desert experience with Bedouin guides', 50.00, 'Wahiba Sands', '+96891234567'),
('Dolphin Watching', 'Morning dolphin watching tour', 25.00, 'Muscat Coast', '+96892345678'),
('Canyoning Adventure', 'Guided canyoning in Wadi Bani Khalid', 75.00, 'Al Sharqiyah', '+96893456789'),
('Omani Cooking Class', 'Learn traditional Omani recipes', 30.00, 'Nizwa', '+96894567890'),
('Stargazing Tour', 'Desert astronomy experience', 40.00, 'Al Wusta', '+96895678901');