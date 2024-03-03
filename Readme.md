# Laravel Fuzzy Search

This Laravel package provides a `FuzzySearchable` trait for Eloquent models, enabling easy implementation of search functionality with support for concatenated attributes. It's designed to enhance search experiences in Laravel applications by allowing more flexible and user-friendly search queries, including partial matches and concatenated field searches.

## Installation

To install the package via Composer, run the following command:

```bash
composer require soliyer/laravel-fuzzy-search
```
Make sure your Laravel version is compatible with this package by checking the Laravel version requirements in the composer.json.

## Usage

### Basic Usage

To add fuzzy search capabilities to your Eloquent models, simply use the `FuzzySearchable` trait and define the attributes you want to make searchable:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Soliyer\LaravelFuzzySearch\Traits\FuzzySearchable;

class Post extends Model
{
    use FuzzySearchable;

    protected $fillable = ['title', 'body', 'author', 'first_name', 'last_name'];

    // Specify the attributes you want to make searchable
    protected $searchable_attributes = ['title', 'body'];
}
```

You can now perform fuzzy searches on your model like so:

```php
$posts = Post::fuzzySearch('Laravel')->get();
```

This will search the `title` and `body` attributes of the Post model for the term `'Laravel'`, allowing for partial matches and enhancing the search experience.

Searching Concatenated Attributes
If you want to search across concatenated attributes (e.g., first and last name), define your searchable attributes as follows:

```php

protected $searchable_attributes = [
    ['first_name', 'last_name'], // This will concatenate first_name and last_name with a space in between
    'email',
    'company'
];

```

Then, performing a fuzzy search will include these concatenated fields in the search criteria.

## Advanced Usage
The FuzzySearchable trait is flexible; you can further customize search behavior by extending the trait or directly in your model methods.

## Contributing
Contributions are welcome and fully credited. Please feel free to fork the repository, make your changes, and submit a pull request on GitHub.

For major changes, please open an issue first to discuss what you would like to change. Make sure to update tests as appropriate.

## Security
If you discover any security-related issues, please email vafaei39@gmail.com instead of using the public issue tracker.

## Credits
`Soheil Vafaei` - Package Author
Special thanks to all contributors who participated in the development of this package.

## License
The Laravel Fuzzy Search package is open-sourced software licensed under the MIT license.


### Key Points:

- **Fillable Array**: This is just an example. Replace or remove the `$fillable` property based on your actual model requirements.
- **Searchable Attributes**: Adapt the `protected $searchable_attributes` array to match the specific attributes you wish to make searchable in your models.
- **GitHub Links**: Ensure the GitHub URL (`https://github.com/soliyer/laravel-fuzzy-search`) and contributors link are correct and point to your actual repository.
- **License File**: The `[MIT license](LICENSE.md)` link assumes you have a `LICENSE.md` file in your repository. Make sure to include this file, replacing `MIT` with your chosen license if different.


