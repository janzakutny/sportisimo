<?php

declare(strict_types=1);

namespace App\Components;

use Contributte\Translation\Translator;
use Kdyby\Autowired\AutowireComponentFactories;
use Nette;
use Nette\Application\UI\Template;
use stdClass;

abstract class BaseControl extends Nette\Application\UI\Control
{
    use AutowireComponentFactories;

    /**
     * @var Translator
     * @inject
     */
    public $translator;

    /**
     * @var Nette\Http\Request
     * @inject
     */
    public $httpRequest;

    /** @var bool automatically derive template path from class name */
    protected $autoSetupTemplateFile = true;

    /** @var string|null */
    protected $fileTemplate;

    /** @var bool */
    private $ajaxMode;


    protected function createTemplate(): Template
    {
        $template = parent::createTemplate(/*string $class = null*/);

        if ($this->autoSetupTemplateFile) {
            $template->setFile($this->getTemplateFilePath());
        }

        $template->lang = $this->translator->getLocale();

        return $template;
    }


    /**
     * Derives template path from class name.
     *
     * @return   string
     */
    protected function getTemplateFilePath($filename = null)
    {
        $reflection = $this->getReflection();
        $filename = ($filename ?: lcfirst($this->fileTemplate ?? $reflection->getShortName())) . '.latte';

        return dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR . $filename;
    }


    public function flashMessage($message, string $type = 'info', $count = null, array $param = []): stdClass
    {
        return parent::flashMessage($this->translator->translate($message, $count, $param), $type);
    }


    /**
     * Is AJAX request?
     *
     * @return bool
     */
    protected function isAjax()
    {
        if ($this->ajaxMode === null) {
            $this->ajaxMode = $this->httpRequest->isAjax();
        }
        return $this->ajaxMode;
    }
}
