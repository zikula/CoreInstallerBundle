<?php

declare(strict_types=1);

/*
 * This file is part of the Zikula package.
 *
 * Copyright Zikula - https://ziku.la/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zikula\Bundle\CoreInstallerBundle\Stage;

use Symfony\Component\Form\FormInterface;
use Zikula\Component\Wizard\FormHandlerInterface;
use Zikula\Component\Wizard\StageInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MailerModule\Form\Type\MailTransportConfigType;
use Zikula\MailerModule\Helper\MailTransportHelper;

class EmailTransportStage implements StageInterface, FormHandlerInterface
{
    /**
     * @var VariableApiInterface
     */
    private $variableApi;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $mailerDsn;

    public function __construct(
        VariableApiInterface $variableApi,
        string $projectDir,
        string $mailerDsn = ''
    ) {
        $this->variableApi = $variableApi;
        $this->projectDir = $projectDir;
        $this->mailerDsn = $mailerDsn;
    }

    public function getName(): string
    {
        return 'emailtransport';
    }

    public function getFormType(): string
    {
        return MailTransportConfigType::class;
    }

    public function getFormOptions(): array
    {
        return [];
    }

    public function getTemplateName(): string
    {
        return '@ZikulaCoreInstaller/Install/mailer.html.twig';
    }

    public function isNecessary(): bool
    {
        $mailerDsn = $this->mailerDsn;
        if (!empty($mailerDsn) && 'smtp://localhost' !== $mailerDsn) {
            return false;
        }

        return true;
    }

    public function getTemplateParams(): array
    {
        return [];
    }

    public function handleFormResult(FormInterface $form): bool
    {
        $formData = $form->getData();
        $this->variableApi->set('ZikulaMailerModule', 'enableLogging', $formData['enableLogging']);
        $this->variableApi->set('ZikulaMailerModule', 'transport', $formData['transport']);
        $this->variableApi->set('ZikulaMailerModule', 'mailer_id', $formData['mailer_id']);

        return (new MailTransportHelper($this->projectDir))->handleFormData($formData);
    }
}
