<?php

namespace App\Livewire\BranchDashboard\Settings;

use App\Models\GlobalBusinessConfiguration;
use Livewire\Component;
use Livewire\WithFileUploads;

class Appearance extends Component
{
    use WithFileUploads;

    public $themeMode;
    public $primaryColor;
    public $sidebarPosition;
    public $uiDensity;
    public $fontSize;
    public $accentColor;
    public $animationEnabled;
    
    // Dark mode logos
    public $lightModeLogo;
    public $darkModeLogo;
    public $existingLightModeLogo;
    public $existingDarkModeLogo;

    protected $rules = [
        'themeMode' => 'required|string|in:system,light,dark',
        'primaryColor' => 'string|max:20',
        'sidebarPosition' => 'string|in:left,right',
        'uiDensity' => 'string|in:compact,normal,spacious',
        'fontSize' => 'string|in:small,normal,large',
        'accentColor' => 'string|max:20',
        'animationEnabled' => 'boolean',
        'lightModeLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'darkModeLogo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];

    public function mount()
    {
        // If user is super admin, load global settings
        if (is_super_admin()) {
            $settings = GlobalBusinessConfiguration::first();

            if ($settings) {
                $appearanceSettings = $settings->appearance_settings ?? [];
                
                $this->themeMode = $appearanceSettings['theme_mode'] ?? 'system';
                $this->primaryColor = $appearanceSettings['primary_color'] ?? '#10b981';
                $this->sidebarPosition = $appearanceSettings['sidebar_position'] ?? 'left';
                $this->uiDensity = $appearanceSettings['ui_density'] ?? 'normal';
                $this->fontSize = $appearanceSettings['font_size'] ?? 'normal';
                $this->accentColor = $appearanceSettings['accent_color'] ?? '#3b82f6';
                $this->animationEnabled = $appearanceSettings['animation_enabled'] ?? true;
                
                $this->existingLightModeLogo = $appearanceSettings['light_mode_logo'] ?? null;
                $this->existingDarkModeLogo = $appearanceSettings['dark_mode_logo'] ?? null;
            }
        } else {
            // For non-super admins, we could potentially load branch-specific settings
            // But currently, appearance settings are only global, so we'll keep the same logic
            $settings = GlobalBusinessConfiguration::first();

            if ($settings) {
                $appearanceSettings = $settings->appearance_settings ?? [];
                
                $this->themeMode = $appearanceSettings['theme_mode'] ?? 'system';
                $this->primaryColor = $appearanceSettings['primary_color'] ?? '#10b981';
                $this->sidebarPosition = $appearanceSettings['sidebar_position'] ?? 'left';
                $this->uiDensity = $appearanceSettings['ui_density'] ?? 'normal';
                $this->fontSize = $appearanceSettings['font_size'] ?? 'normal';
                $this->accentColor = $appearanceSettings['accent_color'] ?? '#3b82f6';
                $this->animationEnabled = $appearanceSettings['animation_enabled'] ?? true;
                
                $this->existingLightModeLogo = $appearanceSettings['light_mode_logo'] ?? null;
                $this->existingDarkModeLogo = $appearanceSettings['dark_mode_logo'] ?? null;
            }
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // If user is super admin, save to global settings
            if (is_super_admin()) {
                $settings = GlobalBusinessConfiguration::first();

                if (!$settings) {
                    $settings = new GlobalBusinessConfiguration();
                }

                // Prepare appearance settings
                $appearanceSettings = [
                    'theme_mode' => $this->themeMode,
                    'primary_color' => $this->primaryColor,
                    'sidebar_position' => $this->sidebarPosition,
                    'ui_density' => $this->uiDensity,
                    'font_size' => $this->fontSize,
                    'accent_color' => $this->accentColor,
                    'animation_enabled' => $this->animationEnabled,
                ];

                // Handle light mode logo upload
                if ($this->lightModeLogo) {
                    // Delete old logo if exists
                    if ($settings->appearance_settings['light_mode_logo'] ?? null) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($settings->appearance_settings['light_mode_logo']);
                    }

                    $path = $this->lightModeLogo->store('logos', 'public');
                    $appearanceSettings['light_mode_logo'] = $path;
                    $this->existingLightModeLogo = $path;
                } else {
                    $appearanceSettings['light_mode_logo'] = $this->existingLightModeLogo;
                }

                // Handle dark mode logo upload
                if ($this->darkModeLogo) {
                    // Delete old logo if exists
                    if ($settings->appearance_settings['dark_mode_logo'] ?? null) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($settings->appearance_settings['dark_mode_logo']);
                    }

                    $path = $this->darkModeLogo->store('logos', 'public');
                    $appearanceSettings['dark_mode_logo'] = $path;
                    $this->existingDarkModeLogo = $path;
                } else {
                    $appearanceSettings['dark_mode_logo'] = $this->existingDarkModeLogo;
                }

                $settings->appearance_settings = $appearanceSettings;
                $settings->save();

                // Clear settings cache to ensure changes take effect immediately
                \App\Helpers\Settings::clearCache();

                // Reset the file inputs
                $this->lightModeLogo = null;
                $this->darkModeLogo = null;

                session()->flash('message', 'Global appearance settings saved successfully! These settings will apply to all branches.');
            } else {
                // For non-super admins, we could potentially save to branch-specific settings
                // But currently, appearance settings are only global, so we'll keep the same logic
                $settings = GlobalBusinessConfiguration::first();

                if (!$settings) {
                    $settings = new GlobalBusinessConfiguration();
                }

                // Prepare appearance settings
                $appearanceSettings = [
                    'theme_mode' => $this->themeMode,
                    'primary_color' => $this->primaryColor,
                    'sidebar_position' => $this->sidebarPosition,
                    'ui_density' => $this->uiDensity,
                    'font_size' => $this->fontSize,
                    'accent_color' => $this->accentColor,
                    'animation_enabled' => $this->animationEnabled,
                ];

                // Handle light mode logo upload
                if ($this->lightModeLogo) {
                    // Delete old logo if exists
                    if ($settings->appearance_settings['light_mode_logo'] ?? null) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($settings->appearance_settings['light_mode_logo']);
                    }

                    $path = $this->lightModeLogo->store('logos', 'public');
                    $appearanceSettings['light_mode_logo'] = $path;
                    $this->existingLightModeLogo = $path;
                } else {
                    $appearanceSettings['light_mode_logo'] = $this->existingLightModeLogo;
                }

                // Handle dark mode logo upload
                if ($this->darkModeLogo) {
                    // Delete old logo if exists
                    if ($settings->appearance_settings['dark_mode_logo'] ?? null) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($settings->appearance_settings['dark_mode_logo']);
                    }

                    $path = $this->darkModeLogo->store('logos', 'public');
                    $appearanceSettings['dark_mode_logo'] = $path;
                    $this->existingDarkModeLogo = $path;
                } else {
                    $appearanceSettings['dark_mode_logo'] = $this->existingDarkModeLogo;
                }

                $settings->appearance_settings = $appearanceSettings;
                $settings->save();

                // Reset the file inputs
                $this->lightModeLogo = null;
                $this->darkModeLogo = null;

                session()->flash('message', 'Appearance settings saved successfully!');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to save appearance settings: ' . $e->getMessage());
            session()->flash('error', 'Failed to save appearance settings. Please try again.');
        }
    }

    public function removeLightModeLogo()
    {
        try {
            if ($this->existingLightModeLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->existingLightModeLogo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->existingLightModeLogo);
            }

            // If user is super admin, remove from global settings
            if (is_super_admin()) {
                $settings = GlobalBusinessConfiguration::first();

                if ($settings && isset($settings->appearance_settings['light_mode_logo'])) {
                    $settings->appearance_settings['light_mode_logo'] = null;
                    $settings->save();
                    
                    // Clear settings cache
                    \App\Helpers\Settings::clearCache();
                }
            } else {
                // For non-super admins, remove from global settings (since appearance settings are global)
                $settings = GlobalBusinessConfiguration::first();

                if ($settings && isset($settings->appearance_settings['light_mode_logo'])) {
                    $settings->appearance_settings['light_mode_logo'] = null;
                    $settings->save();
                }
            }

            $this->existingLightModeLogo = null;
            session()->flash('message', 'Light mode logo removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to remove light mode logo: ' . $e->getMessage());
            session()->flash('error', 'Failed to remove light mode logo. Please try again.');
        }
    }

    public function removeDarkModeLogo()
    {
        try {
            if ($this->existingDarkModeLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->existingDarkModeLogo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->existingDarkModeLogo);
            }

            // If user is super admin, remove from global settings
            if (is_super_admin()) {
                $settings = GlobalBusinessConfiguration::first();

                if ($settings && isset($settings->appearance_settings['dark_mode_logo'])) {
                    $settings->appearance_settings['dark_mode_logo'] = null;
                    $settings->save();
                    
                    // Clear settings cache
                    \App\Helpers\Settings::clearCache();
                }
            } else {
                // For non-super admins, remove from global settings (since appearance settings are global)
                $settings = GlobalBusinessConfiguration::first();

                if ($settings && isset($settings->appearance_settings['dark_mode_logo'])) {
                    $settings->appearance_settings['dark_mode_logo'] = null;
                    $settings->save();
                }
            }

            $this->existingDarkModeLogo = null;
            session()->flash('message', 'Dark mode logo removed successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to remove dark mode logo: ' . $e->getMessage());
            session()->flash('error', 'Failed to remove dark mode logo. Please try again.');
        }
    }

    public function cancel()
    {
        $this->mount(); // Reload original values
        session()->flash('message', 'Changes cancelled. Values reverted to last saved state.');
    }

    public function render()
    {
        return view('livewire.branch-dashboard.settings.appearance');
    }
}