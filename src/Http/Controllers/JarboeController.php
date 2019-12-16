<?php

namespace Vis\Builder;

use Vis\Builder\Handlers\ActionsHandler;
use Vis\Builder\Handlers\ButtonsHandler;
use Vis\Builder\Handlers\CustomClosureHandler;
use Vis\Builder\Handlers\DefinitionHandler;
use Vis\Builder\Handlers\ExportHandler;
use Vis\Builder\Handlers\ImportHandler;
use Vis\Builder\Handlers\QueryHandler;
use Vis\Builder\Handlers\RequestHandler;
use Vis\Builder\Handlers\ViewHandler;
use Illuminate\Support\Str;

/**
 * Class JarboeController.
 */
class JarboeController
{
    /**
     * @var array|bool|\Illuminate\Http\Request|string
     */
    protected $currentID = false;

    /**
     * @var
     */
    protected $options;
    /**
     * @var mixed
     */
    protected $definition;

    /**
     * @var bool
     */
    protected $handler;
    /**
     * @var CustomClosureHandler
     */
    protected $callbacks;
    /**
     * @var array
     */
    protected $fields;
    /**
     * @var array
     */
    protected $groupFields;
    /**
     * @var array
     */
    protected $patterns = [];

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
     * @var
     */
    public $imageStorage;
    /**
     * @var
     */
    public $fileStorage;
    /**
     * @var DefinitionHandler
     */
    public $definitionClass;

    /**
     * @var
     */
    protected $allowedIds;

    /**
     * JarboeController constructor.
     *
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        $this->definition = $this->getTableDefinition($this->getOption('def_name'));
        $this->definitionClass = new DefinitionHandler($this->definition, $this);

        $this->doPrepareDefinition();

        $this->handler = $this->createCustomHandlerInstance();
        if (isset($this->definition['callbacks'])) {
            $this->callbacks = new CustomClosureHandler($this->definition['callbacks'], $this);
        }
        $this->fields = $this->loadFields();
        $this->groupFields = $this->loadGroupFields();

        $this->actions = new ActionsHandler($this->definition['actions'], $this);

        if ($this->definition['export']) {
            $this->export = new ExportHandler($this->definition['export'], $this);
        }

        if ($this->definition['import']) {
            $this->import = new ImportHandler($this->definition['import'], $this);
        }

        if (isset($this->definition['buttons'])) {
            $this->buttons = new ButtonsHandler($this->definition['buttons'], $this);
        }
        $this->query = new QueryHandler($this);
        $this->view = new ViewHandler($this);
        $this->request = new RequestHandler($this);

        $this->currentID = request('id');
    }

    /**
     * @return array|bool|\Illuminate\Http\Request|string
     */
    public function getCurrentID()
    {
        return $this->currentID;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->definition['options']['model'];
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->definition['db']['table'];
    }

    private function doPrepareDefinition()
    {
        if (! isset($this->definition['export'])) {
            $this->definition['export'] = [];
        }
        if (! isset($this->definition['import'])) {
            $this->definition['import'] = [];
        }

        if (! isset($this->definition['actions'])) {
            $this->definition['actions'] = [];
        }

        if (! isset($this->definition['db']['pagination']['uri'])) {
            $this->definition['db']['pagination']['uri'] = $this->options['url'];
        }
    }

    // end doPrepareDefinition

    /**
     * @return array|\Illuminate\Http\JsonResponse|mixed
     */
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

    // end handle

    /**
     * @param $id
     *
     * @return bool
     */
    public function isAllowedID($id)
    {
        return in_array($id, $this->allowedIds);
    }

    /**
     * @param $opt
     *
     * @return mixed
     */
    protected function getPreparedOptions($optionsParam)
    {
        $optionsParam['def_path'] = app_path().$optionsParam['def_path'];

        return $optionsParam;
    }

    /**
     * @return bool
     */
    protected function createCustomHandlerInstance()
    {
        if (isset($this->definition['options']['handler'])) {
            $handlerCustom = '\\'.$this->definition['options']['handler'];

            return new $handlerCustom($this);
        }

        return false;
    }

    /**
     * @param $methodName
     *
     * @return bool
     */
    public function hasCustomHandlerMethod($methodName)
    {
        return $this->getCustomHandler() && is_callable([$this->getCustomHandler(), $methodName]);
    }

    /**
     * @return CustomClosureHandler
     */
    public function getCustomHandler()
    {
        return $this->handler ?: $this->callbacks;
    }

    /**
     * @param $ident
     *
     * @return mixed
     */
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

    // end getField

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    // end getFields

    /**
     * @param $ident
     *
     * @return mixed
     */
    public function getOption($ident)
    {
        if (isset($this->options[$ident])) {
            return $this->options[$ident];
        }

        throw new \RuntimeException("Undefined option [{$ident}].");
    }

    // end getOption

    /**
     * @return string
     */
    public function getDefinitionName()
    {
        $definitionName = explode('.', $this->getOption('def_name'));

        return $definitionName[0];
    }

    /**
     * @return string
     */
    public function getUrlAction()
    {
        return '/admin/handle/'.$this->getDefinitionName();
    }

    /**
     * @return array
     */
    public function getAdditionalOptions()
    {
        if (isset($this->options['additional'])) {
            return $this->options['additional'];
        }

        return [];
    }

    // end getAdditionalOptions

    /**
     * @return mixed
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    // end getDefinition

    /**
     * @return array
     */
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

    // end loadFields

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
    }

    // end getPatterns

    /**
     * @param $name
     *
     * @return false|int
     */
    public function isPatternField($name)
    {
        return preg_match('~^pattern\.~', $name);
    }

    // end isPatternField

    /**
     * @param $name
     * @param $info
     *
     * @return Fields\PatternField
     */
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

    /**
     * @param $name
     * @param $info
     *
     * @return mixed
     */
    protected function createFieldInstance($name, $info)
    {
        $className = 'Vis\\Builder\\Fields\\'.ucfirst(Str::camel($info['type'])).'Field';

        if (isset($info['handler'])) {
            $className = $info['handler'];
        }

        return new $className(
            $name,
            $info,
            $this->options,
            $this->getDefinition(),
            $this->getCustomHandler()
        );
    }

    /**
     * @param string $table
     *
     * @return mixed
     */
    protected function getTableDefinition($table)
    {
        $table = preg_replace('~\.~', '/', $table);
        $path = config_path().'/builder/tb-definitions/'.$table.'.php';

        if (! file_exists($path)) {
            throw new \RuntimeException("Definition \n[{$path}]\n does not exist.");
        }

        $definitionThis = require $path;

        $definitionThis['is_searchable'] = $this->isSearchable($definitionThis);
        $definitionThis['options']['admin_uri'] = config('builder.admin.uri');

        return $definitionThis;
    }

    /**
     * @param array $definition
     *
     * @return bool
     */
    private function isSearchable($definition)
    {
        $isSearchable = false;

        if (isset($definition['fields'])) {
            foreach ($definition['fields'] as $field) {
                if (isset($field['filter'])) {
                    $isSearchable = true;
                    break;
                }
            }
        }

        return $isSearchable;
    }

    /**
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function getFiltersDefinition()
    {
        $defName = $this->getOption('def_name');

        return session('table_builder.'.$defName.'.filters', []);
    }

    /**
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function getOrderDefinition()
    {
        $defName = $this->getOption('def_name');

        return session('table_builder.'.$defName.'.order', []);
    }
}
