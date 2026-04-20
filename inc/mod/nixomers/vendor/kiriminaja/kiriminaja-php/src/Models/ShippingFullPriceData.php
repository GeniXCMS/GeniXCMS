<?php

namespace KiriminAja\Models;

use KiriminAja\Base\ModelBase;

class ShippingFullPriceData extends ModelBase {
    // int	false	ID dari kecamatan_id pengirim
    public int $origin;
    // int	false	ID dari kecamatan_id customer
    public int $destination;
    // int	false	Akumulasi berat paket dalam gram (berat paket aktual). Jika berat dimensi lebih besar dari berat aktual paket maka yang dikirimkan adalah berat dimensi
    public int $weight;
}
