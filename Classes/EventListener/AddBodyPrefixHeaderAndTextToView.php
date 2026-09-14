<?php

namespace Jpmschuler\PowermailLimits\EventListener;

use In2code\Powermail\Events\SendMailServiceCreateEmailBodyEvent;
use Jpmschuler\PowermailLimits\Domain\Model\FormWithSubmissionLimit;
use Jpmschuler\PowermailLimits\Service\PowerMailLimitsService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class AddBodyPrefixHeaderAndTextToView
{
    public function __construct(
        private readonly PowerMailLimitsService $powerMailLimitsService,
    ) {
    }

    public function __invoke(SendMailServiceCreateEmailBodyEvent $event): void
    {
        $originalService = $event->getSendMailService();
        $view = $event->getView();
        $email = $event->getEmail();

        $form = $originalService->getMail()->getForm();

        if (!$form instanceof FormWithSubmissionLimit) {
            return;
        }

        $view = $this->powerMailLimitsService->getNewViewWithBodyPrefix($form, $view, $email);
        $event->setView($view);
    }
}
