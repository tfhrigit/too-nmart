<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * =========================
         * ROLE DIRECTIVE
         * =========================
         * @role('owner')
         * @role(['owner', 'kasir'])
         * @role('owner,kasir')
         */
        Blade::if('role', function ($role) {
            $user = auth()->user();
            if (!$user) {
                return false;
            }

            $roles = is_array($role)
                ? $role
                : array_map('trim', explode(',', $role));

            return in_array($user->role, $roles);
        });

        /**
         * =========================
         * NOT ROLE DIRECTIVE
         * =========================
         * @notrole('kasir')
         * @notrole(['kasir','staff_gudang'])
         */
        Blade::if('notrole', function ($role) {
            $user = auth()->user();
            if (!$user) {
                return true;
            }

            $roles = is_array($role)
                ? $role
                : array_map('trim', explode(',', $role));

            return !in_array($user->role, $roles);
        });

        /**
         * =========================
         * PERMISSION DIRECTIVE
         * =========================
         * @permission('barang.create')
         */
        Blade::if('permission', function ($permission) {
            $user = auth()->user();
            if (!$user) {
                return false;
            }

            return $user->hasAccess($permission);
        });

        /**
         * =========================
         * ROLE NAME HELPER
         * =========================
         * @roleName
         */
        Blade::directive('roleName', function () {
            return "<?php echo auth()->user()?->role_name ?? ''; ?>";
        });
    }
}
