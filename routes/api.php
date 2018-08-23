<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings']
], function($api) {

    // 用户注册相接口
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.sign.limit'),
        'expires' => config('api.rate_limits.sign.expires'),
    ], function($api) {
        $api->post('verificationCodes', 'VerificationCodesController@store')->name('api.verificationCodes.store');
        $api->post('users', 'UsersController@store')->name('users.store');
        $api->post('authorizations', 'AuthorizationsController@store')->name('api.authorizations.store');
        $api->post('socials/{socials_type}/authorizations', 'AuthorizationsController@socialStore')->name('api.socials.authorizations.store');
        $api->put('authorizations/current', 'AuthorizationsController@update')->name('api.authorizations.update');
        $api->delete('authorizations/current', 'AuthorizationsController@destroy')->name('api.authorizations.destroy');
    });

    // 通用接口
    $api->group([
        'middleware' => 'api.throttle',
        'limit' => config('api.rate_limits.access.limit'),
        'expires' => config('api.rate_limits.access.expires'),
    ], function($api) {
        $api->post('captchas', 'CaptchasController@store')->name('captchas.store');

        // 话题分类
        $api->get('categories', 'CategoriesController@index')->name('api.categories.index');

        // 话题
        $api->get('topics', 'TopicsController@index')->name('api.topics.index');
        $api->get('user/{user}/topics', 'TopicsController@userIndex')->name('api.users.topics.index');
        $api->get('topics/{topic}', 'TopicsController@show')->name('api.topics.show');

        // 回复
        $api->get('topics/{topic}/replies', 'RepliesController@index')->name('api.topics.replies');
        $api->get('users/{user}/replies', 'RepliesController@userIndex')->name('api.users.replies');

        // 资源推荐
        $api->get('links', 'LinksController@index')->name('api.topics.replies');



        // 需要 token 验证的接口
        $api->group(['middleware' => 'api.auth'], function($api) {

            // 用户
            $api->get('user', 'UsersController@me')->name('api.user.show');
            $api->patch('user', 'UsersController@update')->name('api.user.update');

            // 图片
            $api->post('images', 'ImagesController@store')->name('api.images.store');

            // 话题
            $api->post('topics', 'TopicsController@store')->name('api.topics.store');
            $api->patch('topics/{topic}', 'TopicsController@update')->name('api.topics.update');
            $api->delete('topics/{topic}', 'TopicsController@destroy')->name('api.topics.destroy');

            // 回复
            $api->post('topics/{topic}/replies', 'RepliesController@store')->name('api.topics.replies.store');
            $api->delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')->name('api.topics.replies.destroy');

            // 消息
            $api->get('user/notifications', 'NotificationsController@index')->name('api.user.notifications.index');
            $api->get('user/notifications/stats', 'NotificationsController@stats')->name('api.user.notifications.stats');
            $api->patch('user/notifications/read', 'NotificationsController@read')->name('api.user.notifications.read');
        });

        // 当前登录用户权限
        $api->get('user/permissions', 'PermissionsController@index')->name('api.user.permissions.index');

    });

});