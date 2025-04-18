<?php
/**
 * @package       Content - WT View PDF
 * @version       1.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (c) 2025 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */
namespace Joomla\Plugin\Content\WtViewPdf\Extension;

use Exception;
use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use function defined;
use function is_null;
use function is_object;
use function strlen;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * @since 1.0.0
 *
 * @noinspection PhpUnused
 */
final class WtViewPdf extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var boolean
	 *
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return  array
	 *
	 * @since   1.2.0
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'onContentPrepare' => 'onContentPrepare',
		];
	}

	/**
	 * @param   ContentPrepareEvent  $event  Event
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since        1.0.0
	 *
	 * @noinspection PhpUnused
	 */
	public function onContentPrepare(ContentPrepareEvent $event): void
	{
		$context = $event->getContext();
		$item = $event->getItem();

		if (!is_object($item) || !property_exists($item, 'text') || is_null($item->text))
		{
			return;
		}

		$regex = '/\{PDF(?:\s+tmpl=([^\s\}]+))?\}(.*?)\{\/PDF\}/s';

		// Remove macros and don't run this plugin when the content is being indexed
		if ($context === 'com_finder.indexer')
		{
			if (str_contains($item->text, '{PDF'))
			{
				$item->text = preg_replace($regex, '', $item->text);
			}

			return;
		}

		if (str_contains($item->text, '{PDF '))
		{
			$defaultTmpl = $this->params->get('default_tmpl', 'default');

			$tmpls = Folder::files(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/tmpl', '\.php$');
			$tmpls = array_map(
				function(string $value) {
					return File::stripExt($value);
				},
				$tmpls
			);

			preg_match_all($regex, $item->text, $matches, PREG_SET_ORDER);

			if ($matches)
			{
				foreach ($matches as $match)
				{
					$tmpl = !empty($match[1]) ? $match[1] : $defaultTmpl;

					if (!in_array($tmpl, $tmpls))
					{
						$tmpl = $defaultTmpl;
					}

					$filePath = trim($match[2]);
					$html = $this->getHtml($tmpl, $filePath);

					if (($start = strpos($item->text, $match[0])) !== false)
					{
						$item->text = substr_replace($item->text, $html, $start, strlen($match[0]));
					}
				}
			}
		}
	}

	/**
	 * @param   string  $tmpl      Tmpl
	 * @param   string  $filePath  The file path
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   1.0
	 */
	protected function getHtml(string $tmpl, string $filePath): string
	{
		$app = Factory::getApplication();

		try
		{
			static $first = true;
			$html = LayoutHelper::render(
				$tmpl,
				[
					'filePath'  => $filePath,
					'first'     => $first,
				],
				JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/tmpl'
			);
			$first = false;
		}
		catch (Exception $e)
		{
			if ($app->get('debug'))
			{
				throw $e;
			}
			else
			{
				return '';
			}
		}

		return $html;
	}
}
