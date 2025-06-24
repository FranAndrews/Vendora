-- Create sample seller
INSERT INTO users (name, email, password, role, status, created_at) 
SELECT 'Tech Store', 'tech@vendora.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'tech@vendora.com');

-- Get seller ID
SET @seller_id = (SELECT id FROM users WHERE email = 'tech@vendora.com' LIMIT 1);

-- Add categories
INSERT INTO categories (name, description) VALUES
('Electronics', 'Latest gadgets and electronic devices'),
('Computers', 'Laptops, desktops, and accessories'),
('Smartphones', 'Mobile phones and accessories'),
('Audio', 'Headphones, speakers, and audio equipment'),
('Gaming', 'Gaming consoles, games, and accessories')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Get category IDs
SET @electronics_id = (SELECT id FROM categories WHERE name = 'Electronics' LIMIT 1);
SET @computers_id = (SELECT id FROM categories WHERE name = 'Computers' LIMIT 1);
SET @smartphones_id = (SELECT id FROM categories WHERE name = 'Smartphones' LIMIT 1);
SET @audio_id = (SELECT id FROM categories WHERE name = 'Audio' LIMIT 1);
SET @gaming_id = (SELECT id FROM categories WHERE name = 'Gaming' LIMIT 1);

-- Add products with Rand prices and real image URLs
INSERT INTO products (seller_id, name, description, price, stock, image_path, status) VALUES
(@seller_id, 'MacBook Pro 14"', 'Apple M2 Pro chip, 16GB RAM, 512GB SSD', 49999.99, 10, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/mbp14-spacegray-select-202301?wid=904&hei=840&fmt=jpeg&qlt=90&.v=1671304673202', 'active'),
(@seller_id, 'iPhone 15 Pro', 'A17 Pro chip, 256GB, Titanium design', 24999.99, 15, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-15-pro-finish-select-202309-6-7inch-naturaltitanium?wid=5120&hei=2880&fmt=p-jpg&qlt=80&.v=1692845702708', 'active'),
(@seller_id, 'Sony WH-1000XM5', 'Wireless noise-cancelling headphones', 9999.99, 20, 'https://www.sony.co.za/image/5d02da5df552836db894a0c2c4c2c0f3?fmt=pjpeg&bgcolor=FFFFFF&bgc=FFFFFF&wid=2515&hei=1320', 'active'),
(@seller_id, 'PlayStation 5', 'Digital Edition, White', 12499.99, 8, 'https://gmedia.playstation.com/is/image/SIEPDC/ps5-product-thumbnail-01-en-14sep21', 'active'),
(@seller_id, 'Samsung 4K Monitor', '32-inch, HDR, 144Hz refresh rate', 12499.99, 12, 'https://images.samsung.com/is/image/samsung/p6pim/za/lc32g85tsslxza/gallery/za-odyssey-g8-lc32g85tsslxza-537686803', 'active'),
(@seller_id, 'AirPods Pro', 'Active noise cancellation, Spatial Audio', 5999.99, 25, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MQD83?wid=1144&hei=1144&fmt=jpeg&qlt=90&.v=1660803972361', 'active'),
(@seller_id, 'iPad Pro 12.9"', 'M2 chip, 256GB, Wi-Fi + Cellular', 32499.99, 10, 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-pro-model-select-gallery-2-202212?wid=5120&hei=2880&fmt=p-jpg&qlt=95&.v=1670887559803', 'active'),
(@seller_id, 'Nintendo Switch OLED', 'White, 64GB', 8999.99, 15, 'https://assets.nintendo.com/image/upload/f_auto/q_auto/dpr_2.0/c_scale,w_400/ncom/en_US/switch/site-design-update/hardware/switch/nintendo-switch-oled-model-white-set/gallery/image01', 'active'),
(@seller_id, 'Samsung Galaxy S23', '256GB, Phantom Black', 22499.99, 18, 'https://images.samsung.com/is/image/samsung/p6pim/za/2308/gallery/za-galaxy-s23-ultra-s918-sm-s918bzgaxfa-thumb-537686803', 'active'),
(@seller_id, 'Logitech MX Master 3S', 'Wireless mouse, Darkfield tracking', 2499.99, 30, 'https://resource.logitech.com/content/dam/logitech/en/products/mice/mx-master-3s/gallery/mx-master-3s-mouse-top-view-graphite.png', 'active')
ON DUPLICATE KEY UPDATE 
    stock = VALUES(stock),
    price = VALUES(price),
    status = VALUES(status);

-- Get product IDs
SET @macbook_id = (SELECT id FROM products WHERE name = 'MacBook Pro 14"' LIMIT 1);
SET @iphone_id = (SELECT id FROM products WHERE name = 'iPhone 15 Pro' LIMIT 1);
SET @sony_id = (SELECT id FROM products WHERE name = 'Sony WH-1000XM5' LIMIT 1);
SET @ps5_id = (SELECT id FROM products WHERE name = 'PlayStation 5' LIMIT 1);
SET @monitor_id = (SELECT id FROM products WHERE name = 'Samsung 4K Monitor' LIMIT 1);
SET @airpods_id = (SELECT id FROM products WHERE name = 'AirPods Pro' LIMIT 1);
SET @ipad_id = (SELECT id FROM products WHERE name = 'iPad Pro 12.9"' LIMIT 1);
SET @switch_id = (SELECT id FROM products WHERE name = 'Nintendo Switch OLED' LIMIT 1);
SET @samsung_id = (SELECT id FROM products WHERE name = 'Samsung Galaxy S23' LIMIT 1);
SET @mouse_id = (SELECT id FROM products WHERE name = 'Logitech MX Master 3S' LIMIT 1);

-- Add product categories
INSERT INTO product_categories (product_id, category_id) VALUES
(@macbook_id, @computers_id),
(@iphone_id, @smartphones_id),
(@sony_id, @audio_id),
(@ps5_id, @gaming_id),
(@monitor_id, @computers_id),
(@airpods_id, @audio_id),
(@ipad_id, @electronics_id),
(@switch_id, @gaming_id),
(@samsung_id, @smartphones_id),
(@mouse_id, @computers_id)
ON DUPLICATE KEY UPDATE product_id = VALUES(product_id); 