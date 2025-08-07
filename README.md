# Laravel Junie

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dcblogdev/laravel-junie.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/laravel-junie)
[![Total Downloads](https://img.shields.io/packagist/dt/dcblogdev/laravel-junie.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/laravel-junie)
[![License](https://img.shields.io/packagist/l/dcblogdev/laravel-junie.svg?style=flat-square)](https://packagist.org/packages/dcblogdev/laravel-junie)

## Introduction

Laravel Junie is a package that allows you to easily install pre-configured guideline documents for Jetbrains Junie in your Laravel application. These guidelines can help your team maintain consistent coding standards and best practices.

The package provides a simple command-line interface to install various guideline documents, such as general coding standards, API development guidelines, Livewire best practices, and more.

## Requirements

- Laravel 12.0 or higher

## Installation

You can install the package via composer:

```bash
composer require dcblogdev/laravel-junie
```

The package will automatically register its service provider.

## Configuration

To publish the configuration file, run:

```bash
php artisan vendor:publish --tag=config
```

This will create a `junie.php` configuration file in your `config` directory. You can customize the following options:

- `documents`: An array of available guideline documents, each with a name, enabled flag, and path.
- `output_path`: The directory where the guideline documents will be installed (default: `.junie`).

Example configuration:

```php
return [
    'documents' => [
        'general' => [
            'name' => 'General guidelines',
            'enabled' => true,
            'path' => 'general.md',
        ],
        // More documents...
    ],

    'output_path' => '.junie',
];
```

You can enable or disable specific documents by setting the `enabled` flag to `true` or `false`.

You can also add custom documents by setting the `custom` flag to `true`.
When this flag is set, the command will not copy the file to the output path, but it will include it in the index.md if such a file exists in the output path.

## Usage

After installing the package, you can:

1. Install all guideline documents:
   ```bash
   php artisan junie:install --all
   ```

2. Install specific guideline documents:
   ```bash
   # Install specific guidelines
   php artisan junie:install --general --testing
   ```

3. Use the interactive installation:
   ```bash
   php artisan junie:install
   ```
   This will prompt you to select which guidelines you want to install.

## Available Guidelines

The package includes the following guideline documents:

- **General Guidelines**
- **API Guidelines**
- **Livewire Guidelines**
- **Testing Guidelines**
- **Frontend Guidelines**
- **Modular Architecture Guidelines**

## Community

There is a Discord community. https://discord.gg/VYau8hgwrm For quick help, ask questions in the appropriate channel.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Pull Requests

- **Document any change in behaviour** - Make sure the `readme.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0]. Randomly breaking public APIs is not an option.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

## License

The MIT License (MIT). Please see [License File](license.md) for more information.
