# Changelog

All notable changes to the Linux Benchmark project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-07-16

### Added
- Initial release of the Linux Benchmark tool
- CPU benchmark using sysbench
- RAM benchmark using sysbench
- Disk benchmark using dd
- GPU benchmark using glmark2
- AI performance benchmark using NumPy and scikit-learn
- CSV output format for benchmark results
- Radar chart visualization in index.html
- Local storage for benchmark history
- Comparison table for multiple benchmark results
- Server-side PHP application for benchmark comparison
- publish.sh script for uploading results to server
- Docker support for local testing
- Configuration via .env file
- Makefile for common operations

### Changed
- Enhanced GPU benchmark to handle various edge cases
- Improved visualization with normalized values
- Better error handling and dependency management

### Fixed
- Fixed GPU benchmark reporting 'N/A' values
- Improved score scaling for better radar chart visualization
- Fixed compatibility issues with various Linux distributions
