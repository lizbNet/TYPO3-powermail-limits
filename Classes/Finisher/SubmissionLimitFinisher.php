<?php

namespace Jpmschuler\PowermailLimits\Finisher;

use In2code\Powermail\Finisher\AbstractFinisher;
use Jpmschuler\PowermailLimits\Domain\Model\FormWithSubmissionLimit;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class DoSomethingFinisher
 */
class SubmissionLimitFinisher extends AbstractFinisher
{
    public function checkSubmissionLimitFinisher(): void
    {
        $mail = $this->getMail();
        /** @var FormWithSubmissionLimit $form */
        $form = $mail->getForm();

        // the notice goes to the form's receiver; a form without one (or a list of several) has
        // nobody to tell, and new Address('') throws, which turned the thank-you page into an error.
        // A hidden sign-up (waiting list, or saved hidden by another extension) doesn't change the
        // visible count, so it isn't the one that reached the limit.
        $receiver = trim($mail->getReceiverMail());
        if ($mail->getHidden() || filter_var($receiver, FILTER_VALIDATE_EMAIL) === false) {
            return;
        }
        if ($form->isLastSubmissionAllowed()) {
            $recipient = new Address($receiver);
            $subject = sprintf(
                LocalizationUtility::translate('mail.submissionlimitreached.subject', 'PowermailLimits'),
                $form->getTitle()
            );
            $body = sprintf(
                LocalizationUtility::translate('mail.submissionlimitreached.body', 'PowermailLimits'),
                $form->getTitle()
            );

            $emailLimitReached = GeneralUtility::makeInstance(MailMessage::class);
            $emailLimitReached
                ->from($recipient)
                ->to($recipient)
                ->subject($subject)
                ->text($body)
                ->send();
        }
    }
}
