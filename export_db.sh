#!/bin/bash

# Database configuration
DB_NAME="sweettooth"
DB_USER="root"
DB_PASS="root"
DB_HOST="localhost"
DB_PORT="3306"

# Output file configuration
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
OUTPUT_FILE="sweettooth_backup_$TIMESTAMP.sql"

echo "Starting database export..."
echo "Database: $DB_NAME"
echo "Exporting to: $OUTPUT_FILE"

# Export the database
mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$OUTPUT_FILE"

# Check if the export was successful
if [ $? -eq 0 ]; then
    echo "Database exported successfully to $OUTPUT_FILE"
    echo "File size: $(du -h "$OUTPUT_FILE" | cut -f1)"
else
    echo "Error: Database export failed"
    exit 1
fi

echo "Export completed!"