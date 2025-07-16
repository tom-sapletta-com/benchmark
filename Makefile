# Makefile for Linux Benchmark project
# Provides automation for common tasks

# Include environment variables from .env file if it exists
-include .env

# Default target
.PHONY: all
all: help

# Help message
.PHONY: help
help:
	@echo "Linux Benchmark Makefile"
	@echo "========================="
	@echo ""
	@echo "Available targets:"
	@echo "  benchmark     - Run the benchmark script"
	@echo "  clean         - Remove temporary files"
	@echo "  publish       - Upload the latest benchmark result to the server"
	@echo "  docker-build  - Build the Docker image"
	@echo "  docker-run    - Run the benchmark in Docker container"
	@echo "  docker-stop   - Stop Docker containers"
	@echo "  docker-clean  - Remove Docker containers and images"
	@echo "  install-deps  - Install dependencies"
	@echo "  help          - Show this help message"

# Run benchmark
.PHONY: benchmark
benchmark:
	@echo "Running benchmark..."
	./benchmark.sh

# Clean temporary files
.PHONY: clean
clean:
	@echo "Cleaning temporary files..."
	rm -f ./testfile
	find . -name "*.pyc" -delete
	find . -name "__pycache__" -delete

# Publish results to server
.PHONY: publish
publish:
	@echo "Publishing latest benchmark results to server..."
	./publish.sh

# Install dependencies
.PHONY: install-deps
install-deps:
	@echo "Installing dependencies..."
	@if command -v apt-get > /dev/null; then \
		sudo apt-get update && sudo apt-get install -y sysbench glmark2 python3 python3-pip; \
	elif command -v dnf > /dev/null; then \
		sudo dnf install -y sysbench glmark2 python3 python3-pip; \
	elif command -v yum > /dev/null; then \
		sudo yum install -y sysbench glmark2 python3 python3-pip; \
	elif command -v pacman > /dev/null; then \
		sudo pacman -Sy sysbench glmark2 python python-pip; \
	else \
		echo "Unsupported package manager. Please install dependencies manually."; \
		exit 1; \
	fi
	pip3 install --user numpy scikit-learn

# Docker targets
.PHONY: docker-build
docker-build:
	@echo "Building Docker image..."
	docker-compose build

.PHONY: docker-run
docker-run:
	@echo "Running benchmark in Docker container..."
	docker-compose up -d
	@echo "Benchmark web interface available at http://$(DOCKER_DOMAIN):$(DOCKER_PORT) or http://localhost:$(DOCKER_PORT)"

.PHONY: docker-stop
docker-stop:
	@echo "Stopping Docker containers..."
	docker-compose down

.PHONY: docker-clean
docker-clean: docker-stop
	@echo "Removing Docker containers and images..."
	docker-compose down --rmi all --volumes --remove-orphans
