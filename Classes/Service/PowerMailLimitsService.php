<?php

namespace Jpmschuler\PowermailLimits\Service;

use Jpmschuler\PowermailLimits\Domain\Model\FormWithSubmissionLimit;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PowerMailLimitsService
{
    public const EXTENSION_KEY = 'powermail_limits';

    public function getNewMailMessageWithSubjectPrefix(FormWithSubmissionLimit $form, MailMessage $message): MailMessage
    {
        $subjectPrefix = '';

        if ($form->isNewSubmissionForWaitlistMailManipulation()) {
            $subjectPrefix = $this->getTranslation('mail.waitinglistsubmission.subjectprefix');
        } elseif (!$form->isNewSubmissionValid()) {
            $subjectPrefix = $this->getTranslation('mail.invalidsubmission.subjectprefix');
        }

        $subject = $subjectPrefix . $message->getSubject();

        return $message->setSubject($subject);
    }

    public function getNewViewWithBodyPrefix(
        FormWithSubmissionLimit $form,
        ViewInterface $view,
        array $email
    ): ViewInterface {
        $bodyPrefixHeader = '';
        $bodyPrefixText = '';

        if ($form->isNewSubmissionForWaitlistMailManipulation()) {
            $bodyPrefixHeader = $this->getTranslation('mail.waitinglistsubmission.bodyprefix.header');
            $bodyPrefixText = $this->getTranslation('mail.waitinglistsubmission.bodyprefix.text');
            $bodyPrefix = sprintf('<p><strong>%s</strong></p><p>%s</p><hr>', $bodyPrefixHeader, $bodyPrefixText);
            $view->assign('powermail_limits_waitlist_active', $bodyPrefix);
        } elseif (!$form->isNewSubmissionValid()) {
            $bodyPrefixHeader = $this->getTranslation('mail.invalidsubmission.bodyprefix.header');
            $bodyPrefixText = $this->getTranslation('mail.invalidsubmission.bodyprefix.text');
            $bodyPrefix = sprintf('<p><strong>%s</strong></p><p>%s</p><hr>', $bodyPrefixHeader, $bodyPrefixText);
            $view->assign('powermail_limits_limit_reached', $bodyPrefix);
        }

        if (isset($bodyPrefix)) {
            $email['rteBody'] = $bodyPrefix . $email['rteBody'];
            $view->assign('powermail_rte', $email['rteBody']);
        }

        return $view;
    }

    private function getTranslation(string $key): ?string
    {
        return LocalizationUtility::translate($key, 'PowermailLimits');
    }
}
