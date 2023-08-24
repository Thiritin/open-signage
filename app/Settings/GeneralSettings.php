<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $name;

    public string $starts_at;

    public string $ends_at;

    public string $playlist_id;

    /**
     * Thinclient Default Configuration
     */
    public string $connection;

    public bool $dhcp;

    public ?string $network_interface;

    public ?string $default_gateway;

    public ?string $netmask;

    public ?string $dns_server;

    public ?string $ssid_name;

    public ?string $hidden_ssid_name;

    public ?string $wifi_encryption;

    public ?string $wep_key;

    public ?string $wpa_password;

    public ?string $wired_authentication;

    public ?string $eapol_username;

    public ?string $eapol_password;

    public ?string $proxy_config;

    public ?string $proxy;

    public ?string $proxy_exceptions;

    public bool $wake_on_lan;

    public ?string $hostname_aliases;

    public bool $disable_firewall;

    public bool $allow_icmp_protocol;

    public string $browser;

    public ?string $homepage_append;

    public ?string $homepage_check;

    public array|string|null $import_certificates;

    public ?array $whitelist;

    public ?array $blacklist;

    public ?string $browser_user_agent;

    public bool $disable_private_mode;

    public bool $allow_popup_windows;

    public bool $disable_zoom_controls;

    public string $browser_zoom_level;

    public bool $enable_file_protocol;

    public bool $disable_navigation_bar;

    public bool $disable_address_bar;

    public bool $autohide_navigation_bar;

    public ?array $onscreen_buttons;

    public ?string $onscreen_buttons_position;

    public ?int $refresh_webpage;

    public bool $virtual_keyboard;

    public ?string $wallpaper;

    public ?string $persistence;

    public ?int $swapfile;

    public bool $removable_devices;

    public string $timezone;

    public ?string $ntp_server;

    public ?string $rtc_wake;

    public ?string $scheduled_action;

    public bool $disable_input_devices;

    public ?string $screen_settings;

    public string $screen_rotate;

    public string $volume_level;

    public bool $shutdown_menu;

    public bool $hardware_video_decode;

    public bool $debug;

    public ?string $default_sound_card;

    public static function group(): string
    {
        return 'general';
    }
}
