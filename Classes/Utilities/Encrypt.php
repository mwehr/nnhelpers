<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;

/**
 * Verschlüsseln und Hashen von Passworten
 */
class Encrypt implements SingletonInterface {
   
	/**
	 * Verschlüsselungsalgorithmen
	 * 
	 */
	const ENCRYPTION_METHOD = 'aes-256-cbc';
	const ENCRYPTION_HMAC 	= 'sha3-512';

	/**
	 * Holt den Enryption / Salting Key aus der Extension Konfiguration für `nnhelpers`.
	 * Falls im Extension Manager noch kein Key gesetzt wurde, wird er automatisch generiert
	 * und in der `LocalConfiguration.php` gespeichert.
	 * ```
	 * \nn\t3::Encrypt()->getSaltingKey();
	 * ```
	 * @return string
	 */
	public function getSaltingKey() {
		if ($key = \nn\t3::Settings()->getExtConf('nnhelpers')['saltingKey'] ?? false) {
			return $key;
		}

		$key = base64_encode(json_encode([
			base64_encode(openssl_random_pseudo_bytes(32)), 
			base64_encode(openssl_random_pseudo_bytes(64))
		]));

		if (!\nn\t3::Settings()->setExtConf( 'nnhelpers', 'saltingKey', $key)) {
			\nn\t3::Exception('Please first set the encryption key in the Extension-Manager for nnhelpers!');
		}
		return $key;
	}

	/**
	 * Verschlüsselt einen String oder ein Array.
	 * 
	 * Im Gegensatz zu `\nn\t3::Encrypt()->hash()` kann ein verschlüsselter Wert per `\nn\t3::Encrypt()->decode()`
	 * wieder entschlüsselt werden. Diese Methods eignet sich daher __nicht__, um sensible Daten wie z.B. Passworte 
	 * in einer Datenbank zu speichern. Dennoch ist der Schutz relativ hoch, da selbst identische Daten, die mit
	 * dem gleichen Salting-Key verschlüsselt wurden, unterschiedlich aussehen.
	 * 
	 * Für die Verschlüsselung wird ein Salting Key generiert und in dem Extension Manager von `nnhelpers` gespeichert.
	 * Dieser Key ist für jede Installation einmalig. Wird er verändert, dann können bereits verschlüsselte Daten nicht
	 * wieder entschlüsselt werden.
	 * ```
	 * \nn\t3::Encrypt()->encode( 'mySecretSomething' );
	 * \nn\t3::Encrypt()->encode( ['some'=>'secret'] );
	 * ```
	 * Komplettes Beispiel mit Verschlüsselung und Entschlüsselung:
	 * ```
	 * $encryptedResult = \nn\t3::Encrypt()->encode( ['password'=>'mysecretsomething'] );
	 * echo \nn\t3::Encrypt()->decode( $encryptedResult )['password'];
	 * 
	 * $encryptedResult = \nn\t3::Encrypt()->encode( 'some_secret_phrase' );
	 * echo \nn\t3::Encrypt()->decode( $encryptedResult );
	 * ```
	 * @return string
	 */
	public function encode( $data = '' ) {
		[$key1, $key2] = json_decode(base64_decode( $this->getSaltingKey() ), true);

		$data = json_encode(['_'=>$data]);
			
		$method = self::ENCRYPTION_METHOD;   
		$iv_length = openssl_cipher_iv_length($method);
		$iv = openssl_random_pseudo_bytes($iv_length);
			
		$first_encrypted = openssl_encrypt($data, $method, base64_decode($key1), OPENSSL_RAW_DATA, $iv);   
		$second_encrypted = hash_hmac(self::ENCRYPTION_HMAC, $first_encrypted, base64_decode($key2), TRUE);
				
		$output = base64_encode($iv . $second_encrypted . $first_encrypted);   
		return $output;   
	}

	/**
	 * Entschlüsselt einen String oder ein Array.
	 * Zum Verschlüsseln der Daten kann `\nn\t3::Encrypt()->encode()` verwendet werden.
	 * Siehe `\nn\t3::Encrypt()->encode()` für ein komplettes Beispiel.
	 * ```
	 * \nn\t3::Encrypt()->decode( '...' );
	 * ```
	 * @return string
	 */
	public function decode( $data = '' ) {

		[$key1, $key2] = json_decode(base64_decode( $this->getSaltingKey() ), true);
		$mix = base64_decode($data);

		$method = self::ENCRYPTION_METHOD;  
		$iv_length = openssl_cipher_iv_length($method);
				
		$iv = substr($mix, 0, $iv_length);
		$second_encrypted = substr($mix, $iv_length, 64);
		$first_encrypted = substr($mix, $iv_length + 64);
	
		$data = openssl_decrypt($first_encrypted, $method, base64_decode($key1), OPENSSL_RAW_DATA, $iv);
		$second_encrypted_new = hash_hmac(self::ENCRYPTION_HMAC, $first_encrypted, base64_decode($key2), TRUE);
		
		if (hash_equals($second_encrypted, $second_encrypted_new)) {
			$data = json_decode( $data, true );
			return $data['_'] ?? null;
		}
		
		return false;
	}
	
	/**
	 * Einfaches Hashing, z.B. beim Check einer uid gegen ein Hash.
	 * 
	 * ```
	 * \nn\t3::Encrypt()->hash( $uid );
	 * ```
	 * Existiert auch als ViewHelper:
	 * ```
	 * {something->nnt3:encrypt.hash()}
	 * ```
	 * @return string
	 */
	public function hash( $string = '' ) {
		$salt = $this->getSaltingKey();
		return preg_replace('/[^a-zA-Z0-9]/', '', base64_encode( sha1("{$string}-{$salt}", true )));
	}

	/**
	 * Session-Hash für `fe_sessions.ses_id` holen.
	 * Enspricht dem Wert, der für den Cookie `fe_typo_user` in der Datenbank gespeichert wird.
	 * 
	 * In TYPO3 < v10 wird hier ein unveränderter Wert zurückgegeben. Ab TYPO3 v10 wird die Session-ID im 
	 * Cookie `fe_typo_user` nicht mehr direkt in der Datenbank gespeichert, sondern gehashed.
	 * Siehe: `TYPO3\CMS\Core\Session\Backend\DatabaseSessionBackend->hash()`.
	 * ```
	 * \nn\t3::Encrypt()->hashSessionId( $sessionIdFromCookie );
	 * ```
	 * Beispiel:
	 * ```
	 * $cookie = $_COOKIE['fe_typo_user'];
	 * $hash = \nn\t3::Encrypt()->hashSessionId( $cookie );
	 * $sessionFromDatabase = \nn\t3::Db()->findOneByValues('fe_sessions', ['ses_id'=>$hash]);
	 * ```
	 * Wird unter anderen verwendet von: `\nn\t3::FrontendUserAuthentication()->loginBySessionId()`.
	 * 
	 * @return string
	 */
	public function hashSessionId( $sessionId = null ) {
		$key = sha1($GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . 'core-session-backend');
		return hash_hmac('sha256', $sessionId, $key);
	}

	/**
	 *	Hashing eines Passwortes nach Typo3-Prinzip.
	 *	Anwendung: Passwort eines fe_users in der Datenbank überschreiben
	 *	```
	 *	\nn\t3::Encrypt()->password('99grad');
	 *	```
	 * 	@return string
	 */
    public function password ( $clearTextPassword = '', $context = 'FE' ) {
		$hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance( $context );
		$saltedPassword = $hashInstance->getHashedPassword( $clearTextPassword );
		return $saltedPassword;
	}

	/**
	 *	Prüft, ob Hash eines Passwortes und ein Passwort übereinstimmen.
	 *	Anwendung: Passwort-Hash eines fe_users in der Datenbank mit übergebenem Passwort
	 *	vergleichen.
	 *	```
	 *	\nn\t3::Encrypt()->checkPassword('99grad', '$1$wtnFi81H$mco6DrrtdeqiziRJyisdK1.');
	 *	```
	 * 	@return boolean
	 */
	public function checkPassword ( $password = '', $passwordHash = null ) {

		// siehe localConfiguration.php [FE][passwordHashing][className], default für Typo3 9 ist \TYPO3\CMS\Core\Crypto\PasswordHashing\BcryptPasswordHash
		$hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('FE');
		$result = $hashInstance->checkPassword($password, $passwordHash);
		if ($result) return true;

		// Fallback für Passworte, die nach Update auf Typo3 9 noch den md5-Hash oder andere verwenden
		if ($hashInstance = $this->getHashInstance( $passwordHash )) {
			$result = $hashInstance->checkPassword($password, $passwordHash);
			return $result;
		}

		return false;
	}

	/**
	 * Prüft, ob Hash aktualisiert werden muss, weil er nicht dem aktuellen Verschlüsselungs-Algorithmus enspricht.
	 * Beim Update von Typo3 in eine neue LTS wird gerne auch der Hashing-Algorithmus der Passwörter in der Datenbank
	 * verbessert. Diese Methode prüft, ob der übergebene Hash noch aktuell ist oder aktualisert werden muss.
	 * 
	 * Gibt `true` zurück, falls ein Update erforderlich ist.
	 * ```
	 * \nn\t3::Encrypt()->hashNeedsUpdate('$P$CIz84Y3r6.0HX3saRwYg0ff5M0a4X1.');	// true
	 * ```
	 * Ein automatisches Update des Passwortes könnte in einem manuellen FE-User Authentification-Service so aussehen:
	 * ```
	 * $uid = $user['uid'];	// uid des FE-Users
	 * $authResult = \nn\t3::Encrypt()->checkPassword( $passwordHashInDatabase, $clearTextPassword );
	 * if ($authResult && \nn\t3::Encrypt()->hashNeedsUpdate( $passwordHashInDatabase )) {
	 * 	\nn\t3::FrontendUserAuthentication()->setPassword( $uid, $clearTextPassword );
	 * }
	 * ```
	 * @return boolean
	 */
	public function hashNeedsUpdate( $passwordHash = '', $loginType = 'FE' ) {

		// Könnte z.B. `TYPO3\CMS\Core\Crypto\PasswordHashing\PhpassPasswordHash` sein
		$currentHashInstance = $this->getHashInstance( $passwordHash );

		// Könnte z.B. `TYPO3\CMS\Core\Crypto\PasswordHashing\BcryptPasswordHash` sein
		$expectedHashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance( $loginType );
		
		return get_class($currentHashInstance) != get_class($expectedHashInstance);
	}

	/**
	 * Gibt den Klassen-Names des aktuellen Hash-Algorithmus eines verschlüsselten Passwortes wieder,
	 * z.B. um beim fe_user zu wissen, wie das Passwort in der DB verschlüsselt wurde.
	 * ```
	 * \nn\t3::Encrypt()->getHashInstance('$P$CIz84Y3r6.0HX3saRwYg0ff5M0a4X1.'); 
	 * // => \TYPO3\CMS\Core\Crypto\PasswordHashing\PhpassPasswordHash
	 * ```
	 * @return class
	 */
	public function getHashInstance( $passwordHash = '', $loginType = 'FE' ) {
		$saltFactory = GeneralUtility::makeInstance(PasswordHashFactory::class);
		
		$hashInstance = false;
		try {
			$hashInstance = $saltFactory->get( $passwordHash, $loginType );
        } catch (InvalidPasswordHashException $invalidPasswordHashException) {
			// unknown
		}
		return $hashInstance;
	}

	/**
	 * Prüft, ob angegebenes Passwort noch nicht gehashed wurde.
	 * Fallback für Typo3 v8. Kann in Typo3 > v9 entfernt werden.
	 * ```
	 * \nn\t3::Encrypt()->isSaltedHash('$1$wtnFi81H$mco6DrrtdeqiziRJyisdK1.');
	 * ```
	 * @return bool
	 */
	protected function isSaltedHash($password) {
		$isSaltedHash = FALSE;
		if (strlen($password) > 2 && (GeneralUtility::isFirstPartOfStr($password, 'C$') || GeneralUtility::isFirstPartOfStr($password, 'M$'))) {
			$isSaltedHash = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::determineSaltingHashingMethod(substr($password, 1));
		}
		if (!$isSaltedHash) {
			$isSaltedHash = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::determineSaltingHashingMethod($password);
		}
		return $isSaltedHash;
	}

	/**
	 * Ein JWT (Json Web Token) erzeugen, signieren und `base64`-Encoded zurückgeben.
	 * 
	 * __Nicht vergessen:__ Ein JWT ist zwar "fälschungssicher", weil der Signatur-Hash nur mit 
	 * dem korrekten Key/Salt erzeugt werden kann – aber alle Daten im JWT sind für jeden
	 * durch `base64_decode()` einsehbar. Ein JWT eignet sich keinesfalls, um sensible Daten wie
	 * Passwörter oder Logins zu speichern!
	 * ```
	 * \nn\t3::Encrypt()->jwt(['test'=>123]);
	 * ```
	 * @param array $payload
	 * @return string
	 */
	public function jwt( $payload = [] ) {
		$header = [
			'alg' => 'HS256',
			'typ' => 'JWT',
		];
		$signature = $this->createJwtSignature($header, $payload);
		return join('.', [
			base64_encode(json_encode($header)),
			base64_encode(json_encode($payload)),
			base64_encode($signature)
		]);
	}

	/**
	 * Ein JWT (Json Web Token) parsen und die Signatur überprüfen.
	 * Falls die Signatur valide ist (und damit der Payload nicht manipuliert wurde), wird der
	 * Payload zurückgegeben. Bei ungültiger Signatur wird `FALSE` zurückgegeben.
	 * ```
	 * \nn\t3::Encrypt()->parseJwt('adhjdf.fsdfkjds.HKdfgfksfdsf');
	 * ```
	 * @param string $token
	 * @return array|false
	 */
	public function parseJwt( $token = '' ) {

		if (!$token) return false;
		if (substr($token, 0, 1) == '[') return false;
		
		$parts = explode('.', $token);
		if (count($parts) < 3) return false;
		
		$header = json_decode(base64_decode( array_shift($parts)), true);		
		$payload = json_decode(base64_decode( array_shift($parts)), true);
		$signature = base64_decode(array_shift($parts));
		
		$checkSignature = $this->createJwtSignature($header, $payload);
		if ($signature !== $checkSignature) return false;
		$payload['token'] = $token;
		
		return $payload;
	}

	/**
	 * Signatur für ein JWT (Json Web Token) erzeugen. 
	 * Die Signatur wird später als Teil des Tokens mit vom User übertragen.
	 * ```
	 * $signature = \nn\t3::Encrypt()->createJwtSignature(['alg'=>'HS256', 'typ'=>'JWT'], ['test'=>123]);
	 * ```
	 * @param array $header
	 * @param array $payload
	 * @return string
	 */
	public function createJwtSignature( $header = [], $payload = [] ) {
		return hash_hmac(
			'sha256',
			base64_encode(json_encode($header)) . '.' . base64_encode(json_encode($payload)), 
			$this->getSaltingKey()
		);
	}

}