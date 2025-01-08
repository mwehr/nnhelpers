<?php

namespace  Nng\Nnhelpers\ViewHelpers\Video;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Konvertiert eine youTube-URL in die watch-Variante, z.B. für die Einbindung in ein iFrame.
 * ```
 * {my.videourl->nnt3:video.embedUrl()}
 * ```
 * ```
 * <iframe src="{my.videourl->nnt3:video.embedUrl()}"></iframe>
 * ```
 */
class EmbedUrlViewHelper extends AbstractViewHelper {

	use CompileWithRenderStatic;

	protected $escapeOutput = false;

	public function initializeArguments() {
	   $this->registerArgument('url', 'string', 'URL zu youtube', false, '');
   }

	public static function renderStatic( array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext ) {
		$url = $arguments['url'];
		if (!$url) $url = $renderChildrenClosure();
		return \nn\t3::Video()->getEmbedUrl( $url );
	}

}