# Storable PHP

Storable PHP makes it easy to store and retrieve some loose values with the same exact syntax as Laravel's config repository. Stored values are saved as a JSON file.

## Getting Started

### Installing

Storable is easy to install into your project. simply install Storable by using composer to pull it in and it's dependencies.

```bash
composer require thavarshan/storable
```

### Basic Usage

```php
use \Storable\Store;

$store = new Store($pathToFile);
$store->set('foo', 'bar');
$store->get('foo'); // should return 'bar'.
```

All values will be saved as JSON in the given file. When there are no values stored, the file will be deleted. You can call the following methods on the `Store`.

## Running the tests

Storable uses PHPUnit for testing. To clone Storable into your local machine and run tests, simple open up your preferred terminal application, navigate into Storable project root directory and run the following command..

```bash
git clone git@github.com:Thavarshan/storable.git
cd storable
composer install
composer test
```

## Contributing

Please read [CONTRIBUTING.md](https://github.com/Thavarshan/storable/blob/49964bea98f3b34ddb6ce59519b14e2885dc7413/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **Thavarshan Thayananthajothy** - *Initial work* - [Thavarshan](https://github.com/Thavarshan)

See also the list of [contributors](https://github.com/Thavarshan/storable/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/Thavarshan/storable/blob/49964bea98f3b34ddb6ce59519b14e2885dc7413/LICENSE.md) file for details
