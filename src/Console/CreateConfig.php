<?php

namespace Vis\Builder\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateConfig extends Command
{
    protected $name = 'createConfig';
    protected $signature = 'admin:createConfig {table} {--fields=}';
    protected $description = 'Command generate default config file, model and migration';

    private string $table;
    private string $model;
    private string $installPath;
    private string $fields;

    public function __construct()
    {
        $this->installPath = __DIR__.'/../install/files/generateFiles';

        parent::__construct();
    }

    public function handle(): mixed
    {
        $this->table = $this->argument('table');
        $this->model = ucfirst(Str::camel(Str::singular($this->table)));
        $this->fields = $this->option('fields');

        $this->createModel();
        $this->createModelDefinition();
        $this->createMigration();
    }

    private function createModel(): void
    {
        $fileModel = app_path().'/Models/'.$this->model.'.php';

        if (file_exists($fileModel)) {
            if (! $this->confirm('Model '.$this->model.' is exist, replace model?')) {
                return;
            }
        }

        copy(
            $this->installPath.'/modelName.php',
            $fileModel
        );

        $this->replaceParams($fileModel);

        $this->info('Model '.$this->model.' created');
    }

    private function createModelDefinition(): void
    {
        $model = Str::plural($this->model);

        $fileDefinition = app_path('/Cms/Definitions/'.$model.'.php');

        if (file_exists($fileDefinition)) {
            if (! $this->confirm('Definition '.$model.' is exist, replace definition?')) {
                return;
            }
        }

        copy(
            $this->installPath.'/DefinitionModel.php',
            $fileDefinition
        );

        $this->replaceParams($fileDefinition);
        $this->replaceFieldsConfig($fileDefinition);

        $this->info('Definition '.$model.' created');
    }

    private function createMigration(): void
    {
        $nameMigration = date('Y_m_d_His').'_create_'.$this->table.'_table.php';
        $fileMigration = base_path().'/database/migrations/'.$nameMigration;

        copy(
            $this->installPath.'/Migration.php',
            $fileMigration
        );

        $this->replaceParams($fileMigration);
        $this->replaceFieldsMigration($fileMigration);

        $this->info('Migration '.$nameMigration.' created');
    }

    private function replaceParams(string $fileReplace): void
    {
        $file = file_get_contents($fileReplace);
        $file = str_replace(
            ['modelName', 'tableName', 'tableUpName', 'modelPluralName'],
            [$this->model, $this->table, ucfirst(Str::camel($this->table . '_table')), Str::plural($this->model)],
            $file);

        file_put_contents($fileReplace, $file);
    }

    private function replaceFieldsConfig(string $fileReplace): void
    {
        $fieldsDescription = '';

        if ($this->fields) {
            $arrFields = explode(',', $this->fields);

            foreach ($arrFields as $field) {
                if (strpos($field, ':')) {
                    $nameAndType = explode(':', $field);
                    $field = $nameAndType[0];
                    $type = $this->adaptiveFieldForConfig($nameAndType[1]);
                } else {
                    $type = 'Text';
                }

                $fieldsDescription .= "$type::make('$field', '$field')->filter()->sortable(),
                ";
            }
        }

        $file = file_get_contents($fileReplace);
        $file = str_replace(
            ["fieldsDescription,"],
            [$fieldsDescription],
            $file);

        file_put_contents($fileReplace, $file);
    }

    private function adaptiveFieldForConfig(string $type): string
    {
        switch ($type) {
            case 'tinyInteger':
            case 'boolean':
                return 'Checkbox';
            case 'text':
                return 'Textarea';
            case 'datetime':
                return 'Datetime';
            default:
                return 'Text';
        }
    }

    private function replaceFieldsMigration(string $fileReplace): void
    {
        $fieldsReplace = '';

        if ($this->fields) {
            $arrFields = explode(',', $this->fields);

            foreach ($arrFields as $field) {
                if (strpos($field, ':')) {
                    $nameAndType = explode(':', $field);
                    $type = $nameAndType[1];
                    $field = $nameAndType[0];
                } else {
                    $type = 'string';
                }

                $fieldsReplace .= '$table->'.$type.'("'.$field.'");
            ';
            }
        }

        $file = file_get_contents($fileReplace);
        $file = str_replace(
            ['$table->fieldsReplace;'],
            [$fieldsReplace],
            $file);

        file_put_contents($fileReplace, $file);
    }
}
