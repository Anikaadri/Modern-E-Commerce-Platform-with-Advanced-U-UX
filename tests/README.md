# Online Shop - Test Suite

Unit and integration tests for the e-commerce platform.

## Running Tests

```bash
composer test
```

## Test Categories

- Unit Tests: Individual component testing
- Integration Tests: Component interaction testing
- Functional Tests: Full workflow testing

## Test Files

- `ProductTest.php` - Product model and controller tests
- `UserTest.php` - User authentication tests
- `CartTest.php` - Shopping cart functionality tests
- `OrderTest.php` - Order processing tests

## Coverage

Run code coverage report:

```bash
phpunit --coverage-html coverage/
```

## Writing Tests

All tests should follow the PHPUnit framework standards and include:

1. Test setup
2. Test execution
3. Assertion validation
4. Cleanup
