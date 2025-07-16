#!/bin/bash
# Script to publish benchmark results to benchmark.sapletta.com
# Usage: ./publish.sh [filename.csv]

# Configuration
SERVER_URL="https://benchmark.sapletta.com/index.php"
RESULTS_DIR="$(pwd)"

# Function to find the latest CSV file
find_latest_csv() {
  find "$RESULTS_DIR" -name "benchmark_results_*.csv" -type f -printf "%T@ %p\n" | sort -nr | head -1 | cut -d' ' -f2-
}

# Function to display error and exit
error_exit() {
  echo "ERROR: $1" >&2
  exit 1
}

# Check if curl is installed
if ! command -v curl &>/dev/null; then
  error_exit "curl is required but not installed. Please install curl and try again."
fi

# Get the CSV file to upload
if [ -n "$1" ]; then
  # Use the provided filename
  CSV_FILE="$1"
  if [ ! -f "$CSV_FILE" ]; then
    error_exit "File not found: $CSV_FILE"
  fi
else
  # Find the latest CSV file
  CSV_FILE=$(find_latest_csv)
  if [ -z "$CSV_FILE" ]; then
    error_exit "No benchmark result files found. Run benchmark.sh first."
  fi
fi

echo "Publishing benchmark results from: $CSV_FILE"

# Upload the file to the server
echo "Uploading to $SERVER_URL..."
RESPONSE=$(curl -s -F "action=upload" -F "benchmark_file=@$CSV_FILE" "$SERVER_URL")

# Check the response
if echo "$RESPONSE" | grep -q "success"; then
  echo "✅ Upload successful!"
  RESULT_URL=$(echo "$RESPONSE" | grep -o 'https://benchmark.sapletta.com/[^"]*')
  echo "View your results at: $RESULT_URL"
else
  error_exit "Upload failed: $RESPONSE"
fi

echo "Done!"
