<?php

namespace App\Settings;

use Carbon\Carbon;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $name;
    public string $starts_at;
    public string $ends_at;

    public string $playlist_id;
    public int|null $project_id;
    /**
     * Thinclient Default Configuration
     */
    public string $connection;
    public bool $dhcp;
    public string|null $network_interface;
    public string|null $default_gateway;
    public string|null $netmask;
    public string|null $dns_server;
    public string|null $ssid_name;
    public string|null $hidden_ssid_name;
    public string|null $wifi_encryption;
    public string|null $wep_key;
    public string|null $wpa_password;
    public string|null $wired_authentication;
    public string|null $eapol_username;
    public string|null $eapol_password;
    public string|null $proxy_config;
    public string|null $proxy;
    public string|null $proxy_exceptions;
    public bool $wake_on_lan;
    public string|null $hostname_aliases;
    public bool $disable_firewall;
    public bool $allow_icmp_protocol;
    public string $browser;
    public string|null $homepage_append;
    public string|null $homepage_check;
    public array|string|null $import_certificates;
    public array|null $whitelist;
    public array|null $blacklist;
    public string|null $browser_user_agent;
    public bool $disable_private_mode;
    public bool $allow_popup_windows;
    public bool $disable_zoom_controls;
    public string $browser_zoom_level;
    public bool $enable_file_protocol;
    public bool $disable_navigation_bar;
    public bool $disable_address_bar;
    public bool $autohide_navigation_bar;
    public array|null $onscreen_buttons;
    public string|null $onscreen_buttons_position;
    public int|null $refresh_webpage;
    public bool $virtual_keyboard;
    public string|null $wallpaper;
    public string|null $persistence;
    public int|null $swapfile;
    public bool $removable_devices;
    public string $timezone;
    public string|null $ntp_server;
    public string|null $rtc_wake;
    public string|null $scheduled_action;
    public bool $disable_input_devices;
    public string|null $screen_settings;
    public string $screen_rotate;
    public string $volume_level;
    public bool $shutdown_menu;
    public bool $hardware_video_decode;
    public bool $debug;
    public string|null $default_sound_card;

    public static function group(): string
    {
        return 'general';
    }
}
