-- Paw Heaven Database Schema - Adoption Platform

-- Users table (CREATE FIRST)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample users (INSERT BEFORE reviews)
INSERT INTO users (full_name, email, phone, password_hash) VALUES
('Sarah Johnson', 'sarah@example.com', '(555) 111-2222', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Michael Chen', 'michael@example.com', '(555) 333-4444', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Shelters table
CREATE TABLE IF NOT EXISTS shelters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO shelters (name, address, phone, email) VALUES
('Happy Tails Shelter', '123 Main St, New York, NY', '(555) 123-4567', 'info@happytails.com'),
('Paws & Claws Rescue', '456 Oak Ave, Los Angeles, CA', '(555) 234-5678', 'adopt@pawsclaws.com');

-- Pet categories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    image_url VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO categories (name, description, image_url) VALUES
('Dogs', 'Loyal canine companions', 'https://images.unsplash.com/photo-1587300003388-59208cc962cb'),
('Cats', 'Independent feline friends', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba'),
('Birds', 'Colorful feathered pets', 'https://images.unsplash.com/photo-1552728089-57bdde30beb3'),
('Rabbits', 'Cute and gentle bunnies', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca');

-- Pets table
CREATE TABLE IF NOT EXISTS pets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    shelter_id INT,
    name VARCHAR(100) NOT NULL,
    category_id INT,
    breed VARCHAR(100),
    age VARCHAR(20),
    gender ENUM('male', 'female') DEFAULT 'male',
    weight DECIMAL(5,2),
    description TEXT,
    image_url VARCHAR(255),
    status ENUM('available', 'pending', 'adopted') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shelter_id) REFERENCES shelters(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Original pets
INSERT INTO pets (shelter_id, name, category_id, breed, age, gender, weight, description, image_url, status) VALUES
(1, 'Max', 1, 'Golden Retriever', '2 years', 'male', 65.00, 'Friendly and energetic Golden Retriever loves to play fetch and go for long walks.', 'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=400&h=300&fit=crop', 'available'),
(2, 'Luna', 2, 'Persian', '1 year', 'female', 8.50, 'Beautiful Persian cat with a calm demeanor and luxurious coat.', 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?w=400&h=300&fit=crop', 'available'),
(1, 'Buddy', 1, 'Labrador', '3 years', 'male', 70.00, 'Gentle giant who loves children and gets along with other pets.', 'https://images.unsplash.com/photo-1477884213360-7e9d7dcc1e48?w=400&h=300&fit=crop', 'available'),
(2, 'Whiskers', 2, 'Tabby', '6 months', 'male', 6.00, 'Playful kitten with lots of energy and a sweet personality.', 'https://images.unsplash.com/photo-1573865526739-10659fec78a5?w=400&h=300&fit=crop', 'available');

-- Additional pets (to avoid duplicates)
INSERT INTO pets (shelter_id, name, category_id, breed, age, gender, weight, description, image_url, status) VALUES
(1, 'Bella', 1, 'German Shepherd', '4 years', 'female', 75.00, 'Intelligent and loyal protector, great with families.', 'https://images.unsplash.com/photo-1601758228041-f3b2795255f1?w=400&h=300&fit=crop', 'available'),
(2, 'Charlie', 1, 'Beagle', '2 years', 'male', 25.00, 'Curious and playful beagle with a great sense of smell.', 'https://images.unsplash.com/photo-1505628346881-b72b27e84530?w=400&h=300&fit=crop', 'available'),
(1, 'Daisy', 1, 'Bulldog', '3 years', 'female', 50.00, 'Sweet and gentle bulldog who loves to cuddle.', 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?w=400&h=300&fit=crop', 'available'),
(2, 'Shadow', 2, 'Siamese', '2 years', 'male', 10.00, 'Elegant Siamese cat with bright blue eyes.', 'https://images.unsplash.com/photo-1513245543132-31f507417b26?w=400&h=300&fit=crop', 'available'),
(1, 'Mittens', 2, 'Calico', '1 year', 'female', 7.50, 'Adorable calico with unique markings and playful personality.', 'https://images.unsplash.com/photo-1495360010541-f48722b34f7d?w=400&h=300&fit=crop', 'available'),
(2, 'Oliver', 2, 'Maine Coon', '5 years', 'male', 18.00, 'Gentle giant Maine Coon, very friendly and fluffy.', 'https://images.unsplash.com/photo-1548247416-ec66f4900b2e?w=400&h=300&fit=crop', 'available'),
(1, 'Polly', 3, 'Parrot', '10 years', 'female', 1.50, 'Colorful parrot who loves to talk and sing.', 'https://images.unsplash.com/photo-1552728089-57bdde30beb3?w=400&h=300&fit=crop', 'available'),
(2, 'Tweety', 3, 'Canary', '2 years', 'male', 0.50, 'Beautiful singer with bright yellow feathers.', 'https://images.unsplash.com/photo-1606567595334-d39972c85dbe?w=400&h=300&fit=crop', 'available'),
(1, 'Thumper', 4, 'Holland Lop', '1 year', 'male', 4.00, 'Fluffy bunny with floppy ears, very gentle.', 'https://images.unsplash.com/photo-1425082661705-1834bfd09dca?w=400&h=300&fit=crop', 'available'),
(2, 'Cinnamon', 4, 'Netherland Dwarf', '6 months', 'female', 2.00, 'Tiny bunny with a big personality.', 'https://images.unsplash.com/photo-1609501676725-7186f017a4b7?w=400&h=300&fit=crop', 'available');

-- Adoption applications
CREATE TABLE IF NOT EXISTS adoption_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    pet_id INT,
    message TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO adoption_applications (user_id, pet_id, message, status) VALUES
(1, 1, 'I have a big backyard and love going for walks. Perfect for Max!', 'completed'),
(2, 2, 'I work from home and can give Luna all the attention she needs.', 'completed');

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    pet_id INT,
    adoption_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    user_name VARCHAR(100),
    pet_name VARCHAR(100),
    user_avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE SET NULL,
    FOREIGN KEY (adoption_id) REFERENCES adoption_applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO reviews (user_id, pet_id, adoption_id, rating, comment, user_name, pet_name, user_avatar) VALUES
(1, 1, 1, 5, 'Amazing experience! Max is the perfect addition to our family. The adoption process was smooth and the team was incredibly helpful!', 'Sarah Johnson', 'Max', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop'),
(2, 2, 2, 5, 'Luna is such a sweet cat. The adoption process was so smooth and the staff really cared about finding the right match!', 'Michael Chen', 'Luna', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop');

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_pet (user_id, pet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
