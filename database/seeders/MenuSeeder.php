<?php

namespace Database\Seeders;

use App\Models\Menu; // Correct import
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            $this->createMenu($value);
        }
    }

    private function createMenu($data, $parent_id = null)
    {
        $menu = new Menu([
            'name' => $data['name'],
            'icon' => $data['icon'],
            'route' => $data['route'],
            'description' => $data['description'],
            'sorting' => $data['sorting'],
            'parent_id' => $parent_id,
            'permission_name' => $data['permission_name'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $menu->save();

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createMenu($child, $menu->id);
            }
        }
    }

    private function datas()
    {
        return [
            [
                'name' => 'Dashboard',
                'icon' => 'home',
                'route' => 'backend.dashboard',
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'dashboard',
                'status' => 'Active',
            ],
            [
                'name' => 'Module Make',
                'icon' => 'slack',
                'route' => 'backend.moduleMaker',
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'module maker',
                'status' => 'Active',
            ],
            [
                'name' => 'User Manage',
                'icon' => 'list',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'user-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'User Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.admin.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'User List',
                        'icon' => 'list',
                        'route' => 'backend.admin.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Permission Manage',
                'icon' => 'unlock',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'permission-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Permission Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.permission.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Permission List',
                        'icon' => 'list',
                        'route' => 'backend.permission.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Role Manage',
                'icon' => 'layers',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'role-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Role Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.role.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Role List',
                        'icon' => 'list',
                        'route' => 'backend.role.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-list',
                        'status' => 'Active',
                    ],
                ],
            ],



    [
        "name" => "Tenant Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Tenant-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Tenant Add",
                "icon" => "plus-circle",
                "route" => "backend.tenant.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Tenant List",
                "icon" => "list",
                "route" => "backend.tenant.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "HouseOwner Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "HouseOwner-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "HouseOwner Add",
                "icon" => "plus-circle",
                "route" => "backend.houseowner.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "HouseOwner List",
                "icon" => "list",
                "route" => "backend.houseowner.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "House Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "House-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "House Add",
                "icon" => "plus-circle",
                "route" => "backend.house.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "House List",
                "icon" => "list",
                "route" => "backend.house.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Flat Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Flat-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Flat Add",
                "icon" => "plus-circle",
                "route" => "backend.flat.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Flat List",
                "icon" => "list",
                "route" => "backend.flat.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Room Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Room-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Room Add",
                "icon" => "plus-circle",
                "route" => "backend.room.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Room List",
                "icon" => "list",
                "route" => "backend.room.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Booking Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Booking-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Booking Add",
                "icon" => "plus-circle",
                "route" => "backend.booking.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Booking List",
                "icon" => "list",
                "route" => "backend.booking.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Payment Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Payment-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Payment Add",
                "icon" => "plus-circle",
                "route" => "backend.payment.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Payment List",
                "icon" => "list",
                "route" => "backend.payment.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "FlatImage Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "FlatImage-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "FlatImage Add",
                "icon" => "plus-circle",
                "route" => "backend.flatimage.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "FlatImage List",
                "icon" => "list",
                "route" => "backend.flatimage.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],

    
    [
        "name" => "SavedProperty Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "SavedProperty-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "SavedProperty Add",
                "icon" => "plus-circle",
                "route" => "backend.savedproperty.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "SavedProperty List",
                "icon" => "list",
                "route" => "backend.savedproperty.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],

    //don't remove this comment from menu seeder
        ];
    }
}