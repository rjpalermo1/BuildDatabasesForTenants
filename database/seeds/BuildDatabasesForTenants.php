<?php

use Hyn\Tenancy\Contracts\Repositories\CustomerRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Models\Customer;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Illuminate\Database\Seeder;

class BuildDatabsesForTenants extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $customers = [
            ['database' => 'customer_foo_tenancy', 'domain' => 'foo.kraken.test', 'name' => 'Foo Customer', 'email' => 'customer@foo.com'],
            ['database' => 'customer_bar_tenancy', 'domain' => 'bar.kraken.test', 'name' => 'Bar Customer', 'email' => 'customer@bar.com'],
            ['database' => 'customer_baz_tenancy', 'domain' => 'baz.kraken.test', 'name' => 'Baz Customer', 'email' => 'customer@baz.com'],
        ];

        foreach ($customers as $customer) {
            /*
            |--------------------------------------------------------------------------
            | CREATE THE WEBSITE
            |--------------------------------------------------------------------------
             */
            $website = new Website(['uuid' => $customer['database']]);
            app(WebsiteRepository::class)->create($website);

            /*
            |--------------------------------------------------------------------------
            | CREATE THE HOSTNAME
            |--------------------------------------------------------------------------
             */
            $hostname = new Hostname(['fqdn' => $customer['domain']]);
            app(HostnameRepository::class)->attach($hostname, $website);

            /*
            |--------------------------------------------------------------------------
            | CREATE THE CUSTOMER
            |--------------------------------------------------------------------------
             */
            $customer = new Customer(['name' => $customer['name'], 'email' => $customer['email']]);
            app(CustomerRepository::class)->create($customer);

            /*
            |--------------------------------------------------------------------------
            | SAVE THE CUSTOMER WITH HIS HOSTNAME AND WEBSITE
            |--------------------------------------------------------------------------
             */
            $hostname->customer()->associate($customer)->save();
            $website->customer()->associate($customer)->save();
        }
    }
}
