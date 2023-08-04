<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->add('general.name', 'Open Signage Event');
        $this->migrator->add('general.starts_at', now());
        $this->migrator->add('general.ends_at', now());
        $this->migrator->add('general.playlist_id', 1);
        // Thinclient Default Configuration
        $thinClientConfig = [
            "connection" => "wired",
            "dhcp" => true,
            "network_interface" => "",
            "default_gateway" => "",
            "netmask" => "",
            "dns_server" => "1.1.1.1",
            "ssid_name" => "",
            "hidden_ssid_name" => "",
            "wifi_encryption" => "",
            "wep_key" => "",
            "wpa_password" => "",
            "wired_authentication" => "none",
            "eapol_username" => "",
            "eapol_password" => "",
            "proxy_config" => "",
            "proxy" => "",
            "proxy_exceptions" => "",
            "wake_on_lan" => true,
            "hostname_aliases" => "",
            "disable_firewall" => false,
            "allow_icmp_protocol" => false,
            "browser" => "chrome",
            "homepage_append" => "hostname",
            "homepage_check" => "Error while fetching signage url, retrying...",
            "import_certificates" => [],
            "whitelist" => [],
            "blacklist" => [],
            "browser_user_agent" => "",
            "disable_private_mode" => false,
            "allow_popup_windows" => false,
            "disable_zoom_controls" => true,
            "browser_zoom_level" => "1.0",
            "enable_file_protocol" => false,
            "disable_navigation_bar" => true,
            "disable_address_bar" => true,
            "autohide_navigation_bar" => true,
            "onscreen_buttons" => [],
            "onscreen_buttons_position" => null,
            "refresh_webpage" => null,
            "virtual_keyboard" => false,
            "wallpaper" => config('app.url')."/storage/splash.png",
            "persistence" => "none",
            "swapfile" => null,
            "removable_devices" => false,
            "timezone" => "Europe/Berlin",
            "ntp_server" => "pool.ntp.org",
            "rtc_wake" => "",
            "scheduled_action" => "",
            "disable_input_devices" => true,
            "screen_settings" => "",
            "screen_rotate" => "normal",
            "volume_level" => "0",
            "shutdown_menu" => false,
            "hardware_video_decode" => false,
        ];
        foreach ($thinClientConfig as $key => $value) {
            $this->migrator->add('general.'.$key, $value);
        }
    }

    public function down()
    {

    }
};
