CREATE TABLE IF NOT EXISTS `mikrotik_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `port` int(11) NOT NULL DEFAULT 8728,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `resellers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pppoe_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `service` varchar(255) DEFAULT 'pppoe',
  `profile` varchar(255) DEFAULT NULL,
  `remote_address` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS hotspot_profile_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_name VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    selling_price DECIMAL(10,2) DEFAULT 0,
    validity VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `billing_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_code` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `reseller_id` int(11) DEFAULT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `date_sold` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `customer_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT 'HOTSPOT',
  `name` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `bill_amount` decimal(15,2) DEFAULT 0.00,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `password` varchar(255) DEFAULT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_ip` varchar(50) DEFAULT NULL,
  `wan_ip` varchar(50) DEFAULT NULL,
  `device_username` varchar(255) DEFAULT NULL,
  `device_password` varchar(255) DEFAULT NULL,
  `coordinates` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
