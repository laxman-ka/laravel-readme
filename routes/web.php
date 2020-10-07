<?php

Route::get('/{version?}/{slug?}', 'Docs\Controller@index')->where('slug', '.*');
