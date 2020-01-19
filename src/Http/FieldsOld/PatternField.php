<?php

namespace Vis\Builder\Fields;

/**
 * Class PatternField.
 */
class PatternField extends AbstractField
{
    /**
     * @var
     */
    protected $fieldName;
    /**
     * @var
     */
    protected $attributes;
    /**
     * @var
     */
    protected $options;
    /**
     * @var
     */
    protected $definition;
    /**
     * @var mixed
     */
    protected $calls;

    /**
     * @var
     */
    protected $handler;

    /**
     * PatternField constructor.
     *
     * @param $fieldName
     * @param $attributes
     * @param $options
     * @param $definition
     * @param $handler
     */
    public function __construct($fieldName, $attributes, $options, $definition, $handler)
    {
        $this->attributes = $attributes;
        $this->options = $options;
        $this->definition = $definition;
        $this->fieldName = $fieldName;

        $this->handler = &$handler;

        $patternName = preg_replace('~^pattern\.~', '', $fieldName);
        $path = config_path().'/builder/tb-definitions/pattern/'.$patternName.'.php';

        if (! file_exists($path)) {
            throw new \RuntimeException("No pattern definition - [{$patternName}].");
        }
        $this->calls = require $path;
    }

    /**
     * @param array $row
     *
     * @return mixed
     */
    public function render($row = [])
    {
        $view = $this->calls['view'];

        return $view($row);
    }

    /**
     * @param $values
     * @param $idRow
     *
     * @return mixed
     */
    public function update($values, $idRow)
    {
        $call = $this->calls['handle']['update'];

        return $call($values, $idRow);
    }

    /**
     * @param $values
     * @param $idRow
     *
     * @return mixed
     */
    public function insert($values, $idRow)
    {
        $call = $this->calls['handle']['insert'];

        return $call($values, $idRow);
    }

    /**
     * @param $idRow
     *
     * @return mixed
     */
    public function delete($idRow)
    {
        $call = $this->calls['handle']['delete'];

        return $call($idRow);
    }

    /**
     * @return bool
     */
    public function isPattern()
    {
        return true;
    }

    // end isPattern

    /**
     * @param $db
     * @param $value
     */
    public function onSearchFilter(&$db, $value)
    {
    }

    /**
     * @return string|void
     */
    public function getClientsideValidatorRules()
    {
    }

    /**
     * @return string|void
     */
    public function getClientsideValidatorMessages()
    {
    }

    /**
     * @param $value
     */
    public function doValidate($value)
    {
    }

    /**
     * @param array $row
     *
     * @return mixed|string
     */
    public function getEditInput($row = [])
    {
        return $this->render($row);
    }

    /**
     * @param array $row
     *
     * @return mixed|string
     */
    public function getTabbedEditInput($row = [])
    {
        return $this->render($row);
    }
}
