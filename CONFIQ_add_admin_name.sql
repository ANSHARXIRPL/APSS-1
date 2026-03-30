-- Add nama column to admin table
ALTER TABLE admin ADD COLUMN nama VARCHAR(100) DEFAULT 'Admin' AFTER username;

-- Add admin_name column to aspirasi table to track which admin handled it
ALTER TABLE aspirasi ADD COLUMN admin_name VARCHAR(100) DEFAULT NULL AFTER feedback;
