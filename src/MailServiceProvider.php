<?php

namespace budisteikul\mail;

use Illuminate\Support\ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'mail');
        
        //$this->loadMigrationsFrom(__DIR__.'/migrations/2019_01_11_142447_create_mail_accounts_table.php');
        //$this->loadMigrationsFrom(__DIR__.'/migrations/2019_01_12_134154_create_mail_options_table.php');
        //$this->loadMigrationsFrom(__DIR__.'/migrations/2019_01_12_150110_create_mail_emails_table.php');
        //$this->loadMigrationsFrom(__DIR__.'/migrations/2019_01_12_150130_create_mail_attachments_table.php');

        $this->publishes([ __DIR__.'/publish/css' => public_path('css'),], 'budisteikul');
        $this->publishes([ __DIR__.'/publish/js' => public_path('js'),], 'budisteikul');
        $this->publishes([ __DIR__.'/publish/fonts' => public_path('fonts'),], 'budisteikul');
        $this->publishes([ __DIR__.'/publish/images' => public_path('images'),], 'budisteikul');

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

    }
}
