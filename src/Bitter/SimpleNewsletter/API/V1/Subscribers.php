<?php /** @noinspection PhpUnused */

namespace Bitter\SimpleNewsletter\API\V1;

use Bitter\SimpleNewsletter\Form\Service\Validation;
use Bitter\SimpleNewsletter\Service\Subscribe;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\JsonResponse;

class Subscribers
{
    protected Subscribe $subscribeService;
    protected Request $request;
    protected Validation $formValidator;
    protected Service $siteService;
    protected Site $site;
    protected Connection $db;

    public function __construct(
        Subscribe  $subscribeService,
        Validation $formValidator,
        Request    $request,
        Connection $db,
        Service    $siteService
    )
    {
        $this->subscribeService = $subscribeService;
        $this->formValidator = $formValidator;
        $this->request = $request;
        $this->db = $db;
        $this->siteService = $siteService;
        $this->site = $this->siteService->getActiveSiteForEditing();
    }

    public function subscribe(): JsonResponse
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();
        $subscribePage = null;

        $this->formValidator->setData($this->request->query->all());

        $this->formValidator->addRequired("email");
        $this->formValidator->addRequired("mailingListId");

        if ($this->formValidator->test()) {
            /** @noinspection PhpDeprecationInspection */
            $rows = $this->db->fetchAll(<<<EOL
SELECT
	cvb.cID
FROM
	BlockTypes AS bt
LEFT JOIN
	Blocks AS b ON (bt.btID = b.btID)
LEFT JOIN
	CollectionVersionBlocks AS cvb ON (cvb.bID = b.bID)
WHERE
	bt.btHandle = 'subscribe_form'
EOL
);

            if (isset($rows) && is_array($rows)) {
                foreach ($rows as $row) {
                    if (isset($row["cID"])) {
                        $c = Page::getByID($row["cID"]);

                        if ($c instanceof Page && !$c->isError()) {
                            if ($c->getSite() === $this->site) {
                                $subscribePage = $c;
                                break;
                            }
                        }
                    }
                }
            }

            if ($subscribePage instanceof Page) {
                $this->subscribeService->subscribe(
                    $this->request->query->get("email"),
                    [$this->request->query->get("mailingListId")],
                    Url::to($subscribePage, "confirm_subscription")
                );

                $editResponse->setTitle(t("Subscribed successfully."));
                $editResponse->setMessage(t("Please check your inbox and confirm your subscription by clicking the link we've just sent you."));
            } else {
                $errorList->add(t("No subscribe page set."));
            }
        } else {
            $errorList = $this->formValidator->getError();
        }

        $editResponse->setError($errorList);

        return new JsonResponse($editResponse);
    }
}