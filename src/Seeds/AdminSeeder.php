<?php

namespace Vis\Builder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([

            'email'       => 'admin@vis-design.com',
            'password'    => bcrypt('secret'),
            'first_name'  => 'admin',
            'last_name'   => 'admin',
            'image'       => '',
            'permissions' => '',
            'last_login'  => date('Y-m-d G:i:s'),
            'created_at'  => date('Y-m-d G:i:s'),
            'updated_at'  => date('Y-m-d G:i:s'),
        ]);

        DB::table('activations')->insert([

            'user_id'      => 1,
            'code'         => 'KAeedobpdF5ngq62xSPIzx1zdZkjjk2P',
            'completed'    => 1,
            'completed_at' => date('Y-m-d G:i:s'),
            'created_at'   => date('Y-m-d G:i:s'),
            'updated_at'   => date('Y-m-d G:i:s'),
        ]);

        DB::table('roles')->insert([

            'slug'        => 'admin',
            'name'        => 'Администратор',
            'permissions' => '{"admin.access":true,"tree.view":true,"articles.view":true,"settingsblock.view":true,"settingssettingsall.view":true,"translationscmsphrases.view":true,"revisions.view":true,"translationsphrases.view":true,"usersgroup.view":true,"users.view":true,"groups.view":true}',
            'created_at'  => date('Y-m-d G:i:s'),
            'updated_at'  => date('Y-m-d G:i:s'),
        ]);

        DB::table('roles')->insert([
            'slug'        => 'editor',
            'name'        => 'Редактор',
            'permissions' => '{"admin.access":true,"tree.view":true,"articles.view":true,"settings_block.view":true,"settingssettings_all.view":true,"translations_cmsphrases.view":true,"revisions.view":true,"translationsphrases.view":true,"users_group.view":true,"users.view":true,"groups.view":true}',
            'created_at'  => date('Y-m-d G:i:s'),
            'updated_at'  => date('Y-m-d G:i:s'),

        ]);

        DB::table('role_users')->insert([
            'user_id' => '1',
            'role_id' => '1',
        ]);

        DB::table('tb_tree')->insert([

            'lft'                    => '1',
            'rgt'                    => '62',
            'depth'                  => '0',
            'title'                  => json_encode(['ua' => 'Головна']),
            'slug'                   => '/',
            'template'               => 'main',
            'is_active'              => '1',
            'created_at'             => date('Y-m-d G:i:s'),
            'updated_at'             => date('Y-m-d G:i:s'),
            'picture'                => '',
            'additional_pictures'    => '',
        ]);
    }
}
