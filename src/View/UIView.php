<?php
declare(strict_types=1);

namespace DistrictUI\View;

use Cake\View\View;

/**
 * View class that keeps BootstrapUI helpers but uses the DistrictUI layout.
 *
 * @property \BootstrapUI\View\Helper\FlashHelper $Flash
 * @property \BootstrapUI\View\Helper\FormHelper $Form
 * @property \BootstrapUI\View\Helper\HtmlHelper $Html
 * @property \BootstrapUI\View\Helper\PaginatorHelper $Paginator
 * @property \BootstrapUI\View\Helper\BreadcrumbsHelper $Breadcrumbs
 */
class UIView extends View
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        if ($this->layout === 'default') {
            $this->layout = 'DistrictUI.default';
        }

        $helpers = [
            'Html' => ['className' => 'BootstrapUI.Html'],
            'Form' => ['className' => 'BootstrapUI.Form'],
            'Flash' => ['className' => 'BootstrapUI.Flash'],
            'Paginator' => ['className' => 'BootstrapUI.Paginator'],
            'Breadcrumbs' => ['className' => 'BootstrapUI.Breadcrumbs'],
        ];

        $this->helpers = array_merge($helpers, $this->helpers);
    }
}
