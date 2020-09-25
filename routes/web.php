<?php

Route::get('docs/{version?}/{slug?}', 'Docs\Controller@index')->where('slug', '.*');
