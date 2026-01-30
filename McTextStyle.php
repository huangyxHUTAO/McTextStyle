<?php
/**
 * 在 Wikitext 解析管线内安全地把 §bText§r 染成彩色
 */

require_once __DIR__ . '/utils.php';
use McTextStyle\Utils;

$GLOBALS['wgExtensionFunctions'][] = function() {
    $container = \MediaWiki\MediaWikiServices::getInstance()->getHookContainer();
    $container->register( 'GetMagicWordIDs', 'McTextStyleHooks::onGetMagicWordIDs' );
    $container->register( 'ParserFirstCallInit', 'McTextStyleHooks::onParserFirstCallInit' );
};

class McTextStyleHooks
{
	/** 绑定解析器函数 */
	public static function onParserFirstCallInit(\Parser $parser)
	{
		$parser->setFunctionHook(
			'mctextstyle',
			[__CLASS__, 'renderMcText'],
			Parser::SFH_OBJECT_ARGS
		);
		return true;
	}


	/**
	 * 核心：把 § 代码换成 <span> 并让 MW 再走一次内部解析
	 */
	public static function renderMcText(\Parser $parser, \PPFrame $frame, $args)
	{
		$raw = isset($args[0]) ? trim($frame->expand($args[0])) : '';

		$wikitext = Utils::mcTextToHtml($raw);

		// 内部解析 经过Sanitizer 以确保安全
		$html = $parser->recursiveTagParse($wikitext, $frame);

		return [$html, 'noparse' => true, 'isHTML' => true];
	}
}