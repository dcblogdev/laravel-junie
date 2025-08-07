<?php

namespace Dcblogdev\Junie\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\multiselect;

class InstallGuidelinesCommand extends Command
{
    protected $name = 'junie:install';

    protected $description = 'Install selected guideline documents';

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        $options = [
            ['all', null, InputOption::VALUE_NONE, 'Install all guidelines'],
        ];

        $configDocuments = config('junie.documents', []);

        foreach ($configDocuments as $key => $document) {
            if ($document['enabled'] ?? false) {
                $options[] = [$key, null, InputOption::VALUE_NONE, "Install {$document['name']}"];
            }
        }

        return $options;
    }

    public function handle(): void
    {
        // Determine which documents to install
        $installAll = $this->option('all');

        $documents = [];
        $configDocuments = config('junie.documents', []);

        foreach ($configDocuments as $key => $document) {
            // Only consider enabled documents
            if ($document['enabled'] ?? false) {
                $documents[$key] = $installAll || $this->option($key);
            } else {
                $documents[$key] = false;
            }
        }

        // If no specific documents were selected, ask the user
        if (! $installAll && ! array_filter($documents)) {
            $documents = $this->promptForDocuments();
        }

        // Install selected documents
        $this->installDocuments($documents);

        $this->info('Guidelines installed successfully!');
    }

    protected function promptForDocuments(): array
    {
        $options = ['all' => 'All guidelines'];
        $configDocuments = config('junie.documents', []);
        $enabledDocuments = [];

        foreach ($configDocuments as $key => $document) {
            if ($document['enabled'] ?? false) {
                $options[$key] = $document['name'];
                $enabledDocuments[$key] = $document;
            }
        }

        // Using Laravel Prompts multiselect function
        $selected = multiselect(
            'Which guidelines would you like to install?',
            $options,
            [],// default
            5, // scroll
            false, // required
            null, // validate
            'Use space to select, enter to confirm' // hint
        );

        $installAll = in_array('all', $selected);

        $documents = [];

        foreach ($configDocuments as $key => $document) {
            // Only include enabled documents
            if ($document['enabled'] ?? false) {
                $documents[$key] = $installAll || in_array($key, $selected);
            } else {
                $documents[$key] = false;
            }
        }

        return $documents;
    }

    protected function installDocuments(array $documents): void
    {
        $outputPath = config('junie.output_path', '.junie');
        $configDocuments = config('junie.documents', []);

        // Create an output directory if it doesn't exist
        if (! File::isDirectory($outputPath)) {
            File::makeDirectory($outputPath, 0755, true);
        }

        // Copy selected documents
        foreach ($documents as $document => $install) {
            if ($configDocuments[$document]['custom'] ?? false) {
                $this->line("Skipped custom <info>{$configDocuments[$document]['name']}</info>");
                continue;
            }

            if ($install && isset($configDocuments[$document]) && ($configDocuments[$document]['enabled'] ?? false)) {
                $documentPath = $configDocuments[$document]['path'] ?? $document.'.md';
                $source = __DIR__.'/../../docs/'.$documentPath;
                $destination = base_path($outputPath.'/'.$documentPath);

                File::copy($source, $destination);
                $this->line("Installed <info>{$configDocuments[$document]['name']}</info>");
            }
        }

        // Create an index file with links to all installed documents
        $this->createIndexFile($outputPath, $documents);
    }

    protected function createIndexFile(string $outputPath, array $documents): void
    {
        $content = "# Laravel Guidelines\n\n";
        $content .= "## Available Guidelines\n\n";
        $configDocuments = config('junie.documents', []);

        foreach ($documents as $document => $installed) {
            if ($installed && isset($configDocuments[$document]) && ($configDocuments[$document]['enabled'] ?? false)) {
                $documentConfig = $configDocuments[$document];
                $title = $documentConfig['name'];
                $path = $documentConfig['path'] ?? $document.'.md';

                if (! File::exists(base_path($outputPath.'/'.$path))) {
                    $this->warn("Document <info>{$title}</info> not found at {$path}.");
                    continue;
                }

                $content .= "- [{$title}]({$path})\n";
            }
        }

        File::put(base_path($outputPath.'/index.md'), $content);
    }
}
