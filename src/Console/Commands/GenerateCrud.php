<?php

namespace mohamedelaraby\QuickCrud\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCrud extends Command
{
    protected $signature = 'generate:crud {model}';
    protected $description = 'Generate CRUD files for a given model';

    public function handle()
    {
        $modelName = ucfirst($this->argument('model'));
        $modelVariable = lcfirst($modelName);
        $pluralModel = Str::plural($modelVariable);
        $translationFormattedString = strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $modelName));
        $titleUpperCase = strtoupper(preg_replace('/([a-z])([A-Z])/', '$1 $2', $modelName));


        // Define Paths
        $controllerPath = base_path("app/Http/Controllers/Admin/{$modelName}Controller.php");
        $viewPath = base_path("resources/views/admin/{$pluralModel}/");
        $modelPath = base_path("app/Models/{$modelName}.php");
        $dataTablePath = base_path("app/DataTables/{$modelName}DataTable.php");
        $migrationPath = base_path('database/migrations/' . date('Y_m_d_His') . "_create_{$pluralModel}_table.php");
        // Ensure directories exist
        $this->createDirectory(dirname($controllerPath));
        $this->createDirectory($viewPath);
        $this->createDirectory(dirname($dataTablePath));

        // Generate Controller
        $controllerStub = file_get_contents(__DIR__.'/../../../resources/stubs/controllers/controller.stub');
        $controllerStub = str_replace(
        ['{{ModelName}}', '{{modelVariable}}', '{{pluralModel}}'],
        [$modelName, $modelVariable, $pluralModel],
        $controllerStub);
        File::put($controllerPath, $controllerStub);
        $this->info("Controller Created: {$controllerPath}");

        // Generate Views
        $views = ['index', 'create', 'edit'];
        foreach ($views as $view) {
            $viewStub = file_get_contents(resource_path("stubs/views/{$view}.stub"));
            $viewStub = str_replace(
                [
                    '{{ModelName}}',
                    '{{modelVariable}}',
                    '{{pluralModel}}',
                    '{{translationFormattedString}}',
                    '{{titleUpperCase}}'
                ],
                [
                    $modelName,
                    $modelVariable,
                    $pluralModel,
                    $translationFormattedString,
                    $titleUpperCase
                ],
                $viewStub
            );
            File::put("{$viewPath}/{$view}.blade.php", $viewStub);
        }
        $this->info("Views Created: {$viewPath}");

        // Generate Model
        $modelStub = file_get_contents(__DIR__.'/../../../resources/stubs/models/model.stub');
        $modelStub = str_replace('{{ModelName}}', $modelName, $modelStub);
        File::put($modelPath, $modelStub);
        $this->info("Model Created: {$modelPath}");

        $this->info("CRUD for {$modelName} generated successfully!");
    }

    private function createDirectory($path)
    {
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
    }
}