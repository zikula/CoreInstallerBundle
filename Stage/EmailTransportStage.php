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
use Zikula\Bundle\CoreInstallerBundle\Helper\ParameterHelper;
use Zikula\Component\Wizard\AbortStageException;
use Zikula\Component\Wizard\FormHandlerInterface;
use Zikula\Component\Wizard\StageInterface;
use Zikula\MailerModule\Form\Type\MailTransportConfigType;
use Zikula\MailerModule\Helper\MailTransportHelper;

class EmailTransportStage implements StageInterface, FormHandlerInterface
{
    /**
     * @var ParameterHelper
     */
    private $parameterHelper;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var string
     */
    private $mailerDsn;

    public function __construct(
        ParameterHelper $parameterHelper,
        string $projectDir,
        string $mailerDsn = ''
    ) {
        $this->parameterHelper = $parameterHelper;
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
        try {
            $this->parameterHelper->writeEncodedParameters($form->getData());
        } catch (AbortStageException $exception) {
            return false;
        }

        return (new MailTransportHelper($this->projectDir))->handleFormData($form->getData());
    }
}
