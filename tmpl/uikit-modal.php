<?php
/**
 * @package       Content - WT View PDF
 * @version       1.0.0
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (c) 2025 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

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

if (!$wa->assetExists('script', 'wt-pdf-js')) {
    // WT PDF.js web asset is not exists
    // Avoid error 500. Return an empty string.
    echo '';

    return;
}

$wa->useScript('wt-pdf-js')
        ->useScript('plg_content_wtviewpdf.uikit.modal');

if (!empty($params)) {
    $btn_text = $params->get('btn_text', 'PLG_CONTENT_WTVIEWPDF_BTN_TEXT');
}
?>
<button
        class="uk-button uk-button-default uk-margin-small-right"
        type="button"
        uk-toggle="target: #modal-wtviewpdf"
        data-file-url="<?php
        echo $filePath; ?>"
>
    <?php echo Text::_($btn_text); ?>
</button>

<?php
if ($first) : ?>
    <style>
        #modal-wtviewpdf .uk-modal-dialog {
            width: 100%;
            height: 100vh;
            margin: 0;
            max-width: none;
            display: flex;
            flex-direction: column;
        }

        #modal-wtviewpdf .uk-modal-body {
            flex-grow: 1;
            overflow-y: auto;
        }

        #modal-wtviewpdf .uk-modal-footer {
            background: #f8f8f8;
            border-top: 1px solid #e5e5e5;
            flex-shrink: 0;
        }

        .fullscreen-modal {
            width: 100vw;
            min-height: 100vh;
        }
    </style>

    <div id="modal-wtviewpdf" class="uk-modal-full fullscreen-modal" uk-modal>
        <div class="uk-modal-dialog">
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>

            <div class="uk-modal-body uk-padding-small">
                <div class="uk-flex uk-width-1-1 uk-height-1-1 uk-flex-center uk-flex-middle uk-background-default" id="modal-loader">
                    <div uk-spinner="ratio: 3"></div>
                </div>
                <div class="pdf-container d-flex flex-column ">

                </div>
            </div>

            <div class="uk-modal-footer uk-padding-small">
                <progress class="uk-progress" value="0" max="100"></progress>
            </div>
        </div>
    </div>
<?php endif; ?>
