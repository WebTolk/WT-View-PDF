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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

use function defined;
use function is_file;
use function is_null;
use function is_object;
use function strlen;

defined('_JEXEC') or die;

/**
 * @since        1.0.0
 */
final class WtViewPdf extends CMSPlugin implements SubscriberInterface
{
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
     */
    public function onContentPrepare(ContentPrepareEvent $event): void
    {
        $context = $event->getContext();
        $item    = $event->getItem();

        if (!is_object($item) ||
                !property_exists($item, 'text') ||
                is_null($item->text) ||
                !str_contains($item->text, '{PDF')) {
            return;
        }

        $regex = '/\{PDF(?:\s+tmpl=([^\s\}]+))?\}(.*?)\{\/PDF\}/s';

        // Remove macros and don't run this plugin when the content is being indexed
        if ($context==='com_finder.indexer') {
            $item->text = preg_replace($regex, '', $item->text);
            return;
        }

        $defaultTmpl = $this->params->get('default_tmpl', 'default');
        preg_match_all($regex, $item->text, $matches, PREG_SET_ORDER);

        if ($matches) {
            foreach ($matches as $match) {
                $tmpl     = !empty($match[1]) ? $match[1] : $defaultTmpl;
                $filePath = trim($match[2]);
                $html = $this->getHtml($filePath, $tmpl);

                $item->text = str_replace($match[0], $html, $item->text);
                if (property_exists($item, 'introtext') && !empty($item->introtext))
                {
                    $item->introtext = str_replace($match[0], $html, $item->introtext);
                }
                if (property_exists($item, 'fulltext') && !empty($item->fulltext))
                {
                    $item->fulltext = str_replace($match[0], $html, $item->fulltext);
                }
            }
        }
    }

    /**
     * Render HTML for PDF
     *
     * @param   string  $tmpl      Layout to be rendered
     * @param   string  $filePath  The file path to pdf
     *
     * @return  string
     *
     * @since   1.0.0
     */
    protected function getHtml(string $filePath, string $tmpl = 'default'): string
    {
        if(!is_file(JPATH_SITE.'/'.$filePath)) {
            return '';
        }

        $layoutPath = JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/tmpl';

        if (!is_file($layoutPath . '/' . $tmpl . '.php')) {
            $tmpl = 'default';
        }

        static $first = true;
        $html  = LayoutHelper::render(
                $tmpl,
                [
                        'filePath' => $filePath,
                        'first'    => $first,
                ],
                $layoutPath
        );
        $first = false;

        return $html;
    }
}
