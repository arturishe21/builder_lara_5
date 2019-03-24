<?php

namespace Vis\Builder;

use Vis\Builder\Handlers\ActionsHandler;
use Vis\Builder\Handlers\ButtonsHandler;
use Vis\Builder\Handlers\CustomClosureHandler;
use Vis\Builder\Handlers\ExportHandler;
use Vis\Builder\Handlers\ImportHandler;
use Vis\Builder\Handlers\QueryHandler;
use Vis\Builder\Handlers\RequestHandler;
use Vis\Builder\Handlers\ViewHandler;
use Vis\Builder\Services\DefinitionLoader;

class JarboeController
{
    /**
     * @var CustomClosureHandler
     */
    protected $callbacks;
    protected $currentID = false;
    protected $options;
    protected $definition;
    protected $handler;
    protected $fields;
    protected $groupFields;
    protected $patterns = [];
    protected $allowedIds;

    /**
     * @var ViewHandler
     */
    public $view;
    /**
     * @var RequestHandler
     */
    public $request;
    /**
     * @var QueryHandler
     */
    public $query;
    /**
     * @var ActionsHandler
     */
    public $actions;
    /**
     * @var ExportHandler
     */
    public $export;
    /**
     * @var ImportHandler
     */
    public $import;
    /**
     * @var ButtonsHandler
    */
    public $buttons;
    public $imageStorage;
    public $fileStorage;

    public static function init($options)
    {
        return new static($options);
    }

    public function __construct($page)
    {
        $this->definition = DefinitionLoader::load($page);
        $this->handler = $this->definition->getCustomHandler();

        if ($this->definition->hasCallbacks()) {
            $this->callbacks = new CustomClosureHandler($this->definition->getCallbacks(), $this);
        }

        $this->fields = $this->loadFields();
        $this->groupFields = $this->loadGroupFields();

        $this->actions = new ActionsHandler($this->definition->getActions(), $this);

        if ($this->definition->hasExportButtons()) {
            $this->export = new ExportHandler($this->definition->getExportButtons(), $this);
        }

        if ($this->definition->hasImportButtons()) {
            $this->import = new ImportHandler($this->definition->getImportButtons(), $this);
        }

        if ($this->definition->hasButtons()) {
            $this->buttons = new ButtonsHandler($this->definition->getButtons(), $this);
        }

        $this->query = new QueryHandler($this);
        $this->view = new ViewHandler($this);
        $this->request = new RequestHandler($this);

        $this->currentID = request('id');
    }

    public function getCurrentID()
    {
        return $this->currentID;
    }

    public function getModel()
    {
        return $this->definition->getModel();
    }

    public function getTable()
    {
        return $this->definition->getModel()->getTable();
    }

    public function handle()
    {
        if ($this->hasCustomHandlerMethod('handle')) {
            $res = $this->getCustomHandler()->handle();
            if ($res) {
                return $res;
            }
        }

        return $this->request->handle();
    }

    public function isAllowedID($id)
    {
        return in_array($id, $this->allowedIds);
    }

    public function hasCustomHandlerMethod($methodName)
    {
        return $this->getCustomHandler() && is_callable([$this->getCustomHandler(), $methodName]);
    }

    public function getCustomHandler()
    {
        return $this->handler ?: $this->callbacks;
    }

    public function getField($ident)
    {
        if (isset($this->fields[$ident])) {
            return $this->fields[$ident];
        } elseif (isset($this->patterns[$ident])) {
            return $this->patterns[$ident];
        } elseif (isset($this->groupFields[$ident])) {
            return $this->groupFields[$ident];
        }

        throw new \RuntimeException("Field [{$ident}] does not exist for current scheme.");
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    protected function loadFields()
    {
        $definitionThis = $this->getDefinition();

        $fieldsThis = [];

        if (! isset($definitionThis['fields'])) {
            return $fieldsThis;
        }

        foreach ($definitionThis['fields'] as $name => $info) {
            if ($this->isPatternField($name)) {
                $this->patterns[$name] = $this->createPatternInstance($name, $info);
            } else {
                $fieldsThis[$name] = $this->createFieldInstance($name, $info);
            }
        }

        return $fieldsThis;
    }

    protected function loadGroupFields()
    {
        $definitionThis = $this->getDefinition();
        $fieldsThis = [];

        if (! isset($definitionThis['fields'])) {
            return $fieldsThis;
        }

        foreach ($definitionThis['fields'] as $info) {
            if ($info['type'] == 'group' && count($info['filds'])) {
                foreach ($info['filds'] as $nameGroup => $infoGroup) {
                    $fieldsThis[$nameGroup] = $this->createFieldInstance($nameGroup, $infoGroup);
                }
            }
        }

        return $fieldsThis;
    }

    public function getPatterns()
    {
        return $this->patterns;
    }

    public function isPatternField($name)
    {
        return preg_match('~^pattern\.~', $name);
    }

    protected function createPatternInstance($name, $info)
    {
        return new Fields\PatternField(
            $name,
            $info,
            $this->options,
            $this->getDefinition(),
            $this->getCustomHandler()
        );
    }

    protected function createFieldInstance($name, $info)
    {
        $className = 'Vis\\Builder\\Fields\\'.ucfirst(camel_case($info['type'])).'Field';

        return new $className(
            $name,
            $info,
            $this->options,
            $this->getDefinition(),
            $this->getCustomHandler()
        );
    }

    public function getFiltersDefinition()
    {
        $defName = $this->getOption('def_name');

        return session('table_builder.'.$defName.'.filters', []);
    }

    public function getOrderDefinition()
    {
        $defName = $this->getOption('def_name');

        return session('table_builder.'.$defName.'.order', []);
    }
}
