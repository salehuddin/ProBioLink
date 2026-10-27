<?php

namespace App\Support;

class Templates
{
    /**
     * Visual templates apply independently of block content.
     */
    public const TEMPLATES = [
        'minimal' => 'Minimal',
        'professional' => 'Professional',
        'bold' => 'Bold',
    ];

    /** @var array<string, array<string, string>>|null */
    private static ?array $themes = null;

    public static function keys(): array
    {
        return array_keys(self::TEMPLATES);
    }

    public static function labels(): array
    {
        return self::TEMPLATES;
    }

    /**
     * Theme class maps, shared with the React renderer via resources/themes.json.
     *
     * @return array<string, string>
     */
    public static function theme(string $template): array
    {
        $themes = self::themes();

        return $themes[$template] ?? $themes['minimal'];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function themes(): array
    {
        if (self::$themes === null) {
            $path = resource_path('themes.json');
            $decoded = json_decode((string) file_get_contents($path), true);

            self::$themes = is_array($decoded) ? $decoded : [];
        }

        return self::$themes;
    }
}
