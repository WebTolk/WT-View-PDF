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

/**
 * @var string   $filePath
 * @var boolean  $first
 */

$app = Factory::getApplication();
$document = $app->getDocument();
$wa = $document->getWebAssetManager();

$wa->useScript('wt-pdf-js');
$wa->useScript('bootstrap.modal');
$wa->useScript('plg_content_wtviewpdf.bootstrap.modal');
?>
<button type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#wtviewpdf"
        data-file-url="<?php echo $filePath; ?>"
>
	<?php echo $filePath; ?>
</button>

<?php if ($first) { ?>
<aside class="modal fade"
       id="wtviewpdf"
       tabindex="-1"
       aria-labelledby="wtviewpdf_btn"
       aria-hidden="true"
>
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header py-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-y-scroll py-0">
                <div class="d-flex w-100 h-100 justify-content-center align-items-center bg-white z-1" id="modal-loader">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="pdf-container d-flex flex-column ">

                </div>
            </div>
            <div class="modal-footer">
                <div class="progress w-100"
                     role="progressbar"
                     aria-label="PDF view progress bar"
                     aria-valuenow="25"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     style="height: 1px"
                >
                    <div class="progress-bar"></div>
                </div>
            </div>
        </div>
    </div>
</aside>
<?php } ?>
