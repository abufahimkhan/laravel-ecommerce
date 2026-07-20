-- Create database (if not exists)
SELECT 'CREATE DATABASE fast_basket'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'fast_basket')\gexec

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE fast_basket TO app_user;
