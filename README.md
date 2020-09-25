# An extension to documentation generator

```php
    php artisan vendor:publish --provider="Sankar\ReadmeServiceProvider" --tag="config"
```

Add to your route config

```php
Route::group(['middleware' => ['web']], function () {
    Route::get('docs/{version?}/{page?}', '\Sankar\Http\Controllers\Docs\Controller@index');
});
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.