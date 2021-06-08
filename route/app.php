<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;
Route::group('region',function (){
    Route::any('getRegion','\yupoxiong\region\RegionController@getRegion');
    Route::any('getProvince','\yupoxiong\region\RegionController@getProvince');
    Route::any('getCity','\yupoxiong\region\RegionController@getCity');
    Route::any('getDistrict','\yupoxiong\region\RegionController@getDistrict');
    Route::any('getStreet','\yupoxiong\region\RegionController@getStreet');
    Route::any('searchRegion','\yupoxiong\region\RegionController@searchRegion');
    Route::any('searchProvince','\yupoxiong\region\RegionController@searchProvince');
    Route::any('searchCity','\yupoxiong\region\RegionController@searchCity');
    Route::any('searchDistrict','\yupoxiong\region\RegionController@searchDistrict');
    Route::any('searchStreet','\yupoxiong\region\RegionController@searchStreet');
});