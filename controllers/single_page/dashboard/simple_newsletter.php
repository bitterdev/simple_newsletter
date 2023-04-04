<?php

/**
 * @project:   Simple Newsletter
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2023 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;

class SimpleNewsletter extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function view()
    {
        return $this->responseFactory->redirect(Url::to("/dashboard/simple_newsletter/settings"), Response::HTTP_TEMPORARY_REDIRECT);
    }

}
