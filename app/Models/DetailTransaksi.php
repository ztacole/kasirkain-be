<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'id_transaksi',
        'id_varian_produk',
        'jumlah',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function varianProduk()
    {
        return $this->belongsTo(VarianProduk::class, 'id_varian_produk');
    }
}