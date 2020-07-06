<?php

use App\Models\Package;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTablesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $account = \App\Models\Account::create([
            'name'              => 'Test Account',
            'package_id'        => Package::first()->id,
            'extra_monthly_fee' => 100,
            'expired_at'        => Carbon::now()->addYear(),
        ]);

        $account->setting([
            'appointments.texts.success'       => 'We added your appointment to :date. Thank you!',
            'appointments.texts.not_available' => 'Sorry, this period is not available',
            'appointments.texts.cancel'        => 'Appointment successfully cancelled!',
            'appointments.texts.cancel_error'  => 'Sorry, appointment cannot be cancelled',
        ])->save();

        /**
         * @var $user \App\Models\User
         */
        $user = \App\Models\User::create([
            'account_id' => $account->id,
            'email'      => 'test@tmmn.test',
            'username'   => 'test',
            'first_name' => 'Test',
            'last_name'  => 'User',
            'phone'      => '17185099627',
            'avatar'     => url("/assets/images/member.jpg"),
            'password'   => bcrypt('secret'),
        ]);

        $user->permissions()->firstOrCreate(['permission' => "accounts.view"]);
        $user->permissions()->firstOrCreate(['permission' => "accounts.create"]);
        $user->permissions()->firstOrCreate(['permission' => "accounts.update"]);
        $user->permissions()->firstOrCreate(['permission' => "accounts.delete"]);
        $user->permissions()->firstOrCreate(['permission' => "accounts.auth"]);
        $user->permissions()->firstOrCreate(['permission' => "admin.reports"]);
        /**
         * @var $adminRole \App\Models\Role
         */
        $adminRole = \App\Models\Role::create([
            'name'         => 'admin',
            'display_name' => 'Admin',
            'account_id'   => $account->id,
        ]);

        \App\Models\Did::create([
            'did'        => '17185099627',
            'account_id' => $account->id,
        ]);
        \App\Models\Permission::insert([
            [
                'name'         => 'dashboard.view',
                'display_name' => 'Dashboard Page',
            ],
            [
                'name'         => 'roles.view',
                'display_name' => 'View Roles',
            ],
            [
                'name'         => 'roles.create',
                'display_name' => 'Create Roles',
            ],
            [
                'name'         => 'roles.update',
                'display_name' => 'Edit Roles',
            ],
            [
                'name'         => 'roles.delete',
                'display_name' => 'Delete Roles',
            ],
            [
                'name'         => 'users.view',
                'display_name' => 'View Users',
            ],
            [
                'name'         => 'users.create',
                'display_name' => 'Create Users',
            ],
            [
                'name'         => 'users.update',
                'display_name' => 'Edit Users',
            ],
            [
                'name'         => 'users.delete',
                'display_name' => 'Delete Users',
            ],
            [
                'name'         => 'contacts.view',
                'display_name' => 'View Contacts',
            ],
            [
                'name'         => 'contacts.create',
                'display_name' => 'Create Contacts',
            ],
            [
                'name'         => 'contacts.update',
                'display_name' => 'Edit Contacts',
            ],
            [
                'name'         => 'contacts.delete',
                'display_name' => 'Delete Contacts',
            ],
            [
                'name'         => 'groups.view',
                'display_name' => 'View Groups',
            ],
            [
                'name'         => 'groups.create',
                'display_name' => 'Create Groups',
            ],
            [
                'name'         => 'groups.update',
                'display_name' => 'Edit Groups',
            ],
            [
                'name'         => 'groups.delete',
                'display_name' => 'Delete Groups',
            ],
            [
                'name'         => 'appointments.view',
                'display_name' => 'View Appointments',
            ],
            [
                'name'         => 'appointments.create',
                'display_name' => 'Create Appointments',
            ],
            [
                'name'         => 'appointments.update',
                'display_name' => 'Edit Appointments',
            ],
            [
                'name'         => 'appointments.delete',
                'display_name' => 'Delete Appointments',
            ],
            [
                'name'         => 'forwarding.view',
                'display_name' => 'View Sms Forwarding',
            ],
            [
                'name'         => 'forwarding.create',
                'display_name' => 'Create Sms Forwarding',
            ],
            [
                'name'         => 'forwarding.update',
                'display_name' => 'Edit Sms Forwarding',
            ],
            [
                'name'         => 'forwarding.delete',
                'display_name' => 'Delete Sms Forwarding',
            ],
            [
                'name'         => 'auto_reply.view',
                'display_name' => 'View Auto Reply',
            ],
            [
                'name'         => 'auto_reply.create',
                'display_name' => 'Create Auto Reply',
            ],
            [
                'name'         => 'auto_reply.update',
                'display_name' => 'Edit Auto Reply',
            ],
            [
                'name'         => 'auto_reply.delete',
                'display_name' => 'Delete Auto Reply',
            ],
            [
                'name'         => 'messages.logs',
                'display_name' => 'View Message Logs',
            ],
            [
                'name'         => 'messages.send',
                'display_name' => 'Send Messages',
            ],
            [
                'name'         => 'messages.delete',
                'display_name' => 'Delete Messages',
            ],
            [
                'name'         => 'custom_labels.view',
                'display_name' => 'View Custom Labels',
            ],
            [
                'name'         => 'custom_labels.create',
                'display_name' => 'Create Custom Labels',
            ],
            [
                'name'         => 'custom_labels.update',
                'display_name' => 'Edit Custom Labels',
            ],
            [
                'name'         => 'custom_labels.delete',
                'display_name' => 'Delete Custom Labels',
            ],
            [
                'name'         => 'message_templates.view',
                'display_name' => 'View Message Templates',
            ],
            [
                'name'         => 'message_templates.create',
                'display_name' => 'Create Message Templates',
            ],
            [
                'name'         => 'message_templates.update',
                'display_name' => 'Edit Message Templates',
            ],
            [
                'name'         => 'message_templates.delete',
                'display_name' => 'Delete Message Templates',
            ],
            [
                'name'         => 'blacklist.view',
                'display_name' => 'View Blacklist',
            ],
            [
                'name'         => 'blacklist.create',
                'display_name' => 'Create Blacklist',
            ],
            [
                'name'         => 'blacklist.update',
                'display_name' => 'Update Blacklist',
            ],
            [
                'name'         => 'blacklist.delete',
                'display_name' => 'Delete Blacklist',
            ],
            [
                'name'         => 'docs.view',
                'display_name' => 'View API Documentation',
            ],
            [
                'name'         => 'messages.cancel',
                'display_name' => 'Cancel Pending Messages',
            ],
            [
                'name'         => 'schedule.view',
                'display_name' => 'Manage Schedules',
            ],
            [
                'name'         => 'schedule.delete',
                'display_name' => 'Delete Schedules',
            ],
            [
                'name'         => 'schedule.update',
                'display_name' => 'Edit Schedules',
            ],
        ]);

        $adminRole->permissions()->attach(Permission::all());

        $user->roles()->attach($adminRole->id);
    }
}
