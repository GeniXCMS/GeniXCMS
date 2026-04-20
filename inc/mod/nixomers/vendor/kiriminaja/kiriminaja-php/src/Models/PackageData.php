<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;

class PackageData extends ModelBase
{
    public string $order_id;                                         //	string(max:20)	false	Order ID, harus memiliki prefix berupa string
    public string $destination_name;                                 //	string(max:50)	false	Nama penerima
    public string $destination_phone;                                //	string(max:15)	false	Nomor telepon diawali dengan angka 0
    public string $destination_address;                              //	string(max:200,min:10)	false	Alamat penerima, kami menggunakan minimal 10 karakter untuk menghindari Bad Address pickup
    public int $destination_kecamatan_id;                            //	int	false	Kecamatan penerima
    public string|int $destination_zipcode;
    public int $weight = 1;                                          //	int(min:1)	false	Berat paket dalam gram
    public int $width = 1;                                           //	int(min:1)	false	cm
    public int $length = 1;                                          //	int(min:1)	false	cm
    public int $qty = 1;                                             //	int	true	Jumlah barang dalam paket, akan terisi 1 jika kosong
    public int $height = 1;                                          //	int(min:1)	false	cm
    public int $item_value = 0;                                      //	int	false	Nilai barang secara keseluruhan
    public int $shipping_cost = 0;                                   //	int	false	Biaya pengiriman, see # Shipping Price section
    public string $service = '';                                     //	string	false	Lihat shipping price untuk ini
    public ?int $insurance_amount = 0;                               //	string	true	Lihat Syarat & Ketentuan
    public string $service_type = '';                                //	string	false	The service type, like EZ, REG, CTC, OKE, etc (see shipping price section)
    public int $cod = 0;                                             //	int	false	COD PRICE NB : Isi 0 untuk paket non COD
    public int $package_type_id = 1;                                 //	int	false	Tipe paket tersedia untuk sementara 1
    public string $item_name = '';                                   //	string(max:255)	false	Isi paket
    public ?bool $drop = false;                                      //	bool	true	DROP-OFF / CASHLESS
    public string $note = '';                                     //	string(max:50)	true	Special instructions
}
