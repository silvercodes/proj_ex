<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use DB;
use App\User;
use App\Models\DocumentGroup;
use App\Models\Kindergarten;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TtDay;
use App\Models\TtPart;
use App\Models\TtGroupType;
use App\Models\NewsGroup;
use App\Support\Enums\PermissionsEnum as PERM;
use App\Support\Enums\RolesEnum as ROLES;
use App\Support\Enums\DocumentGroups as D_GROUPS;
use App\Support\Enums\NewsGroups as N_GROUPS;
use App\Support\Enums\TtDaysEnum as TT_DAYS;
use App\Support\Enums\TtPartsEnum as TT_PARTS;
use App\Support\Enums\TtGroupTypesEnum as TT_GT;

/**
 * Class InitDBConstantsCommand
 * @package App\Console\Commands
 */
class InitDBConstantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:init-const';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize all tables with constants';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');



        //region DATA
        $users = [
            [
                'name' => 'admin1',
                'email' => 'admin1@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin2',
                'email' => 'admin2@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin3',
                'email' => 'admin3@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin4',
                'email' => 'admin4@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin5',
                'email' => 'admin5@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin6',
                'email' => 'admin6@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin7',
                'email' => 'admin7@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin8',
                'email' => 'admin8@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin9',
                'email' => 'admin9@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin10',
                'email' => 'admin10@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin11',
                'email' => 'admin11@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin12',
                'email' => 'admin12@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin13',
                'email' => 'admin13@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'admin14',
                'email' => 'admin14@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::ADMIN,
            ],
            [
                'name' => 'superadmin',
                'email' => 'superadmin@mail.com',
                'password' => Hash::make('123123123'),
                'role' => ROLES::SUPER_ADMIN,
            ],
        ];

        $permissions = PERM::getConstants();

        $roles = ROLES::getConstants();

        $documentGroups = D_GROUPS::getDocumentGroups();

        $kindergartens = [
            [
                'title' => '№ 1 "Червона шапочка"',
                'address' => '85206, Донецька область, м. Торецьк, вул. Терешкової, будинок 6',
                'lat' => 48.397447,
                'lng' => 37.877397,
                'user' => $users[array_search('admin1@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 3 "Струмок"',
                'address' => '85206, Донецька область, м. Торецьк, вул. Центральна, буд.29',
                'lat' => 48.349763,
                'lng' => 37.843399,
                'user' => $users[array_search('admin2@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 4 "Дзвіночок"',
                'address' => '85287, Донецька область, м. Торецьк, смт. Петрівка, вул. Сонячна, буд. 41',
                'lat' => 48.419824,
                'lng' => 37.762801,
                'user' => $users[array_search('admin3@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 5 "Дзвіночок" (ясла-садок)',
                'address' => '85280, Донецька область, м. Торецьк, смт Північне, вул. Юності, буд.25',
                'lat' => 48.386420,
                'lng' => 37.906753,
                'user' => $users[array_search('admin4@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 6 "Сонечко"',
                'address' => '85206, Донецька область, м. Торецьк, вул. Лісова',
                'lat' => 48.628060,
                'lng' => 37.508999,
                'user' => $users[array_search('admin5@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 7 "Мир"',
                'address' => '85207, Донецька область, м. Торецьк, вул. 8 Березня, буд. 7',
                'lat' => 48.380877,
                'lng' => 37.836850,
                'user' => $users[array_search('admin6@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 8 "Золотий ключик"',
                'address' => '85200, Донецька область, м. Торецьк, вул. Маяковського будинок 20-А',
                'lat' => 48.394295,
                'lng' => 37.849857,
                'user' => $users[array_search('admin7@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 9 "Веселка"',
                'address' => '85200, Донецька область, м. Торецьк, вул. Маяковського',
                'lat' => 48.394537,
                'lng' => 37.848612,
                'user' => $users[array_search('admin8@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 10 "Малятко"',
                'address' => '85280, Донецька область, м. Торецьк, смт Північне, вул.Нова, буд.6',
                'lat' => 48.385465,
                'lng' => 37.905905,
                'user' => $users[array_search('admin9@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 11 "Казка"',
                'address' => '85200, Донецька область, м. Торецьк, вул. ім. Івана Карабиця 9',
                'lat' => 48.393251,
                'lng' => 37.854056,
                'user' => $users[array_search('admin10@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 12 "Мрія"',
                'address' => '85280, Донецька область, м. Торецьк, вул. Глінки 1 А',
                'lat' => 48.394252,
                'lng' => 37.850071,
                'user' => $users[array_search('admin11@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 14 "Дивоцвіт"',
                'address' => '85295, Донецька область, м. Торецьк, смт Новгородське, вул. Молодіжна 29 А',
                'lat' => 48.395017,
                'lng' => 37.851313,
                'user' => $users[array_search('admin12@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '№ 15 "Золота рибка"',
                'address' => '85297, Донецька область, м. Торецьк, вул. Воїнів - Інтернаціоналістів 1',
                'lat' => 48.397260,
                'lng' => 37.849708,
                'user' => $users[array_search('admin13@mail.com', array_column($users, 'email'))],
            ],
            [
                'title' => '«Інклюзивно-ресурсний центр»',
                'address' => '85200, Донецька область, м. Торецьк, вул. Маяковського',
                'lat' => 48.394537,
                'lng' => 37.848612,
                'user' => $users[array_search('admin14@mail.com', array_column($users, 'email'))],
            ],

        ];

        $ttdays = TT_DAYS::getConstants();

        $ttparts = TT_PARTS::getConstants();

        $ttgroupTypes = TT_GT::getConstants();

        $newsGroups = N_GROUPS::getNewsGroups();
        //endregion



        //region TRUNCATE
        User::truncate();
        Permission::truncate();
        Role::truncate();
        DB::table('roles_permissions')->truncate();
        DB::table('users_roles')->truncate();
        DocumentGroup::truncate();
        Kindergarten::truncate();
        TtDay::truncate();
        TtPart::truncate();
        TtGroupType::truncate();
        NewsGroup::truncate();
        //endregion



        //region DATA FILLING
        foreach ($permissions as $permission)
            Permission::create($permission);

        foreach ($roles as $r) {
            $role = new Role();
            $role->title = $r['title'];
            $role->slug = $r['slug'];
            $role->save();

            foreach ($r['permissions'] as $permission) {
                $foundPermission = Permission::where('slug', $permission['slug'])->first();
                $role->permissions()->attach($foundPermission);
            }
        }

        foreach ($users as $u) {
            $user = new User($u);
            $user->save();

            $foundRole = Role::where('slug', $u['role']['slug'])->first();
            $user->roles()->attach($foundRole);
        }

        foreach ($documentGroups as $documentGroup)
            DocumentGroup::create($documentGroup);

        foreach ($kindergartens as $k) {
            $kindergarten = new Kindergarten($k);

            $foundUser = User::where('email', $k['user']['email'])->first();
            $kindergarten->user()->associate($foundUser);

            $kindergarten->save();
        }

        foreach ($ttdays as $ttday)
            TtDay::create($ttday);

        foreach ($ttparts as $ttpart)
            TtPart::create($ttpart);

        foreach ($ttgroupTypes as $ttgroupType)
            TtGroupType::create($ttgroupType);

        foreach ($newsGroups as $group)
            NewsGroup::create($group);
        //endregion



        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return 0;
    }
}
