<?php 

//Check if user is a xenforo admin and not banned
Route::filter('xen.auth.admin', function()
{
	$xenBaseUrl = Config::get('xenforobridge::xenforo_base_url_path');

	$xenforo = App::make('XenforoBridge');
	if(!$xenforo->isSuperAdmin() AND ! $xenforo->isBanned())
	{
		Session::put('loginRedirect', Request::url());
		return Redirect::to($xenBaseUrl.'login');
	}
});

//Check if user is logged in and not banned
Route::filter('xen.auth', function()
{
	$xenBaseUrl = Config::get('xenforobridge::xenforo_base_url_path');

	$xenforo = App::make('XenforoBridge');

	if(!$xenforo->isLoggedIn() AND ! $xenforo->isBanned())
	{
		Session::put('loginRedirect', Request::url());
		return Redirect::to($xenBaseUrl.'login');
	}
});