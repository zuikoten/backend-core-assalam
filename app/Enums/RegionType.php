<?php
// app/Enums/RegionType.php
namespace App\Enums;

enum RegionType: string
{
    case PROVINCE = 'province';
    case REGENCY = 'regency';
    case DISTRICT = 'district';

    public function label(): string
    {
        return match ($this) {
            self::PROVINCE => 'Provinsi',
            self::REGENCY => 'Kabupaten/Kota',
            self::DISTRICT => 'Kecamatan',
        };
    }
}
