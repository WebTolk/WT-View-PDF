<?php
/**
 * @package    Content - WT View PDF
 * @version       1.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (c) 2025 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/** @var array $displayData */

extract($displayData);

/**
 * @var string                        $filePath Path to PDF file
 * @var bool                          $first    Is this first iteration or not?
 * @var Joomla\Registry\Registry|null $params   The plugin params
 */

$app      = Factory::getApplication();
$document = $app->getDocument();
$wa       = $document->getWebAssetManager();

if(!$wa->assetExists('script', 'wt-pdf-js')) {
    // WT PDF.js web asset is not exists
    // Avoid error 500. Return an empty string.
    echo '';
    return;
}

$wa->useScript('wt-pdf-js')
        ->useScript('plg_content_wtviewpdf.default');

if(!empty($params))
{
    $btn_text = $params->get('btn_text', 'PLG_CONTENT_WTVIEWPDF_BTN_TEXT');
}
//  echo Text::_($btn_text);
?>

<div class="wtviewpdf-default"
     data-file-url="<?php
     echo $filePath; ?>"
>
    <div class="pdf-container">

    </div>
</div>