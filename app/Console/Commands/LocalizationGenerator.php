<?php

namespace App\Console\Commands;

use File;
use Illuminate\Config\Repository;
use Potsky\LaravelLocalizationHelpers\Command\LocalizationAbstract;
use Potsky\LaravelLocalizationHelpers\Factory\Localization;

class LocalizationGenerator extends LocalizationAbstract
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'localization:json';

    /**
     * Ignore these lang subfolders
     *
     * https://laravel.com/docs/4.2/localization
     * https://laravel.com/docs/5.1/localization
     *
     * @var array
     */
    protected $ignoreVendors = ['vendor', 'packages'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse all translations in app directory and build all lang files';

    /**
     * functions and method to catch translations
     *
     * @var  array
     */
    protected $trans_methods = [];

    /**
     * functions and method to catch translations
     *
     * @var  array
     */
    protected $editor = '';

    /**
     * Folders to seek for missing translations
     *
     * @var  array
     */
    protected $folders = [];

    /**
     * Never make lemmas containing these keys obsolete
     *
     * @var  array
     */
    protected $never_obsolete_keys = [];

    /**
     * Never manage these lang files
     *
     * @var  array
     */
    protected $ignore_lang_files = [];

    /**
     * The lang folder path where are stored lang files in locale sub-directory
     *
     * @var  array
     */
    protected $lang_folder_path = [];

    /**
     * The code style list of fixers to apply
     *
     * @var  array
     */
    protected $code_style_fixers = [];

    /**
     * The code style level to apply
     *
     * @var  string
     */
    protected $code_style_level = null;

    /**
     * The obsolete lemma array key in which to store obsolete lemma
     *
     * @var  string
     *
     * @since 2.x.2
     */
    protected $obsolete_array_key = 'LLH:obsolete';

    /**
     * The dot notation split regex
     *
     * @var  string
     *
     * @since 2.x.5
     */
    protected $dot_notation_split_regex = null;


    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository $configRepository
     */
    public function __construct(Repository $configRepository)
    {
        parent::__construct($configRepository);

        $this->trans_methods            = config(Localization::PREFIX_LARAVEL_CONFIG . 'trans_methods');
        $this->folders                  = config(Localization::PREFIX_LARAVEL_CONFIG . 'folders');
        $this->ignore_lang_files        = config(Localization::PREFIX_LARAVEL_CONFIG . 'ignore_lang_files');
        $this->lang_folder_path         = config(Localization::PREFIX_LARAVEL_CONFIG . 'lang_folder_path');
        $this->never_obsolete_keys      = config(Localization::PREFIX_LARAVEL_CONFIG . 'never_obsolete_keys');
        $this->editor                   = config(Localization::PREFIX_LARAVEL_CONFIG . 'editor_command_line');
        $this->code_style_fixers        = config(Localization::PREFIX_LARAVEL_CONFIG . 'code_style.fixers');
        $this->code_style_level         = config(Localization::PREFIX_LARAVEL_CONFIG . 'code_style.level');
        $this->dot_notation_split_regex = config(Localization::PREFIX_LARAVEL_CONFIG . 'dot_notation_split_regex');

        if ( ! is_string($this->dot_notation_split_regex)) {
            // fallback to dot if provided regex is not a string
            $this->dot_notation_split_regex = '/\\./';
        }

        // @since 2.x.2
        // Users who have not upgraded their configuration file must have a default
        // but users may want to set it to null to keep the old buggy behaviour
        $this->obsolete_array_key = config(Localization::PREFIX_LARAVEL_CONFIG . 'obsolete_array_key', $this->obsolete_array_key);
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $folders         = $this->manager->getPath($this->folders);
        $this->display   = true;
        $extension       = 'php';
        $obsolete_prefix = (empty($this->obsolete_array_key)) ? '' : $this->obsolete_array_key . '.';

        $this->info("Parsings Files...");

        $lemmas = $this->manager->extractTranslationsFromFolders($folders, $this->trans_methods, $extension);

        foreach (config('translatable.locales') as $locale) {
            $data = json_decode(File::get(resource_path('lang/' . $locale . '.json')), JSON_OBJECT_AS_ARRAY);

            foreach ($lemmas as $k => $v) {
                if ( ! array_key_exists($k, $data)) {
                    $data[$k] = $k;
                }
            }

            $content = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            File::put(resource_path('lang/' . $locale . '.json'), $content);
        }

        $this->info("Language Json Files Generated");

        return self::SUCCESS;
    }

}
