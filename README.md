# An extension to documentation generator

```php
    php artisan vendor:publish --provider="Diviky\Readme\ReadmeServiceProvider" --tag="config"
```

Add to your route config

```php
Route::group(['middleware' => ['web']], function () {
    Route::get('docs/{version?}/{page?}', '\Diviky\Readme\Http\Controllers\Docs\Controller@index');
});
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
