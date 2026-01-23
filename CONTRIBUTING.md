# Contributing to Swap It

First off, thank you for considering contributing to Swap It! It's people like you that make Swap It such a great tool for the student community.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to support@swapit.com.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

* **Use a clear and descriptive title** for the issue to identify the problem.
* **Describe the exact steps which reproduce the problem** in as many details as possible.
* **Provide specific examples to demonstrate the steps**.
* **Describe the behavior you observed after following the steps** and point out what exactly is the problem with that behavior.
* **Explain which behavior you expected to see instead and why**.
* **Include screenshots and animated GIFs** if possible.

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When you are creating an enhancement suggestion, please include:

* **Use a clear and descriptive title** for the issue to identify the suggestion.
* **Provide a step-by-step description of the suggested enhancement** in as many details as possible.
* **Provide specific examples to demonstrate the steps**.
* **Describe the current behavior** and **explain which behavior you expected to see instead** and why.
* **Explain why this enhancement would be useful** to most Swap It users.

### Pull Requests

* Fill in the required template
* Do not include issue numbers in the PR title
* Follow the Flutter/Dart style guide
* Include screenshots and animated GIFs in your pull request whenever possible
* End all files with a newline
* Avoid platform-dependent code

## Development Setup

### Prerequisites

* Flutter SDK (>=3.0.0)
* Dart SDK (>=3.0.0)
* Android Studio / VS Code
* Git

### Setting Up Your Development Environment

1. Fork the repo
2. Clone your fork:
   ```bash
   git clone https://github.com/your-username/swapit.git
   cd swapit
   ```

3. Add upstream remote:
   ```bash
   git remote add upstream https://github.com/whympxx/swapit.git
   ```

4. Install dependencies:
   ```bash
   flutter pub get
   ```

5. Create a branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```

### Coding Standards

* Follow [Effective Dart](https://dart.dev/guides/language/effective-dart) guidelines
* Use meaningful variable and function names
* Add comments for complex logic
* Keep functions small and focused
* Write self-documenting code
* Use proper indentation (2 spaces)

### Commit Messages

* Use the present tense ("Add feature" not "Added feature")
* Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
* Limit the first line to 72 characters or less
* Reference issues and pull requests liberally after the first line
* Consider starting the commit message with an applicable emoji:
    * 🎨 `:art:` when improving the format/structure of the code
    * 🐛 `:bug:` when fixing a bug
    * ✨ `:sparkles:` when adding a new feature
    * 📝 `:memo:` when writing docs
    * 🚀 `:rocket:` when improving performance
    * ✅ `:white_check_mark:` when adding tests
    * 🔒 `:lock:` when dealing with security

### Testing

* Write unit tests for new features
* Ensure all tests pass before submitting a PR:
  ```bash
  flutter test
  ```
* Add integration tests for critical user flows
* Test on both Android and iOS if possible

### Running the App

```bash
flutter run
```

### Building

**Android:**
```bash
flutter build apk --release
```

**iOS:**
```bash
flutter build ios --release
```

## Project Structure

```
lib/
├── main.dart              # App entry point
├── models/                # Data models
├── providers/             # State management
├── screens/               # UI screens
└── widgets/               # Reusable widgets
```

## Style Guide

### Dart/Flutter

Follow the official [Flutter Style Guide](https://flutter.dev/docs/development/tools/formatting).

Key points:
* Use `lowerCamelCase` for variables, functions, and parameters
* Use `UpperCamelCase` for classes and type names
* Use `lowercase_with_underscores` for library and file names
* Prefer `final` over `var` when possible
* Use `const` constructors when possible
* Format code using `dart format`

### Git Workflow

1. Keep your fork synced:
   ```bash
   git fetch upstream
   git checkout main
   git merge upstream/main
   ```

2. Make your changes in a feature branch
3. Test your changes thoroughly
4. Commit with clear messages
5. Push to your fork
6. Submit a pull request

### Pull Request Process

1. Update the README.md with details of changes if applicable
2. Update the documentation with any new features
3. The PR will be merged once you have the sign-off of at least one maintainer

## Recognition

Contributors will be recognized in our README.md file and release notes.

## Questions?

Feel free to contact the team at support@swapit.com or open an issue for discussion.

---

Thank you for contributing to Swap It! 🎉
