<?php /** @noinspection PhpComposerExtensionStubsInspection */

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleNewsletter\Entity\Subscriber;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\MenuInterface;
use Bitter\SimpleNewsletter\Subscriber\Search\Result\Column;
use Bitter\SimpleNewsletter\Subscriber\Search\Result\Result;
use Bitter\SimpleNewsletter\Subscriber\Search\Result\Item;
use Bitter\SimpleNewsletter\Subscriber\Search\Result\ItemColumn;
use Bitter\SimpleNewsletter\Subscriber\Menu;

/** @var MenuInterface $menu */
/** @var Result $result */
/** @var DropdownMenu $resultsBulkMenu */

?>
    <div id="ccm-search-results-table">
        <table class="ccm-search-results-table" data-search-results="subscribers">
            <thead>
            <tr>
                <th class="ccm-search-results-bulk-selector">
                    <div class="btn-group dropdown">
                    <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input type="checkbox" data-search-checkbox="select-all"/>
                    </span>

                        <button
                                type="button"
                                disabled="disabled"
                                data-search-checkbox-button="dropdown"
                                class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                data-reference="parent">

                            <span class="sr-only">
                                <?php echo t("Toggle Dropdown"); ?>
                            </span>
                        </button>

                        <?php echo $resultsBulkMenu->getMenuElement(); ?>
                    </div>
                </th>

                <?php foreach ($result->getColumns() as $column): ?>
                    <?php /** @var Column $column */ ?>
                    <th class="<?php echo $column->getColumnStyleClass() ?>">
                        <?php if ($column->isColumnSortable()): ?>
                            <a href="<?php echo h($column->getColumnSortURL()) ?>">
                                <?php echo $column->getColumnTitle() ?>
                            </a>
                        <?php else: ?>
                            <span>
                            <?php echo $column->getColumnTitle() ?>
                        </span>
                        <?php endif; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($result->getItems() as $item) { ?>
                <?php
                /** @var Item $item */
                /** @var Subscriber $subscriber */
                $subscriber = $item->getItem();
                ?>
                <tr data-details-url="<?php echo Url::to('/dashboard/simple_newsletter/subscribers', 'update', $subscriber->getId()) ?>">
                    <td class="ccm-search-results-checkbox">
                        <?php if ($subscriber instanceof Subscriber) { ?>
                            <!--suppress HtmlFormInputWithoutLabel -->
                            <input data-search-checkbox="individual"
                                   type="checkbox"
                                   data-item-id="<?php echo $subscriber->getId() ?>"/>
                        <?php } ?>
                    </td>

                    <?php foreach ($item->getColumns() as $column) { ?>
                        <?php /** @var ItemColumn $column */ ?>
                        <?php if ($column->getColumnKey() == 'u.email') { ?>
                            <td class="ccm-search-results-name">
                                <?php echo $column->getColumnValue(); ?>
                            </td>
                        <?php } else { ?>
                            <td class="<?php echo $class ?? '' ?>">
                                <?php echo $column->getColumnValue(); ?>
                            </td>
                        <?php } ?>
                    <?php } ?>

                    <?php $menu = new Menu($subscriber); ?>

                    <td class="ccm-search-results-menu-launcher">
                        <div class="dropdown" data-menu="search-result">

                            <button class="btn btn-icon"
                                    data-boundary="viewport"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">

                                <svg width="16" height="4">
                                    <use xlink:href="#icon-menu-launcher"/>
                                </svg>
                            </button>

                            <?php echo $menu->getMenuElement(); ?>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="display: none">
        <div id="ccm-dialog-delete-subscriber" class="ccm-ui">
            <p>

            </p>

            <div class="dialog-buttons">
                <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()">

                </button>

                <button class="btn btn-danger float-end">
                    <?php echo t('Delete') ?>
                </button>
            </div>
        </div>
    </div>

    <!--suppress JSUnresolvedFunction -->
    <script>
        (function ($) {
            $(function () {
                let searchResultsTable = new window.ConcreteSearchResultsTable($("#ccm-search-results-table"));
                searchResultsTable.setupBulkActions();

                $(".ccm-delete-item").on("click", function (e) {
                    e.preventDefault();

                    let deleteUrl = $(this).attr("href");

                    $('<div></div>').appendTo('body')
                        .html('<p>' + <?php echo json_encode(t('Are you sure?')) ?> + '</p>')
                        .dialog({
                            modal: true,
                            title: <?php echo json_encode(t('Confirm')) ?>,
                            zIndex: 10000,
                            autoOpen: true,
                            width: 'auto',
                            resizable: false,
                            buttons: {
                                'cancel': {
                                    text: <?php echo json_encode(t('Cancel')) ?>,
                                    class: 'btn btn-secondary float-start',
                                    click: function() {
                                        $(this).dialog("close");
                                    }
                                },
                                'delete': {
                                    text: <?php echo json_encode(t('Delete')) ?>,
                                    class: 'btn btn-danger float-end',
                                    click: function() {
                                        window.location.href = deleteUrl;

                                        $(this).dialog("close");
                                    }
                                }
                            },
                            close: function(event, ui) {
                                $(this).remove();
                            }
                        });

                    return false;
                });
            });
        })(jQuery);
    </script>

<?php echo $result->getPagination()->renderView('dashboard'); ?>
