<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;

class RequestPickupData extends ModelBase
{
    public string $address;         // string(max:200)	false	Alamat lengkap
    public string $phone;           // string(max:15)	false	Nomor telepon menggunakan format angka 0
    public string $name;            // string(max:50)	false	Nama pengirim paket
    public string $zipcode;         // string(max:5)	true	Kode pos pengirim
    public int $kecamatan_id;    // integer	false	Kecamatan id pengirim
    public array | RequestPickupDataList $packages;        // PackageData of array(min:1 object)	false	Lihat penyusunan list paket berikut
    public string $schedule;        // string	false	Lihat bagian #Pickup Schedules
    public ?string $platform_name = null;
    public ?float $latitude;       // float   Latitude dari pengirim, diperlukan ketka menggunakan ekspedisi Lion Parcel.
    public ?float $longitude;       // float   Latitude dari pengirim, diperlukan ketka menggunakan ekspedisi Lion Parcel.

    function __construct() {
        $this->packages = new RequestPickupDataList();
    }
}
