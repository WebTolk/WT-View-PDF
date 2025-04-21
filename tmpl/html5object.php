<?php
/**
 * @package    Content - WT View PDF
 * @version       1.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (c) 2025 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var array $displayData */

extract($displayData);

/**
 * @var string                        $filePath Path to PDF file
 * @var bool                          $first    Is this first iteration or not?
 * @var Joomla\Registry\Registry|null $params   The plugin params
 */

if(!empty($params))
{
    $btn_text = $params->get('btn_text', 'PLG_CONTENT_WTVIEWPDF_BTN_TEXT');
}
 //echo Text::_($btn_text);
?>

<object class="wtviewpdf-html5object" data="<?php echo $filePath;?>" type="application/pdf" width="100%" height="720">
</object>