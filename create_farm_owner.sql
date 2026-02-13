-- Create test farm owner user
INSERT INTO users (name, email, password, phone, role, status, email_verified_at, created_at, updated_at) 
VALUES ('John Farmer', 'john@farm.com', '$2y$12$BmVT7l6Y1WdJf2v4H5jHweiI1B0XxLSgGxqBz5xR5h8W5v5K5z6Li', '09123456789', 'farm_owner', 'active', now(), now(), now());

-- Create farm owner profile
INSERT INTO farm_owners (user_id, farm_name, farm_address, city, province, postal_code, latitude, longitude, business_registration_number, permit_status, created_at, updated_at)
VALUES (4, 'Sunny Farm', '123 Farm Road', 'Manila', 'Metro Manila', '1000', 14.5995, 120.9842, 'BRN-2026-001', 'approved', now(), now());
