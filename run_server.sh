#!/bin/bash
echo "Starting DIRECT PHP Server with 100GB Upload Limit..."
echo "Bypassing artisan serve to ensure flags are active."
echo "Press Ctrl+C to stop."

# Run PHP built-in server directly using server.php router
# This guarantees that the -d flags apply to the actual process handling requests
php -d upload_max_filesize=100G \
    -d post_max_size=100G \
    -d max_execution_time=0 \
    -d max_input_time=0 \
    -d memory_limit=512M \
    -S 127.0.0.1:8000 \
    server.php
