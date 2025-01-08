<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Symfony\Component\HttpFoundation\Cookie;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Methoden zum Setzen eines Cookies.
 * 
 * Seit TYPO3 12 können Cookies nicht einfach über `$_COOKIE[]` gesetzt werden.
 * Sie müssen stattdessen im `Psr\Http\Message\ResponseInterface` gesetzt werden.
 * 
 * 
 */
class Cookies extends \Nng\Nnhelpers\Singleton {
	
	/**
	 * @var array
	 */
	protected $cookiesToSet = [];

	/**
	 * Einen Cookie erzeugen - aber noch nicht an den Client senden.
	 * Der Cookie wird erst in der Middleware gesetzt, siehe:
	 * `\Nng\Nnhelpers\Middleware\ModifyResponse`
	 * 
	 * ```
	 * $cookie = \nn\t3::Cookies()->add( $name, $value, $expire );
	 * $cookie = \nn\t3::Cookies()->add( 'my_cookie', 'my_nice_value', time() + 60 );
	 * ```
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @return Cookie
	 */
	public function add ( $name = '', $value = '', $expire = 0 ) 
	{
		$this->cookiesToSet[$name] = [
			'value' => $value, 
			'expire' => $expire
		];
	}

	/**
	 * Gibt alle Cookies zurück, die darauf warten, in der Middleware
	 * beim Response gesetzt zu werden.
	 * ```
	 * $cookies = \nn\t3::Cookies()->getAll();
	 * ```
	 * @return array
	 */
	public function getAll () 
	{
		return $this->cookiesToSet;
	}

	/**
	 * Fügt alle gespeicherten Cookies an den PSR-7 Response.
	 * Wird von `\Nng\Nnhelpers\Middleware\ModifyResponse` aufgerufen.
	 * ```
	 * // Beispiel in einer MiddleWare:
	 * $response = $handler->handle($request);
	 * \nn\t3::Cookies()->addCookiesToResponse( $request, $response );
	 * ```
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $request
	 * 
	 */
	public function addCookiesToResponse ( $request, &$response ) 
	{
		foreach ($this->cookiesToSet as $name=>$cookie) {
			$cookie = $this->create( $request, $name, $cookie['value'], $cookie['expire'] );
			$response = $response->withAddedHeader('Set-Cookie', (string) $cookie);
		}
		return $response;
	}

	/**
	 * Eine Instanz des Symfony-Cookies erzeugen
	 * ```
	 * $cookie = \nn\t3::Cookies()->create( $request, $name, $value, $expire );
	 * $cookie = \nn\t3::Cookies()->create( $request, 'my_cookie', 'my_nice_value', time() + 60 );
	 * ```
	 * @param ServerRequestInterface $request
	 * @param string $name
	 * @param string $value
	 * @param int $expire
	 * @return Cookie
	 */
	public function create ( $request = null, $name = '', $value = '', $expire = 0 ) 
	{
		$normalizedParams = $request->getAttribute('normalizedParams');
        $secure = $normalizedParams instanceof NormalizedParams && $normalizedParams->isHttps();
        $path = $normalizedParams->getSitePath();
        
        return new Cookie(
            $name,
            $value,
            $expire,
            $path,
            null,
            $secure,
            true,
            false,
            Cookie::SAMESITE_STRICT
        );
	}
	
}