<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public function run(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_alternative","view_any_alternative","create_alternative","update_alternative","restore_alternative","restore_any_alternative","replicate_alternative","reorder_alternative","delete_alternative","delete_any_alternative","force_delete_alternative","force_delete_any_alternative","view_audit","view_any_audit","create_audit","update_audit","restore_audit","restore_any_audit","replicate_audit","reorder_audit","delete_audit","delete_any_audit","force_delete_audit","force_delete_any_audit","view_company","view_any_company","create_company","update_company","restore_company","restore_any_company","replicate_company","reorder_company","delete_company","delete_any_company","force_delete_company","force_delete_any_company","view_contact::message","view_any_contact::message","create_contact::message","update_contact::message","restore_contact::message","restore_any_contact::message","replicate_contact::message","reorder_contact::message","delete_contact::message","delete_any_contact::message","force_delete_contact::message","force_delete_any_contact::message","view_faq","view_any_faq","create_faq","update_faq","restore_faq","restore_any_faq","replicate_faq","reorder_faq","delete_faq","delete_any_faq","force_delete_faq","force_delete_any_faq","view_investor","view_any_investor","create_investor","update_investor","restore_investor","restore_any_investor","replicate_investor","reorder_investor","delete_investor","delete_any_investor","force_delete_investor","force_delete_any_investor","view_office::location","view_any_office::location","create_office::location","update_office::location","restore_office::location","restore_any_office::location","replicate_office::location","reorder_office::location","delete_office::location","delete_any_office::location","force_delete_office::location","force_delete_any_office::location","view_person","view_any_person","create_person","update_person","restore_person","restore_any_person","replicate_person","reorder_person","delete_person","delete_any_person","force_delete_person","force_delete_any_person","view_resource","view_any_resource","create_resource","update_resource","restore_resource","restore_any_resource","replicate_resource","reorder_resource","delete_resource","delete_any_resource","force_delete_resource","force_delete_any_resource","view_similar::site","view_any_similar::site","create_similar::site","update_similar::site","restore_similar::site","restore_any_similar::site","replicate_similar::site","reorder_similar::site","delete_similar::site","delete_any_similar::site","force_delete_similar::site","force_delete_any_similar::site","view_similar::site::category","view_any_similar::site::category","create_similar::site::category","update_similar::site::category","restore_similar::site::category","restore_any_similar::site::category","replicate_similar::site::category","reorder_similar::site::category","delete_similar::site::category","delete_any_similar::site::category","force_delete_similar::site::category","force_delete_any_similar::site::category","view_social::link","view_any_social::link","create_social::link","update_social::link","restore_social::link","restore_any_social::link","replicate_social::link","reorder_social::link","delete_social::link","delete_any_social::link","force_delete_social::link","force_delete_any_social::link","view_tag","view_any_tag","create_tag","update_tag","restore_tag","restore_any_tag","replicate_tag","reorder_tag","delete_tag","delete_any_tag","force_delete_tag","force_delete_any_tag","view_taggable","view_any_taggable","create_taggable","update_taggable","restore_taggable","restore_any_taggable","replicate_taggable","reorder_taggable","delete_taggable","delete_any_taggable","force_delete_taggable","force_delete_any_taggable","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","widget_SiteOverview"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }
}
