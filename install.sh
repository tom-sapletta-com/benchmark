#!/bin/bash
# One-liner installer for benchmark tool
# Usage: curl -s https://raw.githubusercontent.com/tom-sapletta-com/benchmark/main/install.sh | bash

# Clone or update the repository
if [ -d "benchmark" ]; then
  echo "Updating existing benchmark repository..."
  cd benchmark
  git pull
else
  echo "Downloading benchmark repository..."
  git clone https://github.com/tom-sapletta-com/benchmark.git
  cd benchmark
fi

# Make scripts executable
chmod +x benchmark.sh
if [ -f "publish.sh" ]; then
  chmod +x publish.sh
fi

echo "Benchmark tool installed successfully!"
echo "Run ./benchmark.sh to start benchmarking your system."
