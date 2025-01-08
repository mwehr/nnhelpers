<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Alles, was zum Thema Videos wichtig und hilfreich ist.
 */
class Video implements SingletonInterface {
   
	static $VIDEO_PREGS = array(
		'youtube'	=> array(
			array( 1 => '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i') 
		),
		'vimeo'		=> array(
			array( 2 => '/vimeo\.com\/(video\/)*([0-9]+)/i')
		)
	);

	/**
	 * Prüft, ob es sich bei der URL um ein externes Video auf YouTube oder Vimeo handelt.
	 * Gibt ein Array mit Daten zum Einbetten zurück.
	 * ```
	 * \nn\t3::Video()->isExternal( 'https://www.youtube.com/...' );
	 * ```
	 * @return array
	 */
	public function isExternal ( $url = null ) {
		return $this->getExternalType( $url );
	}
	

	/**
	 * Gibt ein Array mit Infos über die Streaming-Platform und Code zum Einbetten eines Videos zurück.
	 * ```
	 * \nn\t3::Video()->getExternalType( 'https://www.youtube.com/watch/abTAgsdjA' );
	 * ```
	 * @return array
	 */
	public function getExternalType( $url = null ) {
		foreach (self::$VIDEO_PREGS as $type=>$pregs) {
			foreach ($pregs as $cnt => $arr) {
				foreach ($arr as $index => $preg) {
					if (preg_match($preg, $url, $match)) {
						return [
							'type'		=> $type, 
							'videoId'	=> $match[$index],
							'embedUrl'	=> $this->getEmbedUrl($type, $match[$index]),
							'watchUrl'	=> $this->getWatchUrl($type, $match[$index]),
						];
					}
				}
			}
		}
		return [];
	}
	
	/**
	 * Einbettungs-URL anhand der Streaming-Plattform zurückgeben.
	 * Klassischerweise die URL, die im src-Attribut des <iframe>
	 * verwendet wird.
	 * ```
	 * \nn\t3::Video()->getEmbedUrl( 'youtube', 'nShlloNgM2E' );
	 * \nn\t3::Video()->getEmbedUrl( 'https://www.youtube.com/watch?v=wu55ZG97zeI&feature=youtu.be' );
	 * ```
	 * Existiert auch als ViewHelper:
	 * ```
	 * {my.videourl->nnt3:video.embedUrl()}
	 * ```
	 * @return string
	 */
	public function getEmbedUrl ($type, $videoId = null) {
		if (!$videoId && strpos($type, 'http') !== false) {
			$infos = $this->isExternal( $type );
			return $infos['embedUrl'];
		}
		switch ($type) {
			case 'youtube':
				return 'https://www.youtube-nocookie.com/embed/'.$videoId;
			case 'vimeo':
				return 'https://player.vimeo.com/video/'.$videoId;
		}
	}
	
	/**
	 * 	Link-URL zum Video auf der externen Plattform
	 * 	z.B. um einen externen Link zum Video darzustellen 
	 *	```
	 *	\nn\t3::Video()->getWatchUrl( $fileReference );
	 *	\nn\t3::Video()->getWatchUrl( 'youtube', 'nShlloNgM2E' );
	 *	\nn\t3::Video()->getWatchUrl( 'https://www.youtube.com/watch?v=wu55ZG97zeI&feature=youtu.be' );
	 *
	 *	// => https://www.youtube-nocookie.com/watch?v=kV8v2GKC8WA
	 *	```
	 * 	@return string
	 */
	public function getWatchUrl ($type, $videoId = null ) {
		if (\nn\t3::Obj()->isFileReference($type)) {
			$type = $type->getOriginalResource()->getPublicUrl();
		}
		if (!$videoId && strpos($type, 'http') !== false) {
			$infos = $this->isExternal( $type );
			return $infos['watchUrl'];
		}
		switch ($type) {
			case 'youtube':
				return 'https://www.youtube-nocookie.com/watch?v='.$videoId;
			case 'vimeo':
				return 'https://vimeo.com/'.$videoId;
		}
	}

}