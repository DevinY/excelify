<?php
Route::middleware(['api'])->group(function () {
    Route::get('/api/excelify/{tablenum?}','\Deviny\Excelify\Controllers\ExcelifyController@excelify'); 
    Route::get('/api/clean_excel_files/{times?}','\Deviny\Excelify\Controllers\ExcelifyController@clear_temp_folder');
});
Route::middleware(['web'])->group(function () {
    Route::get('/excelify','\Deviny\Excelify\Controllers\ExcelReaderController@index');
    Route::redirect('/excel_reader','/excelify');
    Route::post('/unlock','\Deviny\Excelify\Controllers\ExcelReaderController@unlock');

    Route::get('/lock','\Deviny\Excelify\Controllers\ExcelReaderController@lock');

//下載轉存Excel的資料
    Route::get('/download_excel','\Deviny\Excelify\Controllers\ExcelReaderController@download_excel');

//下載網頁轉換的Excel
    Route::get('/download_temp','\Deviny\Excelify\Controllers\ExcelifyController@download_temp');

    Route::post('/download','\Deviny\Excelify\Controllers\ExcelReaderController@download');

    Route::post('/excel_reader','\Deviny\Excelify\Controllers\ExcelReaderController');

    Route::get('/excelify','\Deviny\Excelify\Controllers\ExcelifyController@index');

    Route::post('/excelify','\Deviny\Excelify\Controllers\ExcelifyController');

    Route::get('/test', function() {
        return storage_path();
    });
});

