<?php

namespace App\Services;

use App\Settings\GeneralSettings;
use DateTimeZone;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;

class ScreenTabResource
{
    public static function getForm(string $prefix = '')
    {
        $settings = app(GeneralSettings::class);

        return Tabs::make('Settings')->schema([
            Tab::make('Network')->columns()->schema([
                Select::make($prefix . 'connection')->required()
                    ->label('Connection Type')
                    ->options([
                        'wired' => 'Wired',
                        'wifi' => 'WiFi',
                    ])
                    ->default($settings->connection)
                    ->columnSpanFull()
                    ->reactive(),

                Section::make('Config')->columnSpan(1)->compact()->schema(components: [
                    Checkbox::make($prefix . 'dhcp')->required()->label('DHCP')->reactive()->default($settings->dhcp),
                    TextInput::make($prefix . 'network_interface')->default($settings->network_interface)->label('Network Interface'),
                    TextInput::make($prefix . 'default_gateway')->default($settings->default_gateway)
                        ->hidden(fn (Get $get): bool => $get($prefix . 'dhcp') ?? true)
                        ->label('Default Gateway')->columnSpanFull(),
                    TextInput::make($prefix . 'netmask')->default($settings->netmask)->hidden(fn (
                        Get $get
                    ): bool => $get($prefix . 'dhcp') ?? true)->label('Netmask')->columnSpanFull(),
                    TextInput::make($prefix . 'dns_server')->default($settings->dns_server)->label('DNS Server'),
                ]),

                Section::make('WiFi')->columnSpan(1)->compact()->schema([
                    TextInput::make($prefix . 'ssid_name')->default($settings->ssid_name)->label('SSID Name')->columnSpanFull(),
                    TextInput::make($prefix . 'hidden_ssid_name')->default($settings->hidden_ssid_name)->label('Hidden SSID Name')->columnSpanFull(),
                    Select::make($prefix . 'wifi_encryption')->default($settings->wifi_encryption)->required()->label('WiFi Encryption')->options([
                        'none' => 'None',
                        'wep' => 'WEP',
                        'wpa' => 'WPA',
                        'peap' => 'PEAP',
                    ])->default('none')->columnSpanFull(),
                    TextInput::make($prefix . 'wep_key')->default($settings->wep_key)->label('WEP Key')->columnSpanFull(),
                    TextInput::make($prefix . 'wpa_password')->default($settings->wpa_password)->label('WPA Password')->columnSpanFull(),
                ])->hidden(fn (Get $get): bool => $get($prefix . 'connection') !== 'wifi'),

                Section::make('EAPOL')->compact()->columnSpan(1)->schema([
                    Select::make($prefix . 'wired_authentication')->default($settings->wired_authentication)->hidden()->required()->reactive()->label('Wired Authentication')->options([
                        'none' => 'None',
                        'eapol' => 'EAPOL',
                    ])->default('none')->hidden(fn (Get $get): bool => $get($prefix . 'connection') !== 'wired'),
                    TextInput::make($prefix . 'eapol_username')->default($settings->eapol_username)->label('EAPOL Username')->hidden(fn (
                        Get $get
                    ) => $get($prefix . 'wired_authentication') !== 'eapol')->columnSpanFull(),
                    TextInput::make($prefix . 'eapol_password')->default($settings->eapol_password)->label('EAPOL Password')->hidden(fn (
                        Get $get
                    ) => $get($prefix . 'wired_authentication') !== 'eapol')->columnSpanFull(),
                ])->hidden(fn (Get $get): bool => $get($prefix . 'connection') !== 'wired'),
                Section::make('Proxy')->columnSpan(1)->schema([
                    TextInput::make($prefix . 'proxy_config')->default($settings->proxy_config)->placeholder('proxy_config=http://192.168.1.10/files/proxy.pac')->label('Proxy Config')->columnSpanFull(),
                    TextInput::make($prefix . 'proxy')->default($settings->proxy)->placeholder('proxy=192.168.1.20:3128')->label('Proxy')->columnSpanFull(),
                    TextInput::make($prefix . 'proxy_exceptions')->default($settings->proxy_exceptions)->placeholder('proxy_exceptions=192.168.1.10 domain.local kernel.org')->label('Proxy Exceptions')->columnSpanFull(),
                ]),
                Section::make('Other')->columnSpan(1)->schema([
                    Checkbox::make($prefix . 'wake_on_lan')->default($settings->wake_on_lan)->required()->label('Wake on LAN')->columnSpanFull(),
                    TextInput::make($prefix . 'hostname_aliases')->default($settings->hostname_aliases)->label('Hostname Aliases')->columnSpanFull(),
                ]),
            ]),
            Tab::make('Security')->columns()->schema([
                Section::make('Firewall')->columns()->schema([
                    Checkbox::make($prefix . 'disable_firewall')->default($settings->disable_firewall)->required()->reactive()->label('Disable Firewall'),
                    Checkbox::make($prefix . 'allow_icmp_protocol')->default($settings->allow_icmp_protocol)->required()
                        ->hidden(fn (Get $get): bool => $get($prefix . 'disable_firewall') ?? false)
                        ->label('Allow ICMP Protocol'),
                ])->compact(),
            ]),
            Tab::make('Browser Settings')->columns()->schema([
                Select::make($prefix . 'browser')->default($settings->browser)->reactive()->required()->label('Browser')->options([
                    'chrome' => 'Chrome',
                    'firefox' => 'Firefox',
                ])->columnSpanFull(),
                Select::make($prefix . 'homepage_append')->default($settings->homepage_append)->label('Homepage Append')->helperText('Cannot be changed.')->disabled()->options([
                    'hostname' => 'Hostname',
                    'mac' => 'MAC Address',
                ]),
                TextInput::make($prefix . 'homepage_check')->default($settings->homepage_check)->label('Homepage Check Error Text')->helperText('System notification is displayed every 10 seconds for the case when homepage is not available.'),
                TagsInput::make($prefix . 'import_certificates')->default($settings->import_certificates)->placeholder('http://domain.com/files/certificate1.crt')->label('Certificates to Import'),
                TagsInput::make($prefix . 'whitelist')->default($settings->whitelist)->helperText('URLs and IPs which you want to allow in the browser. Everything else will be blocked.')->label('Whitelist'),
                TagsInput::make($prefix . 'blacklist')->default($settings->blacklist)->helperText('URLs and IPs which you want to block in the browser. Everything else will be allowed.')->label('Blacklist'),
                TextInput::make($prefix . 'browser_user_agent')->default($settings->browser_user_agent)->label('Browser User Agent')->helperText('Custom user agent string to be used by the browser.')->hint('Leave empty to apply default.'),
                Checkbox::make($prefix . 'disable_private_mode')->default($settings->disable_private_mode)->label('Disable Private Mode')->helperText('Disable private mode to let the browser remember form/search history, cookies and caches during the session. Signons (login/password) are not remembered unless enabled through a separate parameter below. A browser restart still returns to factory defaults. Use with caution as caches are saved in RAM (unless \'persistence=full\' paraemter is used) and may slow down the kiosk when the PC has low memory.')->columnSpanFull(),
                Checkbox::make($prefix . 'allow_popup_windows')->default($settings->allow_popup_windows)->helperText('Allow popup windows which are opened as a new tabs in Firefox and directly on the screen in Google Chrome.')->label('Allow Popup Windows')->columnSpanFull(),
                Checkbox::make($prefix . 'disable_zoom_controls')->default($settings->disable_zoom_controls)->helperText('Remove zoom controls from the Firefox UI, deactivate \'pinch to zoom\' touch gesture and block Ctrl++/Ctrl+-/Ctrl+0 keyboard shortcuts. This is to prevent the users from changing zoom level in the browser. If Chrome has navigation bar enabled then its still possible to zoom in and out through the browser menu.')->label('Disable Zoom Controls'),
                Select::make($prefix . 'browser_zoom_level')->default($settings->browser_zoom_level)->label('Browser Zoom Level')->options([
                    '0.25' => '25%',
                    '0.5' => '50%',
                    '0.75' => '75%',
                    '1.0' => '100%',
                    '1.25' => '125%',
                    '1.5' => '150%',
                    '1.75' => '175%',
                    '2' => '200%',
                ]),
                Checkbox::make($prefix . 'enable_file_protocol')->default($settings->enable_file_protocol)->label('Enable File Protocol')->helperText('Enable access to local files and folders through the file:// protocol.'),

                Checkbox::make($prefix . 'disable_navigation_bar')->default($settings->disable_navigation_bar)
                    ->label('Disable Navigation Bar')
                    ->reactive()
                    ->helperText('Disable the navigation bar to convert the kiosk into digital signage station displaying a picture, video or a single webpage.'),

                Checkbox::make($prefix . 'disable_address_bar')->default($settings->disable_address_bar)->label('Disable Address Bar')
                    ->reactive()
                    ->hidden(fn (Get $get) => $get($prefix . 'disable_navigation_bar') === true)
                    ->helperText('Disable address bar in the browser.'),
                Checkbox::make($prefix . 'autohide_navigation_bar')->default($settings->autohide_navigation_bar)
                    ->hidden(fn (Get $get) => $get($prefix . 'disable_navigation_bar') === true)
                    ->label('Autohide Navigation Bar')->helperText('Hide the Firefox navigation bar when not in use. When bumping the mouse into the top of the window the navigation reappears again.'),
                Section::make('Onscreen Buttons')->schema([
                    Select::make($prefix . 'onscreen_buttons')->default($settings->onscreen_buttons)
                        ->multiple()->options([
                            'back' => 'Back',
                            'forward' => 'Forward',
                            'refresh' => 'Refresh',
                            'home' => 'Home',
                            'zoom-in' => 'Zoom In',
                            'zoom-out' => 'Zoom Out',
                            'print' => 'Print',
                            'close' => 'Close',
                        ]),

                    Select::make($prefix . 'onscreen_buttons_position')->default($settings->onscreen_buttons_position)->label('Onscreen Buttons Position')->options([
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                        'left' => 'Left',
                        'right' => 'Right',
                    ]),
                ])->columns(2)
                    ->hidden(fn (
                        Get $get
                    ): bool => (! $get($prefix . 'disable_address_bar') && ! $get($prefix . 'disable_navigation_bar')) || $get($prefix . 'browser') !== 'firefox'),

                TextInput::make($prefix . 'refresh_webpage')->default($settings->refresh_webpage)->numeric()->suffix('seconds')->nullable()->label('Refresh Webpage')->hint('Leave empty to disable.')->helperText('This parameter tells the system to emulate "F5" key press every "X" seconds which breaks screensaver and session idle functions. This is useful for the cases when the system is configured to lock the screen after a certain period of inactivity.')->columnSpanFull(),
                Checkbox::make($prefix . 'virtual_keyboard')->default($settings->virtual_keyboard)->label('Virtual Keyboard')->helperText('Enable virtual keyboard extension for the Chrome and the Firefox browsers. Virtual keyboard will popup automatically when an input field is clicked on the webpage.')->columnSpanFull(),
            ]),
            Tab::make('System')->columns()->schema([
                FileUpload::make($prefix . 'wallpaper')->image()->columnSpanFull(),
                Select::make($prefix . 'persistence')->default($settings->persistence)->helperText('Custom persistence level for the guest\'s home folder. Set the parameter value to \'session\' in order to keep user data persistent when browser or session are restarted. Set it to \'full\' to keep user data persistent all the time - even when system is rebooted or powered down. Useful mostly with private mode disabled for the browser.')->label('Persistence')->options([
                    'none' => 'None',
                    'full' => 'Full',
                    'wipe' => 'Wipe',
                    'session' => 'Session',
                ])->columnSpanFull(),
                // Swapfile
                TextInput::make($prefix . 'swapfile')->default($settings->swapfile)->label('Swapfile')->helperText('Swapfile size in megabytes. Leave empty to disable.')->hint('Leave empty to disable.'),
                Checkbox::make($prefix . 'removable_devices')->default($settings->removable_devices)->label('Removable Devices')->helperText('Allow access to removable devices such as USB sticks, external hard drives, etc.'),
                Select::make($prefix . 'timezone')->default($settings->timezone)
                    ->options(array_combine(
                        DateTimeZone::listIdentifiers(),
                        DateTimeZone::listIdentifiers()
                    ))->label('Timezone'),
                TextInput::make($prefix . 'ntp_server')->default($settings->ntp_server)->label('NTP Server')->hint('Leave empty to use default.')->helperText('NTP server to use for time synchronization.'),
                TextInput::make($prefix . 'rtc_wake')->default($settings->rtc_wake)->label('RTC Wake')->hint('Leave empty to disable.')->helperText('Set the time when the system should wake up from RTC alarm.')->columnSpanFull(),
                TextInput::make($prefix . 'scheduled_action')->default($settings->scheduled_action)->label('Scheduled Action')->hint('Leave empty to disable.')->helperText('Set the time when the system should perform a scheduled action.')->columnSpanFull(),
                Checkbox::make($prefix . 'disable_input_devices')->default($settings->disable_input_devices)->label('Disable Input Devices')->helperText('Disable all input devices like mice, keyboards, touchpads, touchscreens, digital pens, etc so its not possible to type, touch, scroll, draw or click on any objects on the screen. This is useful when kiosk works in a digital signage mode.')->columnSpanFull(),
                TextInput::make($prefix . 'screen_settings')->default($settings->screen_settings)->label('Screen Settings')->hint('Leave empty to use default.')->helperText('Set custom screen resolution and orientation.')->columnSpanFull(),
                Select::make($prefix . 'screen_rotate')->default($settings->screen_rotate)->label('Screen Rotate')->options([
                    'normal' => 'Normal',
                    'left' => 'Left',
                    'right' => 'Right',
                    'inverted' => 'Inverted',
                ])->columnSpanFull(),
                TextInput::make($prefix . 'volume_level')->default($settings->volume_level)->label('Volume Level')->hint('Leave empty to use default.')->suffix('%')->minValue(0)->maxValue(100)->helperText('Set custom volume level.'),
                TextInput::make($prefix . 'default_sound_card')->default($settings->default_sound_card)->label('Default Sound Card')->placeholder('0.0'),
                Checkbox::make($prefix . 'shutdown_menu')->default($settings->shutdown_menu)->label('Shutdown Menu')->helperText('Enable shutdown menu.')->columnSpanFull(),
                Checkbox::make($prefix . 'hardware_video_decode')->default($settings->hardware_video_decode)->label('Hardware Video Decode')->helperText('Use GPU card for processing video data. Hardware video decode is faster and more efficient than software video decode. Video files must be encoded with codecs which are supported by ceratin GPU models. Check this link for reference: link. You can also run \'vainfo\' command over SSH on the target PC to find which video codecs are supported by your GPU. This function is especially useful for digital signage deployments.')->columnSpanFull(),
                Checkbox::make($prefix . 'debug')->default($settings->debug)->label('Debug')->helperText('Enable debug mode.')->columnSpanFull(),

            ]),

        ])->columnSpanFull();
    }
}
