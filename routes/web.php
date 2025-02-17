<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;


Route::get('/', [ContactController::class, 'index'])->name('contacts.index');



Route::resource('contacts', ContactController::class);
Route::post('/contacts/import', [ContactController::class, 'importXML'])->name('contacts.importXML');


