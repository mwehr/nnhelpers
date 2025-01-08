<?php

namespace nn;

use Nng\Nnhelpers\Utilities\Arrays;
use Nng\Nnhelpers\Utilities\BackendUser;
use Nng\Nnhelpers\Utilities\Cache;
use Nng\Nnhelpers\Utilities\Configuration;
use Nng\Nnhelpers\Utilities\Content;
use Nng\Nnhelpers\Utilities\Convert;
use Nng\Nnhelpers\Utilities\Cookies;
use Nng\Nnhelpers\Utilities\Db;
use Nng\Nnhelpers\Utilities\Dom;
use Nng\Nnhelpers\Utilities\Environment;
use Nng\Nnhelpers\Utilities\Errors;
use Nng\Nnhelpers\Utilities\Encrypt;
use Nng\Nnhelpers\Utilities\Fal;
use Nng\Nnhelpers\Utilities\File;
use Nng\Nnhelpers\Utilities\Flexform;
use Nng\Nnhelpers\Utilities\FrontendUser;
use Nng\Nnhelpers\Utilities\FrontendUserAuthentication;
use Nng\Nnhelpers\Utilities\Geo;
use Nng\Nnhelpers\Utilities\Http;
use Nng\Nnhelpers\Utilities\LL;
use Nng\Nnhelpers\Utilities\Log;
use Nng\Nnhelpers\Utilities\Mail;
use Nng\Nnhelpers\Utilities\Message;
use Nng\Nnhelpers\Utilities\Menu;
use Nng\Nnhelpers\Utilities\Obj;
use Nng\Nnhelpers\Utilities\Page;
use Nng\Nnhelpers\Utilities\Registry;
use Nng\Nnhelpers\Utilities\Request;
use Nng\Nnhelpers\Utilities\Settings;
use Nng\Nnhelpers\Utilities\Slug;
use Nng\Nnhelpers\Utilities\Storage;
use Nng\Nnhelpers\Utilities\SysCategory;
use Nng\Nnhelpers\Utilities\TCA;
use Nng\Nnhelpers\Utilities\Template;
use Nng\Nnhelpers\Utilities\Tsfe;
use Nng\Nnhelpers\Utilities\TypoScript;
use Nng\Nnhelpers\Utilities\Video;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;

class t3 
{
	/**
	 * @return Arrays
	 */
	public static function Arrays( $array = null ) {
		return new \Nng\Nnhelpers\Utilities\Arrays( $array );
	}

	/**
	 * @return BackendUser
	 */
	public static function BackendUser() {
		return self::injectClass(BackendUser::class);
	}

	/**
	 * @return Configuration
	 */
	public static function Configuration() {
		return self::injectClass(Configuration::class);
	}
	
	/**
	 * @return Cache
	 */
	public static function Cache() {
		return self::injectClass(Cache::class);
	}
   
	/**
	 * @return Content
	 */
	public static function Content() {
		return new \Nng\Nnhelpers\Utilities\Content();
	}
	
	/**
	 * @return Convert
	 */
	public static function Convert( $obj = null ) {
		return new \Nng\Nnhelpers\Utilities\Convert( $obj );
	}

	/**
	 * @return Cookies
	 */
	public static function Cookies() {
		return self::injectClass(Cookies::class);
	}

	/**
	 * @return Db
	 */
	public static function Db() {
		return self::injectClass(Db::class);
	}

	/**
	 * @return Dom
	 */
	public static function Dom( $html = null ) {
		return new \Nng\Nnhelpers\Utilities\Dom($html);
	}

	/**
	 * @return Environment
	 */
	public static function Environment() {
		return self::injectClass(Environment::class);
	}

	/**
	 * @return Encrypt
	 */
	public static function Encrypt() {
		return self::injectClass(Encrypt::class);
	}
	
	/**
	 * @return Errors
	 */
	public static function Errors() {
		return self::injectClass(Errors::class);
	}
   
	/**
	 * @return Fal
	 */
	public static function Fal() {
		return self::injectClass(Fal::class);
	}
	
	/**
	 * @return File
	 */
	public static function File() {
		return self::injectClass(File::class);
	}

	/**
	 * @return Flexform
	 */
	public static function Flexform() {
		return self::injectClass(Flexform::class);
	}

	/**
	 * @return FrontendUser
	 */
	public static function FrontendUser() {
		return self::injectClass(FrontendUser::class);
	}

	/**
	 * @return FrontendUserAuthentication
	 */
	public static function FrontendUserAuthentication() {
		return self::injectClass(FrontendUserAuthentication::class);
	}
	
	/**
	 * @return Geo
	 */
	public static function Geo( $config = [] ) {
		return new Geo( $config );
	}
	
	/**
	 * @return Http
	 */
	public static function Http() {
		return self::injectClass(Http::class);
	}

	/**
	 * @return LL
	 */
	public static function LL() {
		return self::injectClass(LL::class);
	}
	
	/**
	 * @return Log
	 */
	public static function Log( $message = null ) {
		return new \Nng\Nnhelpers\Utilities\Log($message);
	}

	/**
	 * @return Mail
	 */
	public static function Mail() {
		return self::injectClass(Mail::class);
	}
	
	/**
	 * @return Message
	 */
	public static function Message() {
		return self::injectClass(Message::class);
	}

	/**
	 * @return Menu
	 */
	public static function Menu() {
		return self::injectClass(Menu::class);
	}
   
	/**
	 * @return Obj
	 */
	public static function Obj( $obj = null ) {
		return new \Nng\Nnhelpers\Utilities\Obj( $obj );
	}
	
	/**
	 * @return Page
	 */
	public static function Page() {
		return self::injectClass(Page::class);
	}
	
	/**
	 * @return Registry
	 */
	public static function Registry() {
		return self::newClass(Registry::class);
	}
	
	/**
	 * @return Request
	 */
	public static function Request() {
		return self::injectClass(Request::class);
	}

	/**
	 * @return Settings
	 */
	public static function Settings() {
		return self::injectClass(Settings::class);
	}

	/**
	 * @return Slug
	 */
	public static function Slug() {
		return self::injectClass(Slug::class);
	}
   
	/**
	 * @return Storage
	 */
	public static function Storage() {
		return self::injectClass(Storage::class);
	}
   
	/**
	 * @return SysCategory
	 */
	public static function SysCategory() {
		return self::injectClass(SysCategory::class);
	}
   
	/**
	 * @return TCA
	 */
	public static function TCA() {
		return self::injectClass(TCA::class);
	}

	/**
	 * @return Template
	 */
	public static function Template() {
		return self::injectClass(Template::class);
	}
	
	/**
	 * @return Tsfe
	 */
	public static function Tsfe() {
		return self::injectClass(Tsfe::class);
	}
   
	/**
	 * @return TypoScript
	 */
	public static function TypoScript() {
		return self::injectClass(TypoScript::class);
	}

	/**
	 * @return Video
	 */
	public static function Video() {
		return self::injectClass(Video::class);
	}
	
	// ---------------------------------------------------------------------------------
	//  Funktionen, die so oft verwendet werden, dass sie nicht in einer eigenen
	//  Utility-Class gekapselt werden.

	/**
	 *  Eine Klasse über new Class\Name instanziieren.
	 * 
	 *  $example = \nn\t3::newClass( \My\Example\ClassName::class );
	 * 
	 *  @return mixed  
	 */
	public static function newClass($class) {
		if (!class_exists($class)) return false;
		
		if (is_a($class, \Nng\Nnhelpers\Singleton::class, true)) {
			return call_user_func($class . '::makeInstance');
		}

		return new $class;
	}

	/**
	 * Schnellste Art, eine Klasse zu instanziieren.
	 * Verwendet ObjectManager.
	 * ```
	 * $example = \nn\t3::injectClass( \My\Example\ClassName::class );
	 * ```
	 * @return mixed  
	 */
	public static function injectClass($class) {

		if ($cache = $GLOBALS['__nnt3_cachedSingletons_' . $class] ?? false) {
			return $cache;
		}

		if (!class_exists($class)) return false;
		$class = ltrim( $class, '\\');

		if (is_a($class, \Nng\Nnhelpers\Singleton::class, true)) {
			return $GLOBALS['__nnt3_cachedSingletons_' . $class] = call_user_func($class . '::makeInstance');
		}

		return GeneralUtility::makeInstance( $class );
	}
	
	/**
	 * Externe Libraries laden (nur bei Bedarf, um Konflikte zu vermeiden)
	 * ```
	 * \nn\t3::autoload();
	 * ```
	 * @return void
	 */
	public static function autoload() {
		require_once( \nn\t3::Environment()->extPath('nnhelpers') . 'Resources/Libraries/vendor/autoload.php');
	}
	
	/**
	 *  Die Version von Typo3 holen
	 * 
	 *  $typo3Version = \nn\t3::t3Version();
	 * 
	 *	@return float
	 */
	public static function t3Version() {
		// ab >= 12
		if (class_exists(\TYPO3\CMS\Core\Information\Typo3Version::class)) {
			return (new \TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion();
		}
		// bis <= 11
		return floor(VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version)/1000000);
	}
	
	/**
	 *  Wirft eine Exception.
	 *  Alias zu \nn\t3::Errors()->Exception( $message, $code );
	 *  ```
	 *  \nn\t3::Exception( 'Damn.' );
	 *  \nn\t3::Exception( 'Damn.', '4711' );
	 *  ```
	 *  @param string $text     Fehler-Meldung
	 *  @param string $code     Fehler-Code (Optional)
	 *	@return void
	 */
	public static function Exception( $message = '', $code = '' ) {
	   \nn\t3::Errors()->Exception( $message, $code );
	}
	
	/**
	 *  Wirft einen Fehler.
	 *  Alias zu \nn\t3::Errors()->Error( $message, $code );
	 *  ```
	 *  \nn\t3::Error( 'Damn.' );
	 *  \nn\t3::Error( 'Damn.', '4711' );
	 *  ```
	 *  @param string $text     Fehler-Meldung
	 *  @param string $code     Fehler-Code (Optional)
	 *	@return void
	 */
	public static function Error( $message = '', $code = '' ) {
	   \nn\t3::Errors()->Error( $message, $code );
	}
	
	/**
	 *  Der bessere Debugger für alles.
	 *  Gibt auch Zeilen-Nummer und Class mit an, die den Debug aufgerufen hat – 
	 *  dadurch leichter wiederzufinden.
	 *  Kann QueryBuilder-Statements ausgeben, um MySQL-Queries zu debuggen. 
	 *  ```
	 *  \nn\t3::debug('Hallo!');
	 *  \nn\t3::debug($queryBuilder);
	 *  \nn\t3::debug($query);
	 *  ```
	 *	@return float
	 */
	public static function debug( $obj = null, $title = null ) {

		// Ermittelt, wo der Aufruf von debug() stattgefunden hat
		$backtrace = debug_backtrace();
		$backtrace = array_shift($backtrace);

		// Absoluten Pfad zum Extension-Ordner in Schreibweise mit 'EXT:'-Prefix kürzen 
		$filename = str_replace( \nn\t3::Environment()->getPathSite() . 'typo3conf/ext/', 'EXT:', $backtrace['file']);
		$line = $backtrace['line'];
		$callerInfo = ($title ? $title . ' // ' : '') . $filename . ' Zeile ' . $line;

		// Bei Debug von Queries: MySQL-Statement ausgeben
		if ($obj instanceof QueryBuilder || $obj instanceof Query) {
			$queryStatement = \nn\t3::Db()->debug( $obj, true );

			// Inline-Styles: Dadurch unabhängig von jeglicher CSS-Einbindung. Angelehnt an die Core Debugger-Utility.
			echo '
				<div style="position: relative; z-index: 1000; background: #222; color: #ce9178; font: 12px/1.5 monospace; padding: 15px; margin: 20px;">
					<div style="background-color: #444; color: #fff; margin: -15px -15px 10px -15px; padding: 8px 15px">' . $callerInfo . '</div>
					<div>' . $queryStatement . '</div>
				</div>
			';
			 return $queryStatement;
		}

		return DebuggerUtility::var_dump( $obj, $callerInfo );
	}


	/**
	 * Ruft eine Methode in einem Objekt auf.
	 * Parameter werden als Referenz übergeben, können in Methode modifiziert werden.
	 *
	 * ```
	 * $result = \nn\t3::call( 'My\Extension\ClassName->method' );
	 * $result = \nn\t3::call( 'My\Extension\ClassName->method', $param1, $params2, $params3 );
	 * ```
	 * @var string $funcStr 			=> z.B. \Nng\Nnsubscribe\Service\NotifcationService->do_someting
	 * @var array $params				=> Parameter, die an Funktion übergeben werden sollen
	 * @var array $params2				=> zweiter Parameter, der an Funktion übergeben werden sollen
	 * @var array $params3				=> dritter Parameter, der an Funktion übergeben werden sollen
	 *
	 * @return mixed
	 */
	public static function call ( $funcStr, &$params = [], &$params2 = null, &$params3 = null, &$params4 = null ) {
		if (!trim($funcStr)) self::Exception("\\nn\\t3::call() - Keine Klasse angegeben.");
		
		$useStaticCall =strpos($funcStr, '::') !== false;
		$delimiter = $useStaticCall ? '::' : '->';

		list($class, $method) = explode( $delimiter, $funcStr );
		if (!class_exists($class)) self::Exception("\\nn\\t3::call({$class}) - Klasse {$class} existiert nicht.");
		
		$classRef = self::injectClass($class);
		if (!method_exists($classRef, $method)) self::Exception("\\nn\\t3::call() - Methode {$class}->{$method}() existiert nicht.");

		// $allParams = [&$params, &$params2, &$params3, &$params4];
		// return $classRef->$method( ...$allParams );

		if ($useStaticCall) {
			return call_user_func_array([$class, $method], [$params, $params2, $params3, $params4]);
		}

		if ($params4 != null) return $classRef->$method($params, $params2, $params3, $params4);		
		if ($params3 != null) return $classRef->$method($params, $params2, $params3);		
		if ($params2 != null) return $classRef->$method($params, $params2);
		return $classRef->$method($params);
	}

}