<?php

namespace Nng\Nnhelpers\Utilities;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Messaging\FlashMessageRendererResolver;

/**
 * Vereinfacht die Verwendung der FlashMessages.
 * 
 * Im Backend: FlashMessages werden automatisch ganz oben ausgegeben
 * ```
 * \nn\t3::Message()->OK('Titel', 'Infotext');
 * \nn\t3::Message()->ERROR('Titel', 'Infotext');
 * ```
 * Im Frontend: FlashMessages können über ViewHelper ausgegeben werden
 * ```
 * \nn\t3::Message()->OK('Titel', 'Infotext');
 * <nnt3:flashMessages />
 * <f:flashMessages queueIdentifier='core.template.flashMessages' />
 * 
 * \nn\t3::Message()->setId('oben')->OK('Titel', 'Infotext');
 * <nnt3:flashMessages id='oben' />
 * <f:flashMessages queueIdentifier='oben' />
 * ````
 * ... oder als HTML gerendert und zurückgegeben werden:
 * ```
 * echo \nn\t3::Message()->render('oben');
 * echo \nn\t3::Message()->render();
 * ```
 */
class Message implements SingletonInterface {    

	/**
     * @var string
     */
	protected $queueId;

	/**
	 * 	Legt fest, welcher MessageQueue verwendet werden soll
	 *	```
	 *	\nn\t3::Message()->setId('oben')->OK('Titel', 'Infotext');
	 *	```
	 *	Ausgabe in Fluid per ViewHelper:
	 *	```
	 *	<nnt3:flashMessages id="oben" />
	 *	{nnt3:flashMessages(id:'oben')}
	 *	```
	 * 	@return void
	 */
    public function setId( $name = null ) {
		$this->queueId = $name;
		return $this;
	}

	/**
	 * 	Gibt eine "OK" Flash-Message aus
	 *	```
	 *	\nn\t3::Message()->OK('Titel', 'Infotext');
	 *	```
	 * 	@return void
	 */
    public function OK( $title = '', $text = '' ) {
		$this->flash( $title, $text );
	}

	/**
	 * 	Gibt eine "ERROR" Flash-Message aus
	 *	```
	 *	\nn\t3::Message()->ERROR('Titel', 'Infotext');
	 *	```
	 * 	@return void
	 */
    public function ERROR( $title = '', $text = '' ) {
		$this->flash( $title, $text, 'ERROR' );
	}
	
	/**
	 * 	Gibt eine "WARNING" Flash-Message aus
	 *	```
	 *	\nn\t3::Message()->WARNING('Titel', 'Infotext');
	 *	```
	 * 	@return void
	 */
    public function WARNING( $title = '', $text = '' ) {
		$this->flash( $title, $text, 'WARNING' );
	}

	/**
	 * 	Speichert eine Flash-Message in den Message-Queue für Frontend oder Backend.
	 * 	@return void
	 */
    public function flash( $title = '', $text = '', $type = 'OK', $queueID = null ) 
	{
		$message = GeneralUtility::makeInstance(FlashMessage::class,
			$text,
			$title,
			constant("TYPO3\CMS\Core\Type\ContextualFeedbackSeverity::{$type}"),
			true
		);
		$flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
		$queueID = $queueID ?: $this->queueId ?: 'core.template.flashMessages';
		$messageQueue = $flashMessageService->getMessageQueueByIdentifier( $queueID );
		$messageQueue->addMessage($message);
	}

	/**
	 * 	Rendert die Flash-Messages in der Queue
	 *	Simples Beispiel:
	 *	```
	 *	\nn\t3::Message()->OK('Ja', 'Nein');
	 *	echo \nn\t3::Message()->render();
	 *	```
	 *	Beispiel mit einer Queue-ID:
	 *	```
	 *	\nn\t3::Message()->setId('oben')->OK('Ja', 'Nein');
	 *	echo \nn\t3::Message()->render('oben');
	 *	```
	 *	Ausgabe im Fluid über den ViewHelper:
	 *	```
	 *	<nnt3:flashMessages id="oben" />
	 *	{nnt3:flashMessages()}
	 *	```
	 * 	@return string
	 */
	public function render( $queueID = null ) {
		if (!($messages = $this->flush($queueID))) return '';
		$html = GeneralUtility::makeInstance(FlashMessageRendererResolver::class)->resolve()->render($messages);
		return $html;
	}

	/**
	 * 	Löscht alle Flash-Messages
	 * 	Optional kann eine Queue-ID angegeben werden.
	 *	```
	 *	\nn\t3::Message()->flush('oben');
	 *	\nn\t3::Message()->flush();
	 *	```
	 * 	@return array
	 */
	public function flush( $queueID = null ) {
		$flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
		$queueID = $queueID ?: $this->queueId ?: 'core.template.flashMessages';
		$messageQueue = $flashMessageService->getMessageQueueByIdentifier( $queueID );
		return $messageQueue->getAllMessagesAndFlush() ?: [];
	}

}