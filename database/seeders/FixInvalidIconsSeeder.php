<?php

namespace Database\Seeders;

use App\Models\DepartmentPage;
use Illuminate\Database\Seeder;

class FixInvalidIconsSeeder extends Seeder
{
    /**
     * Fix department pages with invalid Flux icons.
     */
    public function run(): void
    {
        $this->command->info('Fixing department pages with invalid icons...');

        // Map invalid icons to valid Flux icons
        $iconMap = [
            'box' => 'archive-box',      // Use archive-box instead of box
            'dashboard' => 'chart-bar',  // Use chart-bar instead of dashboard
            'o-shopping-bag' => 'shopping-bag', // Use proper flux icon instead of Heroicons prefix
        ];

        $fixedCount = 0;

        foreach ($iconMap as $invalidIcon => $validIcon) {
            $pages = DepartmentPage::where('icon', $invalidIcon)->get();
            
            foreach ($pages as $page) {
                $page->update(['icon' => $validIcon]);
                $this->command->info("Fixed icon for page '{$page->name}': changed from '$invalidIcon' to '$validIcon'");
                $fixedCount++;
            }
        }

        // Also fix any other potentially invalid icons that might exist
        $allPages = DepartmentPage::all();
        $fluxIconsDir = base_path('vendor/livewire/flux/stubs/resources/views/flux/icon/');
        $customIconsDir = resource_path('views/flux/icon/');

        foreach ($allPages as $page) {
            $iconFile = $customIconsDir . $page->icon . '.blade.php';
            $vendorIconFile = $fluxIconsDir . $page->icon . '.blade.php';

            if (!file_exists($iconFile) && !file_exists($vendorIconFile)) {
                // If it's an unknown icon, set it to a default
                $page->update(['icon' => 'circle-stack']);
                $this->command->info("Fixed unknown icon for page '{$page->name}': changed to 'circle-stack'");
                $fixedCount++;
            }
        }

        $this->command->info("Fixed $fixedCount department pages with invalid icons.");
    }
}