<?php
/**
 * @package       Content - WT View PDF
 * @version       1.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (c) 2022-2025 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/** @var array $displayData */

extract($displayData);

/** @var string $filePath */

$app = Factory::getApplication();
$document = $app->getDocument();
$wa = $document->getWebAssetManager();

$wa->useScript('wt-pdf-js');
$wa->useScript('plg_content_wtviewpdf.default');
?>

<div class="wtviewpdf-default"
     data-file-url="<?php echo $filePath; ?>"
>
    <div class="pdf-container">

    </div>
</div>