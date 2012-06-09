<?php
// namespace
namespace icore\auth;

define('ETSY_OAUTH_ENTRY_REAL', 'http://openapi.etsy.com/v2');
define('ETSY_OAUTH_ENTRY_SANDBOX', 'http://sandbox.openapi.etsy.com/v2');

use icore\Auth;

/**
 * OAuth for Etsy.com
 */
class EtsyOAuth extends Auth
{
	protected $_entry;
	protected $_callback_url;
	protected $_scope;
	protected $_debug;

	public function setCallback($url)
	{
		$this->_callback_url = $url;
	}

	public function setEntry($entry = ETSY_OAUTH_ENTRY_SANDBOX)
	{
		$this->_entry = $entry;
		$this->_debug = $this->_entry === ETSY_OAUTH_ENTRY_SANDBOX ? true : false;
	}

	/**
	 * scope reference: http://www.etsy.com/developers/documentation/getting_started/oauth
	 *
	 * ex. 'email_r profile_r address_r'
	 */
	public function setScope($scope)
	{
		$this->_scope = $scope;
	}

	/**
	 * process 1: send request for request token
	 *
	 * you must call setCallback & setEntry first to call this method
	 * @param string app id you create on the server
	 * @param string secret the server create for you
	 * @return string login url to grant the access to the client
	 */
	public function auth($appId, $secret)
	{
		$oauth = new OAuth($appId, $secret);	
		if ($this->_debug) $oauth->enableDebug();

		$scope = empty($this->_scope) ? '' : '?scope=' . rawurlencode($this->_scope);
		$data = $oauth->getRequestToken($this->_entry . '/oauth/request_token' . $scope, $this->_callback_url);
		if (!$data)
			throw new \Exception($oauth->getLastResponse());

		// remember oauth_token & oauth_token_secret
		$_SESSION['oauth_token'] = $data['oauth_token'];
		$_SESSION['oauth_token_secret'] = $data['oauth_token_secret'];

		return $data['login_url'];
	}

	/**
	 * process 2: send request for access token
	 *
	 * you must call setEntry first, and put this method on the callback page
	 */
	public function authAgain($appId, $secret)
	{
		$oauth = new OAuth($appId, $secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
		if ($this->_debug) $oauth->enableDebug();

		$oauth_token = $_GET['oauth_token'];
		$oauth_verifier = $_GET['oauth_verifier'];

		if ($oauth_token !== $_SESSION['oauth_token'])
			return false; // not the same auth in previous call
		
		// set the tmp token
		$oatuh->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$data = $oauth->getAccessToken($this->_entry . '/oauth/access_token', null, $oauth_verifier);
		if (!$data)
			throw new \Exception($oauth->getLastResponse());

		// if ok, update SESSION
		$_SESSION['oauth_token'] = $data['oauth_token'];
		$_SESSION['oauth_token_secret'] = $data['oauth_token_secret'];

		// fetch user information
		$oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		$ret = $oauth->fetch($this->_entry . '/users/__SELF__', null, OAUTH_HTTP_METHOD_GET);
		if (!$ret)
			throw new \Exception($oauth->getLastResponse());
		
		// user information
		$data = json_decode($oauth->getLastResponse());

		$user = array();
		$user['login'] = $data['primary_email'];
		$user['pwd'] = $data['primary_email'];
		$user['nick'] = $data['login_name'];
		$user['extra'] = 'etsy.com:' . $data['user_id'];
		// TODO: check local, add session or error
	}
}
