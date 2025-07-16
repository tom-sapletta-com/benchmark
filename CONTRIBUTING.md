# Contributing to Linux Benchmark

Thank you for your interest in contributing to the Linux Benchmark project! This document provides guidelines and instructions for contributing.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for everyone.

## How to Contribute

### Reporting Bugs

If you find a bug, please create an issue with the following information:
- A clear, descriptive title
- A detailed description of the issue
- Steps to reproduce the behavior
- Expected behavior
- Screenshots (if applicable)
- Environment information (OS, hardware specs, etc.)

### Suggesting Enhancements

We welcome suggestions for enhancements! Please create an issue with:
- A clear, descriptive title
- A detailed description of the proposed enhancement
- Any relevant examples or mockups
- Explanation of why this enhancement would be useful

### Pull Requests

1. Fork the repository
2. Create a new branch (`git checkout -b feature/your-feature-name`)
3. Make your changes
4. Run tests to ensure your changes don't break existing functionality
5. Commit your changes (`git commit -m 'Add some feature'`)
6. Push to the branch (`git push origin feature/your-feature-name`)
7. Create a new Pull Request

## Development Setup

### Prerequisites

- Bash shell
- Python 3.6+
- Docker and Docker Compose (for containerized testing)

### Local Development

1. Clone the repository:
   ```
   git clone https://github.com/tom-sapletta-com/benchmark.git
   cd benchmark
   ```

2. Install dependencies:
   ```
   pip3 install --user numpy scikit-learn
   ```

3. Run the benchmark:
   ```
   ./benchmark.sh
   ```

4. For Docker development:
   ```
   make docker-build
   make docker-run
   ```

## Testing

Before submitting a pull request, please ensure:
- The benchmark script runs without errors
- All new features are properly documented
- The visualization works correctly with your changes
- Docker setup works if you've modified related files

## Coding Standards

- Shell scripts should follow the [Google Shell Style Guide](https://google.github.io/styleguide/shellguide.html)
- Python code should follow [PEP 8](https://www.python.org/dev/peps/pep-0008/)
- JavaScript code should follow [Airbnb JavaScript Style Guide](https://github.com/airbnb/javascript)
- HTML/CSS should be well-formatted and validated

## License

By contributing to this project, you agree that your contributions will be licensed under the project's license.
