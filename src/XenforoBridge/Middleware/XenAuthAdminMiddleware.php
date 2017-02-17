<?php namespace Urb\XenforoBridge\Middleware;

use Closure;
use Config;
use Session;
use Redirect;
use Urb\XenforoBridge\XenforoBridge;

class XenAuthAdminMiddleware {

    /**
     * stores Xenforo Bridge class
     * @var XenforoBridge\XenforoBridge
     */
    private $xenforo;

    /**
     * Construct Middleware Class
     * 
     * @param \Urb\XenforoBridge\XenforoBridge $xenforo
     */
    public function __construct(XenforoBridge $xenforo)
    {
        $this->xenforo = $xenforo;
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $xenBaseUrl = config('xenforobridge.xenforo_base_url_path');

        if(!$this->xenforo->isAdmin() AND ! $this->xenforo->isBanned())
        {
            Session::put('loginRedirect', $request->url());
            return Redirect::to($xenBaseUrl.'login');
        }
        
        return $next($request);
    }

}