<?php

app('router')->setCompiledRoutes(
    array (
  'compiled' => 
  array (
    0 => false,
    1 => 
    array (
      '/login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'login.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/logout' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'logout',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/confirm-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.confirm',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'password.confirm.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/confirmed-password-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.confirmation',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/two-factor-challenge' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.login.store',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/two-factor-authentication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.enable',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.disable',
          ),
          1 => NULL,
          2 => 
          array (
            'DELETE' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/confirmed-two-factor-authentication' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.confirm',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/two-factor-qr-code' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.qr-code',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/two-factor-secret-key' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.secret-key',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/user/two-factor-recovery-codes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.recovery-codes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
        1 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.regenerate-recovery-codes',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/flux/flux.js' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::IhF59WlqPIvVGXh7',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/flux/flux.min.js' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::37DAhng9X8Y0FagU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/flux/editor.css' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::FVGsf4kmexPnwfxD',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/flux/editor.js' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::lh9uGKPx8fAiaeJJ',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/flux/editor.min.js' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7MJ2cjNfuKuaNEzp',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/livewire/update' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'livewire.update',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/livewire/livewire.js' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::tUPMPDN8uNBV7rmh',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/livewire/livewire.min.js.map' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::7zgyhr2Q6EWRp0QU',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/livewire/upload-file' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'livewire.upload-file',
          ),
          1 => NULL,
          2 => 
          array (
            'POST' => 0,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/up' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::sfMeqpnNwoIEdjH5',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'home',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'generated::0GnytSN6BJG25uAp',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/profile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'settings.profile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'settings.password',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/appearance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'settings.appearance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/settings/two-factor' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'two-factor.show',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/forgot-password' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.request',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/staff-login' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'staff-login',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/verify-email' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.notice',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/roles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/branches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::dbvNEOPh7qiJygl6',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/deleted-branch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::DlRlF3rXqj4thdEl',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/settings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::3Ha5QP8JYJb9WKws',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/settings/*' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::lTEORVyQ6Kq0H8OA',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/md-reports' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::tYUUoaLS7HNctLT0',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/super-admin/md-reports/*' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'super-admin.generated::yLkFgVJqves2qYEy',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
            'POST' => 2,
            'PUT' => 3,
            'PATCH' => 4,
            'DELETE' => 5,
            'OPTIONS' => 6,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/router' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboards.router',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboards/super-admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboards.super-admin',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboards/admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboards.admin',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboards/manager' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboards.manager',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboards/supervisor' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboards.supervisor',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/inventory' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.inventory',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/corner-store' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.corner-store',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/hr' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.hr',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.admin',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/dashboard/super-admin' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.super-admin',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/employees' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/employee/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/employee-appraisals' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee-appraisals',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/clock-in-board' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.clock-in-board.today',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/clock-in-board/all' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.clock-in-board.all',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/hr/appraisals/cycles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.hr.appraisals.cycles',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/types' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.types',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/apply' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.apply',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/my-leaves' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.my-leaves',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/approve' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.approve',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/balance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.balance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/leave/manage-allocations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.leave.manage-allocations',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/departments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.branch.departments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/department/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.department.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/departments/category' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.branch.departments.category',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/department/category/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.department.category.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/role-assignments' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.role-assignments.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/role-permisssion' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.role-permission',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/roles' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.roles.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/branches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.branches.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/deleted-branches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.branches.deleted',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/settings' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.settings.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/md-reports/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.md-reports.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/organization/helper' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.organization.helper',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/helper' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.helper',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/sales-dashboard/helper' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.helper',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/helper' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.helper',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/auth/shift' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.select_shift',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/items' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.items',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/purchases' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.purchases',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/stocks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.stocks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/item-requests' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.item-requests',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/item-dispatches' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.item-dispatches',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/stock-takes' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.stock-takes',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/health-checks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.health-checks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/shift-closing' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.shift-closing',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/callbacks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.callbacks.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/reports/stock-levels' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.reports.stock-levels',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/reports/stock-movement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.reports.stock-movement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/reports/turnover' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.reports.turnover',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/reports/reorder' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.reports.reorder',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/inventory/reports/variance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.inventory.reports.variance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/suppliers' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.suppliers.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/suppliers/create' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.suppliers.create',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/module' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.module.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/raw-material-tracking' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.raw-material-tracking',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/callbacks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.callbacks.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/callbacks/create-inventory' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.callbacks.create-inventory',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/callbacks/approve-sales-callbacks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.callbacks.approve-sales-callbacks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/operations' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.operations',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/performance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.performance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/planning' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.planning',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/efficiency' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.efficiency',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/quality' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.quality',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/waste' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.waste',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/cost' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.cost',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/recipe-performance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.recipe-performance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/shift-summary' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.shift-summary',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/ingredient-utilization' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.ingredient-utilization',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/pipeline' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.pipeline',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/production/reports/capacity' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.reports.capacity',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/overview' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.overview',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/stock-level' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.stock-level',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/stock-movement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.stock-movement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/purchase' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.purchase',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/request-dispatch' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.request-dispatch',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/alerts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.alerts',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/analytics/stock-valuation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.analytics.stock-valuation',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/exports/stock-level-analytics' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.exports.stock-level-analytics',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/exports/health-checks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.exports.health-checks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/exports/item-requests' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.exports.item-requests',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/reporting/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.reporting.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/reporting/review' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.reporting.review',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/reporting/compile' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.reporting.compile',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/reporting/send-to-md' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.reporting.send-to-md',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/audit' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.audit.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/audit/inventory-approvals' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.audit.inventory-approvals',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/dashboard' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.dashboard',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/overview' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.overview',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/accounts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.accounts',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/periods' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.periods',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/journal-entry' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.journal-entry',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/posting-status' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.posting-status',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/inventory-valuation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.inventory-valuation',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/bank-reconciliation' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.bank-reconciliation',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports/general-ledger' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.general-ledger',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports/trial-balance' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.trial-balance',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports/income-statement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.income-statement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports/balance-sheet' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.balance-sheet',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/accounting/reports/cash-flow-statement' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.accounting.reports.cash-flow-statement',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/sales-dashboard/expiry-alerts' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.expiry-alerts',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/sales-dashboard/callbacks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.callbacks.index',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/sales-dashboard/callbacks/dispatch-callbacks' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.callbacks.dispatch-callbacks',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      '/branch-dashboard/sales-dashboard/stock-monitor' => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.stock-monitor',
          ),
          1 => NULL,
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
    ),
    2 => 
    array (
      0 => '{^(?|/tallstackui/s(?|cript(?:/([^/]++))?(*:43)|tyle(?:/([^/]++))?(*:68))|/livewire/preview\\-file/([^/]++)(*:108)|/reset\\-password/([^/]++)(*:141)|/verify\\-email/([^/]++)/([^/]++)(*:181)|/branch\\-dashboard/(?|d(?|ashboard/(?|production(?:/([^/]++))?(*:251)|sales(?:/([^/]++))?(*:278))|epartment/(?|([^/]++)/edit(*:313)|category/([^/]++)/edit(*:343)))|employee(?|/([^/]++)/(?|edit(*:381)|([^/]++)(*:397))|\\-appraisal\\-history/([^/]++)(*:435))|appraise\\-employee/([^/]++)(*:471)|clock\\-in\\-board/employee/([^/]++)/history(*:521)|md\\-reports/view/([^/]++)(*:554)|s(?|uppliers/([^/]++)(?|(*:586)|/performance(*:606))|ales\\-dashboard/(?|s(?|tock\\-opening(?:/([^/]++))?(*:665)|hift\\-closing(?:/([^/]++))?(*:700))|dispatches(?:/([^/]++))?(*:733)|pos(?:/([^/]++))?(*:758)|analytics(?:/([^/]++))?(*:789)|my\\-sales(?:/([^/]++))?(*:820)))|production/(?|product(?|\\-types(?:/([^/]++))?(*:875)|s(?:/([^/]++))?(*:898))|re(?|quest(?|(?:/([^/]++))?(*:934)|/([^/]++)/create(*:958))|cipes(?|(?:/([^/]++))?(*:989)|/([^/]++)/(?|add(*:1013)|([^/]++)(?|/edit(*:1038)|(*:1047)))))|daily\\-produce/([^/]++)(*:1083)|shift\\-closing/([^/]++)(*:1115))|reporting/compiled/([^/]++)(*:1152))|/storage/(.*)(*:1175))/?$}sDu',
    ),
    3 => 
    array (
      43 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tallstackui.script',
            'file' => NULL,
          ),
          1 => 
          array (
            0 => 'file',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      68 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'tallstackui.style',
            'file' => NULL,
          ),
          1 => 
          array (
            0 => 'file',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      108 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'livewire.preview-file',
          ),
          1 => 
          array (
            0 => 'filename',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      141 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'password.reset',
          ),
          1 => 
          array (
            0 => 'token',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      181 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'verification.verify',
          ),
          1 => 
          array (
            0 => 'id',
            1 => 'hash',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      251 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.production',
            'deptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      278 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.dashboard.sales',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      313 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.department.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      343 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.department.category.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      381 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee.edit',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      397 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee.details',
            'employee_number' => NULL,
          ),
          1 => 
          array (
            0 => 'employee_number',
            1 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      435 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.employee-appraisal-history',
          ),
          1 => 
          array (
            0 => 'employee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      471 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.appraise-employee',
          ),
          1 => 
          array (
            0 => 'employee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      521 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.clock-in-board.employee-history',
          ),
          1 => 
          array (
            0 => 'employee',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      554 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.md-reports.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      586 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.suppliers.show',
          ),
          1 => 
          array (
            0 => 'supplier',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      606 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.suppliers.performance',
          ),
          1 => 
          array (
            0 => 'supplier',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      665 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.stock-opening.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      700 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.shift-closing.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      733 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.dispatches.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      758 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.pos.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      789 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.analytics.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      820 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.sales-dashboard.my-sales.index',
            'salesDeptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'salesDeptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      875 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.product-types',
            'deptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      898 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.products',
            'deptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      934 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.request.index',
            'deptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      958 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.request.create',
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      989 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.recipes.index',
            'deptSlug' => NULL,
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1013 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.recipes.add',
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1038 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.recipes.edit',
          ),
          1 => 
          array (
            0 => 'deptSlug',
            1 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => false,
          6 => NULL,
        ),
      ),
      1047 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.recipes.detail',
          ),
          1 => 
          array (
            0 => 'deptSlug',
            1 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1083 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.daily-produce.index',
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1115 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.production.shift-closing.index',
          ),
          1 => 
          array (
            0 => 'deptSlug',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1152 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'branch-dashboard.reporting.compiled.view',
          ),
          1 => 
          array (
            0 => 'id',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
      ),
      1175 => 
      array (
        0 => 
        array (
          0 => 
          array (
            '_route' => 'storage.local',
          ),
          1 => 
          array (
            0 => 'path',
          ),
          2 => 
          array (
            'GET' => 0,
            'HEAD' => 1,
          ),
          3 => NULL,
          4 => false,
          5 => true,
          6 => NULL,
        ),
        1 => 
        array (
          0 => NULL,
          1 => NULL,
          2 => NULL,
          3 => NULL,
          4 => false,
          5 => false,
          6 => 0,
        ),
      ),
    ),
    4 => NULL,
  ),
  'attributes' => 
  array (
    'tallstackui.script' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'tallstackui/script/{file?}',
      'action' => 
      array (
        'uses' => 'TallStackUi\\Foundation\\Http\\Controllers\\TallStackUiAssetsController@script',
        'controller' => 'TallStackUi\\Foundation\\Http\\Controllers\\TallStackUiAssetsController@script',
        'as' => 'tallstackui.script',
        'namespace' => NULL,
        'prefix' => '/tallstackui',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'tallstackui.style' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'tallstackui/style/{file?}',
      'action' => 
      array (
        'uses' => 'TallStackUi\\Foundation\\Http\\Controllers\\TallStackUiAssetsController@style',
        'controller' => 'TallStackUi\\Foundation\\Http\\Controllers\\TallStackUiAssetsController@style',
        'as' => 'tallstackui.style',
        'namespace' => NULL,
        'prefix' => '/tallstackui',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Livewire\\Auth\\Login@__invoke',
        'controller' => 'App\\Livewire\\Auth\\Login',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'login.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'login',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest:web',
          2 => 'throttle:login',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\AuthenticatedSessionController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\AuthenticatedSessionController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'login.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'logout' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'logout',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Livewire\\Actions\\Logout@__invoke',
        'controller' => 'App\\Livewire\\Actions\\Logout',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'logout',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.confirm' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user/confirm-password',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmablePasswordController@show',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmablePasswordController@show',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.confirm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.confirmation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user/confirmed-password-status',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmedPasswordStatusController@show',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmedPasswordStatusController@show',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.confirmation',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.confirm.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'user/confirm-password',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmablePasswordController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmablePasswordController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.confirm.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'two-factor-challenge',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest:web',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticatedSessionController@create',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticatedSessionController@create',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.login.store' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'two-factor-challenge',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest:web',
          2 => 'throttle:two-factor',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticatedSessionController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticatedSessionController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.login.store',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.enable' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'user/two-factor-authentication',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticationController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticationController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.enable',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.confirm' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'user/confirmed-two-factor-authentication',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmedTwoFactorAuthenticationController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\ConfirmedTwoFactorAuthenticationController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.confirm',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.disable' => 
    array (
      'methods' => 
      array (
        0 => 'DELETE',
      ),
      'uri' => 'user/two-factor-authentication',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticationController@destroy',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorAuthenticationController@destroy',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.disable',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.qr-code' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user/two-factor-qr-code',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorQrCodeController@show',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorQrCodeController@show',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.qr-code',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.secret-key' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user/two-factor-secret-key',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorSecretKeyController@show',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\TwoFactorSecretKeyController@show',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.secret-key',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.recovery-codes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'user/two-factor-recovery-codes',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\RecoveryCodeController@index',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\RecoveryCodeController@index',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.recovery-codes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.regenerate-recovery-codes' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'user/two-factor-recovery-codes',
      'action' => 
      array (
        'domain' => NULL,
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth:web',
          2 => 'password.confirm',
        ),
        'uses' => 'Laravel\\Fortify\\Http\\Controllers\\RecoveryCodeController@store',
        'controller' => 'Laravel\\Fortify\\Http\\Controllers\\RecoveryCodeController@store',
        'namespace' => 'Laravel\\Fortify\\Http\\Controllers',
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.regenerate-recovery-codes',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::IhF59WlqPIvVGXh7' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'flux/flux.js',
      'action' => 
      array (
        'uses' => 'Flux\\AssetManager@fluxJs',
        'controller' => 'Flux\\AssetManager@fluxJs',
        'as' => 'generated::IhF59WlqPIvVGXh7',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::37DAhng9X8Y0FagU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'flux/flux.min.js',
      'action' => 
      array (
        'uses' => 'Flux\\AssetManager@fluxMinJs',
        'controller' => 'Flux\\AssetManager@fluxMinJs',
        'as' => 'generated::37DAhng9X8Y0FagU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::FVGsf4kmexPnwfxD' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'flux/editor.css',
      'action' => 
      array (
        'uses' => 'Flux\\AssetManager@editorCss',
        'controller' => 'Flux\\AssetManager@editorCss',
        'as' => 'generated::FVGsf4kmexPnwfxD',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::lh9uGKPx8fAiaeJJ' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'flux/editor.js',
      'action' => 
      array (
        'uses' => 'Flux\\AssetManager@editorJs',
        'controller' => 'Flux\\AssetManager@editorJs',
        'as' => 'generated::lh9uGKPx8fAiaeJJ',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7MJ2cjNfuKuaNEzp' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'flux/editor.min.js',
      'action' => 
      array (
        'uses' => 'Flux\\AssetManager@editorMinJs',
        'controller' => 'Flux\\AssetManager@editorMinJs',
        'as' => 'generated::7MJ2cjNfuKuaNEzp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'livewire.update' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'livewire/update',
      'action' => 
      array (
        'uses' => 'Livewire\\Mechanisms\\HandleRequests\\HandleRequests@handleUpdate',
        'controller' => 'Livewire\\Mechanisms\\HandleRequests\\HandleRequests@handleUpdate',
        'middleware' => 
        array (
          0 => 'web',
        ),
        'as' => 'livewire.update',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::tUPMPDN8uNBV7rmh' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'livewire/livewire.js',
      'action' => 
      array (
        'uses' => 'Livewire\\Mechanisms\\FrontendAssets\\FrontendAssets@returnJavaScriptAsFile',
        'controller' => 'Livewire\\Mechanisms\\FrontendAssets\\FrontendAssets@returnJavaScriptAsFile',
        'as' => 'generated::tUPMPDN8uNBV7rmh',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::7zgyhr2Q6EWRp0QU' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'livewire/livewire.min.js.map',
      'action' => 
      array (
        'uses' => 'Livewire\\Mechanisms\\FrontendAssets\\FrontendAssets@maps',
        'controller' => 'Livewire\\Mechanisms\\FrontendAssets\\FrontendAssets@maps',
        'as' => 'generated::7zgyhr2Q6EWRp0QU',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'livewire.upload-file' => 
    array (
      'methods' => 
      array (
        0 => 'POST',
      ),
      'uri' => 'livewire/upload-file',
      'action' => 
      array (
        'uses' => 'Livewire\\Features\\SupportFileUploads\\FileUploadController@handle',
        'controller' => 'Livewire\\Features\\SupportFileUploads\\FileUploadController@handle',
        'as' => 'livewire.upload-file',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'livewire.preview-file' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'livewire/preview-file/{filename}',
      'action' => 
      array (
        'uses' => 'Livewire\\Features\\SupportFileUploads\\FilePreviewController@handle',
        'controller' => 'Livewire\\Features\\SupportFileUploads\\FilePreviewController@handle',
        'as' => 'livewire.preview-file',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::sfMeqpnNwoIEdjH5' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'up',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:825:"function () {
                    $exception = null;

                    try {
                        \\Illuminate\\Support\\Facades\\Event::dispatch(new \\Illuminate\\Foundation\\Events\\DiagnosingHealth);
                    } catch (\\Throwable $e) {
                        if (app()->hasDebugModeEnabled()) {
                            throw $e;
                        }

                        report($e);

                        $exception = $e->getMessage();
                    }

                    return response(\\Illuminate\\Support\\Facades\\View::file(\'/home/ilem/Documents/sweettooth/vendor/laravel/framework/src/Illuminate/Foundation/Configuration\'.\'/../resources/health-up.blade.php\', [
                        \'exception\' => $exception,
                    ]), status: $exception ? 500 : 200);
                }";s:5:"scope";s:54:"Illuminate\\Foundation\\Configuration\\ApplicationBuilder";s:4:"this";N;s:4:"self";s:32:"0000000000000d900000000000000000";}}',
        'as' => 'generated::sfMeqpnNwoIEdjH5',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'home' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => '/',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
        ),
        'uses' => 'App\\Livewire\\Auth\\LoginOption@__invoke',
        'controller' => 'App\\Livewire\\Auth\\LoginOption',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'home',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'verified',
        ),
        'uses' => '\\Illuminate\\Routing\\ViewController@__invoke',
        'controller' => '\\Illuminate\\Routing\\ViewController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'dashboard',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'view' => 'dashboard',
        'data' => 
        array (
        ),
        'status' => 200,
        'headers' => 
        array (
        ),
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'generated::0GnytSN6BJG25uAp' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'generated::0GnytSN6BJG25uAp',
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => 'settings/profile',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'settings.profile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/profile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Livewire\\Settings\\Profile@__invoke',
        'controller' => 'App\\Livewire\\Settings\\Profile',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'settings.profile',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'settings.password' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Livewire\\Settings\\Password@__invoke',
        'controller' => 'App\\Livewire\\Settings\\Password',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'settings.password',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'settings.appearance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/appearance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Livewire\\Settings\\Appearance@__invoke',
        'controller' => 'App\\Livewire\\Settings\\Appearance',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'settings.appearance',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'two-factor.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'settings/two-factor',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'password.confirm',
        ),
        'uses' => 'App\\Livewire\\Settings\\TwoFactor@__invoke',
        'controller' => 'App\\Livewire\\Settings\\TwoFactor',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'two-factor.show',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.request' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'forgot-password',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Livewire\\Auth\\ForgotPassword@__invoke',
        'controller' => 'App\\Livewire\\Auth\\ForgotPassword',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.request',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'password.reset' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'reset-password/{token}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Livewire\\Auth\\ResetPassword@__invoke',
        'controller' => 'App\\Livewire\\Auth\\ResetPassword',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'password.reset',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'staff-login' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'staff-login',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'guest',
        ),
        'uses' => 'App\\Livewire\\Auth\\StaffLogin@__invoke',
        'controller' => 'App\\Livewire\\Auth\\StaffLogin',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'staff-login',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verification.notice' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => 'App\\Livewire\\Auth\\VerifyEmail@__invoke',
        'controller' => 'App\\Livewire\\Auth\\VerifyEmail',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.notice',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'verification.verify' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'verify-email/{id}/{hash}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'signed',
          3 => 'throttle:6,1',
        ),
        'uses' => 'App\\Http\\Controllers\\Auth\\VerifyEmailController@__invoke',
        'controller' => 'App\\Http\\Controllers\\Auth\\VerifyEmailController',
        'namespace' => NULL,
        'prefix' => '',
        'where' => 
        array (
        ),
        'as' => 'verification.verify',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/roles',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::dbvNEOPh7qiJygl6' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/branches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::dbvNEOPh7qiJygl6',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/branches',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::DlRlF3rXqj4thdEl' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/deleted-branch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::DlRlF3rXqj4thdEl',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/deleted-branches',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::3Ha5QP8JYJb9WKws' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::3Ha5QP8JYJb9WKws',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/settings',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::lTEORVyQ6Kq0H8OA' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/settings/*',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::lTEORVyQ6Kq0H8OA',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/settings',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::tYUUoaLS7HNctLT0' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/md-reports',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::tYUUoaLS7HNctLT0',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/md-reports/dashboard',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'super-admin.generated::yLkFgVJqves2qYEy' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
        2 => 'POST',
        3 => 'PUT',
        4 => 'PATCH',
        5 => 'DELETE',
        6 => 'OPTIONS',
      ),
      'uri' => 'super-admin/md-reports/*',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
        ),
        'uses' => '\\Illuminate\\Routing\\RedirectController@__invoke',
        'controller' => '\\Illuminate\\Routing\\RedirectController',
        'as' => 'super-admin.generated::yLkFgVJqves2qYEy',
        'namespace' => NULL,
        'prefix' => '/super-admin',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
        'destination' => '/branch-dashboard/md-reports/{1}',
        'status' => 302,
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboards.router' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/router',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Dashboards\\Router@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Dashboards\\Router',
        'as' => 'branch-dashboard.dashboards.router',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboards.super-admin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboards/super-admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Dashboards\\SuperAdminDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Dashboards\\SuperAdminDashboard',
        'as' => 'branch-dashboard.dashboards.super-admin',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboards.admin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboards/admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Dashboards\\AdminDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Dashboards\\AdminDashboard',
        'as' => 'branch-dashboard.dashboards.admin',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboards.manager' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboards/manager',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Dashboards\\ManagerDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Dashboards\\ManagerDashboard',
        'as' => 'branch-dashboard.dashboards.manager',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboards.supervisor' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboards/supervisor',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Dashboards\\SupervisorDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Dashboards\\SupervisorDashboard',
        'as' => 'branch-dashboard.dashboards.supervisor',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.inventory' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/inventory',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view_inventory_dashboard',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\InventoryDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\InventoryDashboard',
        'as' => 'branch-dashboard.dashboard.inventory',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.production' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/production/{deptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view_production_dashboard',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\ProductionDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\ProductionDashboard',
        'as' => 'branch-dashboard.dashboard.production',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.sales' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/sales/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view-sales-dashboard',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\SalesDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\SalesDashboard',
        'as' => 'branch-dashboard.dashboard.sales',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.corner-store' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/corner-store',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view-sales-dashboard',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\CornerStoreDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\CornerStoreDashboard',
        'as' => 'branch-dashboard.dashboard.corner-store',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.hr' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/hr',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\HRDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\HRDashboard',
        'as' => 'branch-dashboard.dashboard.hr',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.admin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_branches',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\BranchAdminDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\BranchAdminDashboard',
        'as' => 'branch-dashboard.dashboard.admin',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.dashboard.super-admin' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/dashboard/super-admin',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_system',
        ),
        'uses' => 'App\\Livewire\\Dashboards\\SuperAdminDashboard@__invoke',
        'controller' => 'App\\Livewire\\Dashboards\\SuperAdminDashboard',
        'as' => 'branch-dashboard.dashboard.super-admin',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:0:{}s:8:"function";s:298:"function () {
        $branchId = \\request()->query(\'b_id\') ?? \\current_branch_id();
        if ($branchId) {
            return \\redirect()->route(\'branch-dashboard.dashboards.router\', [\'b_id\' => $branchId]);
        }
        return \\redirect()->route(\'branch-dashboard.dashboards.router\');
    }";s:5:"scope";s:37:"Illuminate\\Routing\\RouteFileRegistrar";s:4:"this";N;s:4:"self";s:32:"0000000000000d700000000000000000";}}',
        'as' => 'branch-dashboard.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employees',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Index',
        'as' => 'branch-dashboard.employee.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employee/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Create@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Create',
        'as' => 'branch-dashboard.employee.create',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employee/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Edit@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Edit',
        'as' => 'branch-dashboard.employee.edit',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee.details' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employee/{employee_number?}/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Details@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\Details',
        'as' => 'branch-dashboard.employee.details',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee-appraisals' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employee-appraisals',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeAppraisals@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeAppraisals',
        'as' => 'branch-dashboard.employee-appraisals',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.employee-appraisal-history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/employee-appraisal-history/{employee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeAppraisalHistory@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeAppraisalHistory',
        'as' => 'branch-dashboard.employee-appraisal-history',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.appraise-employee' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/appraise-employee/{employee}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\AppraiseEmployee@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\AppraiseEmployee',
        'as' => 'branch-dashboard.appraise-employee',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.clock-in-board.today' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/clock-in-board',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\TodayIndex@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\TodayIndex',
        'as' => 'branch-dashboard.clock-in-board.today',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/clock-in-board',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.clock-in-board.all' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/clock-in-board/all',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\GeneralClockInBoard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\GeneralClockInBoard',
        'as' => 'branch-dashboard.clock-in-board.all',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/clock-in-board',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.clock-in-board.employee-history' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/clock-in-board/employee/{employee}/history',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\EmployeeHistory@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\ClockInModule\\EmployeeHistory',
        'as' => 'branch-dashboard.clock-in-board.employee-history',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/clock-in-board',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.hr.appraisals.cycles' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/hr/appraisals/cycles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\HR\\AppraisalCycles@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\HR\\AppraisalCycles',
        'as' => 'branch-dashboard.hr.appraisals.cycles',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/hr/appraisals',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.types' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/types',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\LeaveTypes@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\LeaveTypes',
        'as' => 'branch-dashboard.leave.types',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.apply' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/apply',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ApplyLeave@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ApplyLeave',
        'as' => 'branch-dashboard.leave.apply',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.my-leaves' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/my-leaves',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\MyLeaves@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\MyLeaves',
        'as' => 'branch-dashboard.leave.my-leaves',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.approve' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/approve',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ApproveLeave@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ApproveLeave',
        'as' => 'branch-dashboard.leave.approve',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.balance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/balance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\LeaveBalance@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\LeaveBalance',
        'as' => 'branch-dashboard.leave.balance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.leave.manage-allocations' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/leave/manage-allocations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ManageAllocations@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\LeaveManagement\\ManageAllocations',
        'as' => 'branch-dashboard.leave.manage-allocations',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/leave',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.branch.departments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/departments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Index',
        'as' => 'branch-dashboard.branch.departments.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.department.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/department/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Department\\CreateOrUpdate@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Department\\CreateOrUpdate',
        'as' => 'branch-dashboard.department.create',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.department.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/department/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Department\\CreateOrUpdate@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Department\\CreateOrUpdate',
        'as' => 'branch-dashboard.department.edit',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.branch.departments.category' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/departments/category',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Category@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Category',
        'as' => 'branch-dashboard.branch.departments.category',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.department.category.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/department/category/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Cartegory\\Create@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Cartegory\\Create',
        'as' => 'branch-dashboard.department.category.create',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.department.category.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/department/category/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Cartegory\\Edit@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\DepartmentModule\\Cartegory\\Edit',
        'as' => 'branch-dashboard.department.category.edit',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.role-assignments.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/role-assignments',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\RolePermission\\AssignRole@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\RolePermission\\AssignRole',
        'as' => 'branch-dashboard.role-assignments.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.role-permission' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/role-permisssion',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_organization',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\RolePermission\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\EmployeeModule\\RolePermission\\Index',
        'as' => 'branch-dashboard.role-permission',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.roles.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/roles',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_roles',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Roles\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Roles\\Index',
        'as' => 'branch-dashboard.roles.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.branches.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/branches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_branches',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Branches\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Branches\\Index',
        'as' => 'branch-dashboard.branches.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.branches.deleted' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/deleted-branches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_branches',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Branches\\DeleteBranch@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Branches\\DeleteBranch',
        'as' => 'branch-dashboard.branches.deleted',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.settings.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/settings',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:manage_settings',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Settings\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Settings\\Index',
        'as' => 'branch-dashboard.settings.index',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.md-reports.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/md-reports/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\MDReports\\Dashboard\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\MDReports\\Dashboard\\Index',
        'as' => 'branch-dashboard.md-reports.dashboard',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/md-reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.md-reports.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/md-reports/view/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'role_or_permission:view_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\MDReports\\ViewReport\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\MDReports\\ViewReport\\Index',
        'as' => 'branch-dashboard.md-reports.view',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/md-reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.organization.helper' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/organization/helper',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Organization\\Helper@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Organization\\Helper',
        'as' => 'branch-dashboard.organization.helper',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.helper' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/helper',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Helper@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Helper',
        'as' => 'branch-dashboard.inventory.helper',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.helper' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/helper',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Helper@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Helper',
        'as' => 'branch-dashboard.sales-dashboard.helper',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.helper' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/helper',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Helper@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Helper',
        'as' => 'branch-dashboard.production.helper',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.select_shift' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/auth/shift',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
        ),
        'uses' => 'App\\Livewire\\Auth\\Shift@__invoke',
        'controller' => 'App\\Livewire\\Auth\\Shift',
        'as' => 'branch-dashboard.select_shift',
        'namespace' => NULL,
        'prefix' => '/branch-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.items' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/items',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Items@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Items',
        'as' => 'branch-dashboard.inventory.items',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.purchases' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/purchases',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Purchases@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Purchases',
        'as' => 'branch-dashboard.inventory.purchases',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.stocks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/stocks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Stocks@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Stocks',
        'as' => 'branch-dashboard.inventory.stocks',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.item-requests' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/item-requests',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\ItemRequests@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\ItemRequests',
        'as' => 'branch-dashboard.inventory.item-requests',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.item-dispatches' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/item-dispatches',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\ItemDispatches@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\ItemDispatches',
        'as' => 'branch-dashboard.inventory.item-dispatches',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.stock-takes' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/stock-takes',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\StockTakes@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\StockTakes',
        'as' => 'branch-dashboard.inventory.stock-takes',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.health-checks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/health-checks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\HealthChecks@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\HealthChecks',
        'as' => 'branch-dashboard.inventory.health-checks',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.shift-closing' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/shift-closing',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\ShiftClosing\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\ShiftClosing\\Index',
        'as' => 'branch-dashboard.inventory.shift-closing',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.callbacks.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/callbacks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Callbacks\\ApproveCallbacks@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Callbacks\\ApproveCallbacks',
        'as' => 'branch-dashboard.inventory.callbacks.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.reports.stock-levels' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/reports/stock-levels',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockLevels\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockLevels\\Index',
        'as' => 'branch-dashboard.inventory.reports.stock-levels',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.reports.stock-movement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/reports/stock-movement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockMovement\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockMovement\\Index',
        'as' => 'branch-dashboard.inventory.reports.stock-movement',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.reports.turnover' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/reports/turnover',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockTurnover\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\StockTurnover\\Index',
        'as' => 'branch-dashboard.inventory.reports.turnover',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.reports.reorder' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/reports/reorder',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\Reorder\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\Reorder\\Index',
        'as' => 'branch-dashboard.inventory.reports.reorder',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.inventory.reports.variance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/inventory/reports/variance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\Variance\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Inventory\\Reports\\Variance\\Index',
        'as' => 'branch-dashboard.inventory.reports.variance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/inventory/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.suppliers.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/suppliers',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:view-suppliers|manage-suppliers',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierIndex@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierIndex',
        'as' => 'branch-dashboard.suppliers.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/suppliers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.suppliers.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/suppliers/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:view-suppliers|manage-suppliers',
          7 => 'role_or_permission:create-suppliers|manage-suppliers',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Supplier\\CreateSupplier@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Supplier\\CreateSupplier',
        'as' => 'branch-dashboard.suppliers.create',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/suppliers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.suppliers.show' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/suppliers/{supplier}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:view-suppliers|manage-suppliers',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierDetails@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierDetails',
        'as' => 'branch-dashboard.suppliers.show',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/suppliers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.suppliers.performance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/suppliers/{supplier}/performance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:view-suppliers|manage-suppliers',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierPerformance@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Supplier\\SupplierPerformance',
        'as' => 'branch-dashboard.suppliers.performance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/suppliers',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.product-types' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/product-types/{deptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\ProductTypes@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\ProductTypes',
        'as' => 'branch-dashboard.production.product-types',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.products' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/products/{deptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Products@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Products',
        'as' => 'branch-dashboard.production.products',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.request.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/request/{deptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Request\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Request\\Index',
        'as' => 'branch-dashboard.production.request.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/request',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.request.create' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/request/{deptSlug}/create',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Request\\Create@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Request\\Create',
        'as' => 'branch-dashboard.production.request.create',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/request',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.daily-produce.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/daily-produce/{deptSlug}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\DailyProduce\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\DailyProduce\\Index',
        'as' => 'branch-dashboard.production.daily-produce.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/daily-produce',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.shift-closing.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/shift-closing/{deptSlug}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\ShiftClosing\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\ShiftClosing\\Index',
        'as' => 'branch-dashboard.production.shift-closing.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/shift-closing',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.recipes.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/recipes/{deptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes',
        'as' => 'branch-dashboard.production.recipes.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.recipes.add' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/recipes/{deptSlug}/add',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes\\Add@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes\\Add',
        'as' => 'branch-dashboard.production.recipes.add',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.recipes.edit' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/recipes/{deptSlug}/{id}/edit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes\\Edit@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Recipes\\Edit',
        'as' => 'branch-dashboard.production.recipes.edit',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.recipes.detail' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/recipes/{deptSlug}/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\RecipeDetail@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\RecipeDetail',
        'as' => 'branch-dashboard.production.recipes.detail',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.module.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/module',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\KitchenModule\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\KitchenModule\\Index',
        'as' => 'branch-dashboard.production.module.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/module',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.raw-material-tracking' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/raw-material-tracking',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\RawMaterialTracking@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\RawMaterialTracking',
        'as' => 'branch-dashboard.production.raw-material-tracking',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.callbacks.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/callbacks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\Index',
        'as' => 'branch-dashboard.production.callbacks.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.callbacks.create-inventory' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/callbacks/create-inventory',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\CreateInventoryCallback@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\CreateInventoryCallback',
        'as' => 'branch-dashboard.production.callbacks.create-inventory',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.callbacks.approve-sales-callbacks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/callbacks/approve-sales-callbacks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\ApproveCallbacks@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Callbacks\\ApproveCallbacks',
        'as' => 'branch-dashboard.production.callbacks.approve-sales-callbacks',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.operations' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/operations',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\OperationsReports@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\OperationsReports',
        'as' => 'branch-dashboard.production.reports.operations',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.performance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/performance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PerformanceReports@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PerformanceReports',
        'as' => 'branch-dashboard.production.reports.performance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.planning' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/planning',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PlanningReports@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PlanningReports',
        'as' => 'branch-dashboard.production.reports.planning',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.efficiency' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/efficiency',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\ProductionEfficiency\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\ProductionEfficiency\\Index',
        'as' => 'branch-dashboard.production.reports.efficiency',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.quality' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/quality',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\QualityMetrics\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\QualityMetrics\\Index',
        'as' => 'branch-dashboard.production.reports.quality',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.waste' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/waste',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\WasteAnalysis\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\WasteAnalysis\\Index',
        'as' => 'branch-dashboard.production.reports.waste',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.cost' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/cost',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\CostAnalysis\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\CostAnalysis\\Index',
        'as' => 'branch-dashboard.production.reports.cost',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.recipe-performance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/recipe-performance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\RecipePerformance\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\RecipePerformance\\Index',
        'as' => 'branch-dashboard.production.reports.recipe-performance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.shift-summary' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/shift-summary',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\ShiftSummary\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\ShiftSummary\\Index',
        'as' => 'branch-dashboard.production.reports.shift-summary',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.ingredient-utilization' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/ingredient-utilization',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\IngredientUtilization\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\IngredientUtilization\\Index',
        'as' => 'branch-dashboard.production.reports.ingredient-utilization',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.pipeline' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/pipeline',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PipelineStatus\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\PipelineStatus\\Index',
        'as' => 'branch-dashboard.production.reports.pipeline',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.production.reports.capacity' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/production/reports/capacity',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\CapacityPlanning\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Production\\Reports\\CapacityPlanning\\Index',
        'as' => 'branch-dashboard.production.reports.capacity',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/production/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.overview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/overview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\OverallSummaryDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\OverallSummaryDashboard',
        'as' => 'branch-dashboard.analytics.overview',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.stock-level' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/stock-level',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockLevelAnalytics@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockLevelAnalytics',
        'as' => 'branch-dashboard.analytics.stock-level',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.stock-movement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/stock-movement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockMovementAnalytics@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockMovementAnalytics',
        'as' => 'branch-dashboard.analytics.stock-movement',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.purchase' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/purchase',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\PurchaseAnalytics@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\PurchaseAnalytics',
        'as' => 'branch-dashboard.analytics.purchase',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.request-dispatch' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/request-dispatch',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\RequestDispatchAnalytics@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\RequestDispatchAnalytics',
        'as' => 'branch-dashboard.analytics.request-dispatch',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.alerts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/alerts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\AlertsDashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\AlertsDashboard',
        'as' => 'branch-dashboard.analytics.alerts',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.analytics.stock-valuation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/analytics/stock-valuation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockValuation@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Analytics\\StockValuation',
        'as' => 'branch-dashboard.analytics.stock-valuation',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.exports.stock-level-analytics' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/exports/stock-level-analytics',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Http\\Controllers\\ExportController@stockLevelAnalytics',
        'controller' => 'App\\Http\\Controllers\\ExportController@stockLevelAnalytics',
        'as' => 'branch-dashboard.exports.stock-level-analytics',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/exports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.exports.health-checks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/exports/health-checks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Http\\Controllers\\ExportController@healthChecks',
        'controller' => 'App\\Http\\Controllers\\ExportController@healthChecks',
        'as' => 'branch-dashboard.exports.health-checks',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/exports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.exports.item-requests' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/exports/item-requests',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Http\\Controllers\\ExportController@itemRequests',
        'controller' => 'App\\Http\\Controllers\\ExportController@itemRequests',
        'as' => 'branch-dashboard.exports.item-requests',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/exports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.reporting.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/reporting/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\Dashboard\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\Dashboard\\Index',
        'as' => 'branch-dashboard.reporting.dashboard',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/reporting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.reporting.review' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/reporting/review',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\ReviewReports\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\ReviewReports\\Index',
        'as' => 'branch-dashboard.reporting.review',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/reporting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.reporting.compile' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/reporting/compile',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\CompileReports\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\CompileReports\\Index',
        'as' => 'branch-dashboard.reporting.compile',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/reporting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.reporting.compiled.view' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/reporting/compiled/{id}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\ViewCompiled\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\ViewCompiled\\Index',
        'as' => 'branch-dashboard.reporting.compiled.view',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/reporting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.reporting.send-to-md' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/reporting/send-to-md',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\SendToMD\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\ReportingDepartment\\SendToMD\\Index',
        'as' => 'branch-dashboard.reporting.send-to-md',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/reporting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.audit.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/audit',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\AuditManagement\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\AuditManagement\\Index',
        'as' => 'branch-dashboard.audit.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.audit.inventory-approvals' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/audit/inventory-approvals',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\AuditManagement\\InventoryApprovals@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\AuditManagement\\InventoryApprovals',
        'as' => 'branch-dashboard.audit.inventory-approvals',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/audit',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.dashboard' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/dashboard',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Dashboard@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Dashboard',
        'as' => 'branch-dashboard.accounting.dashboard',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.overview' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/overview',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Overview@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Overview',
        'as' => 'branch-dashboard.accounting.overview',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.accounts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/accounts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
          7 => 'role_or_permission:manage_accounts',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\GlAccountList@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\GlAccountList',
        'as' => 'branch-dashboard.accounting.accounts',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.periods' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/periods',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
          7 => 'role_or_permission:manage_periods',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\PeriodManagement@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\PeriodManagement',
        'as' => 'branch-dashboard.accounting.periods',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.journal-entry' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/journal-entry',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
          7 => 'role_or_permission:create_journal_entries',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\ManualJournalEntry@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\ManualJournalEntry',
        'as' => 'branch-dashboard.accounting.journal-entry',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.posting-status' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/posting-status',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\PostingStatusMonitor@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\PostingStatusMonitor',
        'as' => 'branch-dashboard.accounting.posting-status',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.inventory-valuation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/inventory-valuation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\InventoryValuationPosting@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\InventoryValuationPosting',
        'as' => 'branch-dashboard.accounting.inventory-valuation',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.bank-reconciliation' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/bank-reconciliation',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
          7 => 'role_or_permission:reconcile_bank_accounts',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\BankReconciliation@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\BankReconciliation',
        'as' => 'branch-dashboard.accounting.bank-reconciliation',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\Index',
        'as' => 'branch-dashboard.accounting.reports.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.general-ledger' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports/general-ledger',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\GeneralLedgerReport@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\GeneralLedgerReport',
        'as' => 'branch-dashboard.accounting.reports.general-ledger',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.trial-balance' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports/trial-balance',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\TrialBalanceReport@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\TrialBalanceReport',
        'as' => 'branch-dashboard.accounting.reports.trial-balance',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.income-statement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports/income-statement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\IncomeStatementReport@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\IncomeStatementReport',
        'as' => 'branch-dashboard.accounting.reports.income-statement',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.balance-sheet' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports/balance-sheet',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\BalanceSheetReport@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\BalanceSheetReport',
        'as' => 'branch-dashboard.accounting.reports.balance-sheet',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.accounting.reports.cash-flow-statement' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/accounting/reports/cash-flow-statement',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'role_or_permission:access_accounting,view_financial_reports',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\CashFlowStatementReport@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\Accounting\\Report\\CashFlowStatementReport',
        'as' => 'branch-dashboard.accounting.reports.cash-flow-statement',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/accounting/reports',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.expiry-alerts' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/expiry-alerts',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\ExpiryAlerts@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\ExpiryAlerts',
        'as' => 'branch-dashboard.sales-dashboard.expiry-alerts',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.stock-opening.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/stock-opening/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
          7 => 'validate-sales-workflow',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\StockOpening\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\StockOpening\\Index',
        'as' => 'branch-dashboard.sales-dashboard.stock-opening.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/stock-opening',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.dispatches.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/dispatches/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Dispatches\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Dispatches\\Index',
        'as' => 'branch-dashboard.sales-dashboard.dispatches.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/dispatches',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.callbacks.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/callbacks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Callbacks\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Callbacks\\Index',
        'as' => 'branch-dashboard.sales-dashboard.callbacks.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.callbacks.dispatch-callbacks' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/callbacks/dispatch-callbacks',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Callbacks\\CreateDispatchCallback@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Callbacks\\CreateDispatchCallback',
        'as' => 'branch-dashboard.sales-dashboard.callbacks.dispatch-callbacks',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/callbacks',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.stock-monitor' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/stock-monitor',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\StockMonitor@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\StockMonitor',
        'as' => 'branch-dashboard.sales-dashboard.stock-monitor',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.pos.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/pos/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
          7 => 'validate-sales-workflow',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Pos\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Pos\\Index',
        'as' => 'branch-dashboard.sales-dashboard.pos.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/pos',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.analytics.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/analytics/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Analytics\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\Analytics\\Index',
        'as' => 'branch-dashboard.sales-dashboard.analytics.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/analytics',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.my-sales.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/my-sales/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\MySales\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\MySales\\Index',
        'as' => 'branch-dashboard.sales-dashboard.my-sales.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/my-sales',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'branch-dashboard.sales-dashboard.shift-closing.index' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'branch-dashboard/sales-dashboard/shift-closing/{salesDeptSlug?}',
      'action' => 
      array (
        'middleware' => 
        array (
          0 => 'web',
          1 => 'auth',
          2 => 'setBranchContext',
          3 => 'branch',
          4 => 'redirect-super-admin',
          5 => 'require_active_shift',
          6 => 'validate-sales-department-context',
          7 => 'validate-sales-workflow',
        ),
        'uses' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\ShiftClosing\\Index@__invoke',
        'controller' => 'App\\Livewire\\BranchDashboard\\SalesDashboard\\ShiftClosing\\Index',
        'as' => 'branch-dashboard.sales-dashboard.shift-closing.index',
        'namespace' => NULL,
        'prefix' => 'branch-dashboard/sales-dashboard/shift-closing',
        'where' => 
        array (
        ),
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
    'storage.local' => 
    array (
      'methods' => 
      array (
        0 => 'GET',
        1 => 'HEAD',
      ),
      'uri' => 'storage/{path}',
      'action' => 
      array (
        'uses' => 'O:55:"Laravel\\SerializableClosure\\UnsignedSerializableClosure":1:{s:12:"serializable";O:46:"Laravel\\SerializableClosure\\Serializers\\Native":5:{s:3:"use";a:3:{s:4:"disk";s:5:"local";s:6:"config";a:5:{s:6:"driver";s:5:"local";s:4:"root";s:51:"/home/ilem/Documents/sweettooth/storage/app/private";s:5:"serve";b:1;s:5:"throw";b:0;s:6:"report";b:0;}s:12:"isProduction";b:0;}s:8:"function";s:323:"function (\\Illuminate\\Http\\Request $request, string $path) use ($disk, $config, $isProduction) {
                    return (new \\Illuminate\\Filesystem\\ServeFile(
                        $disk,
                        $config,
                        $isProduction
                    ))($request, $path);
                }";s:5:"scope";s:47:"Illuminate\\Filesystem\\FilesystemServiceProvider";s:4:"this";N;s:4:"self";s:32:"0000000000000d890000000000000000";}}',
        'as' => 'storage.local',
      ),
      'fallback' => false,
      'defaults' => 
      array (
      ),
      'wheres' => 
      array (
        'path' => '.*',
      ),
      'bindingFields' => 
      array (
      ),
      'lockSeconds' => NULL,
      'waitSeconds' => NULL,
      'withTrashed' => false,
    ),
  ),
)
);
