<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class Install extends Command
{
    

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allows to install Nura24 directly through CLI';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        

        $this->line('Setup database tables');
        Artisan::call('migrate');
        

        $this->line('Adding core settings into tables');

        DB::table('sys_config')->insertOrIgnore(['name' => 'template', 'value' => 'nura24_default']);     
        DB::table('sys_config')->insertOrIgnore(['name' => 'site_meta_author', 'value' => null]);
        DB::table('sys_config')->insertOrIgnore(['name' => 'registration_enabled', 'value' => 1]);
        DB::table('sys_config')->insertOrIgnore(['name' => 'favicon', 'value' => '/default/favicon.png']);
        DB::table('sys_config')->insertOrIgnore(['name' => 'logo', 'value' => '/default/logo.png']);
        DB::table('sys_config')->insertOrIgnore(['name' => 'logo_auth' ,'value' => '/default/logo-auth.png']);
        
        // language
        if(! DB::table('sys_lang')->where('is_default', 1)->where('status', 'active')->exists()) 
            DB::table('sys_lang')->updateOrInsert(['code' => 'en'],['name' => 'English', 'locale' => 'en_US', 'is_default' => 1, 'status' => 'active', 'timezone' => 'Europe/London', 'date_format' => '%A, %e %B %Y', 'currency_display_style' => 'condensed', 'site_short_title' => 'Nura24']);        

        // Modules
        DB::table('sys_modules')->updateOrInsert(['module' => 'accounts'],['label' => 'Accounts', 'description' => 'Access to manage accounts', 'status' => 'active', 'have_frontend' => 0]);     
        DB::table('sys_modules')->updateOrInsert(['module' => 'blocks'],['label' => 'Content blocks', 'description' => 'Add content blocks in your website', 'status' => 'active', 'have_frontend' => 0]);     
        DB::table('sys_modules')->updateOrInsert(['module' => 'cart'],['label' => 'eCommerce', 'description' => 'eCommerce (online shop) section', 'status' => 'active', 'have_frontend' => 1]);     
        DB::table('sys_modules')->updateOrInsert(['module' => 'contact'],['label' => 'Contact page', 'description' => 'Add a contact page with contact form on your website', 'status' => 'active', 'have_frontend' => 1]);     
        DB::table('sys_modules')->updateOrInsert(['module' => 'docs'],['label' => 'Knowledge Base', 'description' => 'Add a knowledge base (documentation) section on your website', 'status' => 'active', 'have_frontend' => 1]); 
        DB::table('sys_modules')->updateOrInsert(['module' => 'downloads'],['label' => 'Downloads', 'description' => 'Add a downloads area on your website', 'status' => 'active', 'have_frontend' => 1]);    
        DB::table('sys_modules')->updateOrInsert(['module' => 'email_marketing'],['label' => 'Email Marketing', 'description' => 'Email Marketing', 'status' => 'active', 'have_frontend' => 0]);    
        DB::table('sys_modules')->updateOrInsert(['module' => 'faq'],['label' => 'F.A.Q.', 'description' => 'Frequently Asked Questions page', 'status' => 'active', 'have_frontend' => 1]);    
        DB::table('sys_modules')->updateOrInsert(['module' => 'forum'],['label' => 'Community', 'description' => 'Add a community forum on your website', 'status' => 'active', 'have_frontend' => 1]);   
        DB::table('sys_modules')->updateOrInsert(['module' => 'pages'],['label' => 'Static pages', 'description' => 'Create static / landing pages', 'status' => 'active', 'have_frontend' => 1]);   
        DB::table('sys_modules')->updateOrInsert(['module' => 'posts'],['label' => 'Posts', 'description' => 'Create a blog / articles / news section on your website', 'status' => 'active', 'have_frontend' => 1]);   
        DB::table('sys_modules')->updateOrInsert(['module' => 'slider'],['label' => 'Slider', 'description' => 'Add a content slider on your homepage', 'status' => 'active', 'have_frontend' => 1]);   
        DB::table('sys_modules')->updateOrInsert(['module' => 'tickets'],['label' => 'Support Tickets', 'description' => 'Support Tickets area for registered users', 'status' => 'active', 'have_frontend' => 1]);   
        DB::table('sys_modules')->updateOrInsert(['module' => 'translates'],['label' => 'Translates', 'description' => 'Access to translate content', 'status' => 'active', 'have_frontend' => 0]);   

        // Accounts roles
        DB::table('users_roles')->updateOrInsert(['role' => 'admin'],['active' => 1, 'registration_enabled' => 0]);     
        DB::table('users_roles')->updateOrInsert(['role' => 'internal'],['active' => 1, 'registration_enabled' => 0]);     
        DB::table('users_roles')->updateOrInsert(['role' => 'user'],['active' => 1, 'registration_enabled' => 1]);     
        DB::table('users_roles')->updateOrInsert(['role' => 'vendor'],['active' => 0, 'registration_enabled' => 1]);     


        $admin_name = $this->askValid('Input administrator full name: ', 'admin_name', ['required', 'min:3']);
        $admin_email = $this->askValid('Input administrator email: ', 'admin_email', ['required', 'email']);
        $admin_pass = $this->askValid('Input administrator password: ', 'admin_pass', ['required', 'min:5']);        

        // Add administrator account
        $this->line('Adding administrator account');
        $UserModel = new User();    
        $role_id_admin = $UserModel->get_role_id_from_role('admin');
        DB::table('users')->updateOrInsert(['email' => $admin_email], [
            'name' => $admin_name ?? 'Admin',
            'code' => strtoupper(Str::random(8)),
            'slug' => Str::slug(($admin_name ?? 'admin'), '-'), 
            'email' => $admin_email,      
            'role_id' => $role_id_admin,     
            'password' => Hash::make('12345678'),
            'active' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
        ]);    

        $this->info('The install was successful!');
    }


    protected function askValid($question, $field, $rules)
    {
        $value = $this->ask($question);

        if($message = $this->validateInput($rules, $field, $value)) {
            $this->error($message);

            return $this->askValid($question, $field, $rules);
        }

        return $value;
    }


    protected function validateInput($rules, $fieldName, $value)
    {
        $validator = Validator::make([
        $fieldName => $value
        ], [
        $fieldName => $rules
        ]);

        return $validator->fails()
            ? $validator->errors()->first($fieldName)
            : null;
    }
}