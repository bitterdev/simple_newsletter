<?php /** @noinspection PhpUnused */

namespace Bitter\SimpleNewsletter\API\V1;

use Bitter\SimpleNewsletter\Service\MailingList;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class MailingLists
{
    protected MailingList $mailingListService;
    protected Request $request;

    public function __construct(
        MailingList $mailingListService,
        Request     $request
    )
    {
        $this->mailingListService = $mailingListService;
        $this->request = $request;
    }

    public function getAll(): JsonResponse
    {
        $editResponse = new EditResponse();
        $editResponse->setAdditionalDataAttribute("lists", $this->mailingListService->getListByCurrentSite());
        return new JsonResponse($editResponse);
    }
}