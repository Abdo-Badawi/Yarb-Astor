-- Update the support_content table to add missing columns for FAQ management

-- First check if the table exists
SELECT COUNT(*) FROM information_schema.tables 
WHERE table_schema = 'homestay' 
AND table_name = 'support_content';

-- Add display_order column if it doesn't exist
ALTER TABLE support_content 
ADD COLUMN IF NOT EXISTS display_order INT DEFAULT 0;

-- Add tags column if it doesn't exist
ALTER TABLE support_content 
ADD COLUMN IF NOT EXISTS tags VARCHAR(255) DEFAULT '';

-- Add featured column if it doesn't exist
ALTER TABLE support_content 
ADD COLUMN IF NOT EXISTS featured TINYINT(1) DEFAULT 0;

-- Add user_type column if it doesn't exist
ALTER TABLE support_content 
ADD COLUMN IF NOT EXISTS user_type VARCHAR(50) DEFAULT 'admin';

-- Update the category enum to include more values
ALTER TABLE support_content 
MODIFY COLUMN category ENUM('account', 'safety', 'opportunity', 'other', 'host', 'traveler', 'general', 'payment') DEFAULT NULL;

-- Update the status enum to include more values
ALTER TABLE support_content 
MODIFY COLUMN status ENUM('active', 'archived', 'published', 'draft') DEFAULT 'draft';
